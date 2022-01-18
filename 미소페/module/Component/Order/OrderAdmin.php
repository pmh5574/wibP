<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link http://www.godo.co.kr
 */
namespace Component\Order;

use App;
use Component\Bankda\BankdaOrder;
use Component\Database\DBTableField;
use Component\Delivery\Delivery;
use Component\Deposit\Deposit;
use Component\Godo\MyGodoSmsServerApi;
use Component\Godo\NaverPayAPI;
use Component\Mail\MailMimeAuto;
use Component\Mall\Mall;
use Component\Member\Manager;
use Component\Mileage\Mileage;
use Component\Naver\NaverPay;
use Component\Sms\Code;
use Component\Validator\Validator;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use Framework\Debug\Exception\Except;
use Framework\Debug\Exception\LayerNotReloadException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\StaticProxy\Proxy\UserFilePath;
use Framework\Utility\ArrayUtils;
use Framework\Utility\DateTimeUtils;
use Framework\Utility\NumberUtils;
use Framework\Utility\StringUtils;
use Globals;
use LogHandler;
use Request;
use Session;
use Vendor\Spreadsheet\Excel\Reader as SpreadsheetExcelReader;
use Component\Page\Page;
use Component\Wib\WibSql;
/**
 * 주문 class
 * 주문 관련 관리자 Class
 *
 * @package Bundle\Component\Order
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 */
class OrderAdmin extends \Bundle\Component\Order\OrderAdmin
{
    public function requestUserHandle($orderNo, $orderGoodsNo, $handleMode, $bundleData)
    {
        $goodsData = $this->getOrderGoods($orderNo, null, null, null, null, ['memNo']);
        $filteredGoodsData = [];
       
        // 배열이 아닌 경우 배열로 만듬
        if (!is_array($orderGoodsNo)) {
            $orderGoodsNo = [$orderGoodsNo];
        }

        // 원 주문상품 리스트와 비교
        foreach ($goodsData as $key => $val) {
            // 이마트 보안취약점 요청사항 (사용자 환불신청시 회원 유효성 검증)
            if ($handleMode == 'r') {
                if ($val['memNo'] != Session::get('member.memNo')) {
                    return 'invalid_order';
                }
            }

            if (in_array($val['sno'], $orderGoodsNo)) {
                $filteredGoodsData[] = $val;
            }
        }

        // 사용자 환불신청 테이블 저장
        $bundleData['orderNo'] = $orderNo;
        $bundleData['userHandleMode'] = $handleMode;
        $goodsCnt = $bundleData['userHandleGoodsCnt'];
        if (empty($bundleData['userRefundAccountNumber']) === false) {
            $bundleData['userRefundAccountNumber'] = \Encryptor::encrypt($bundleData['userRefundAccountNumber']);
        }
        
        foreach ($filteredGoodsData as $orderGoods) {

            // 처리할 상품별 번호 및 수량
            $bundleData['userHandleGoodsNo'] = $orderGoods['sno'];
            $bundleData['userHandleGoodsCnt'] = $goodsCnt[$orderGoods['sno']];

            // 중복 등록 방지
            $query = "SELECT count(*) as cnt  FROM " . DB_ORDER_USER_HANDLE . " WHERE regDt >= (now()-INTERVAL 30 SECOND) AND userHandleReason = ? AND userHandleDetailReason = ? AND orderNo = ? AND userHandleMode = ? AND userHandleGoodsNo = ? AND userHandleGoodsCnt = ?";
            $this->db->bind_param_push($arrBind, 's', $bundleData['userHandleReason']);
            $this->db->bind_param_push($arrBind, 's', $bundleData['userHandleDetailReason']);
            $this->db->bind_param_push($arrBind, 'i', $bundleData['orderNo']);
            $this->db->bind_param_push($arrBind, 's', $bundleData['userHandleMode']);
            $this->db->bind_param_push($arrBind, 'i', $bundleData['userHandleGoodsNo']);
            $this->db->bind_param_push($arrBind, 'i', $bundleData['userHandleGoodsCnt']);
            $result = $this->db->query_fetch($query, $arrBind, false);

            if($result['cnt']>0) {
                throw new AlertRedirectException(__('등록중 입니다. 잠시만 기다려 주세요'), null, null, 'order_list.php', 'parent');
            }

            // handle 테이블에 입력후 insertId 반환
            if (method_exists($this, 'insertOrderUserHandle') === true) {
                $userHandleSno[$orderGoods['sno']] = $this->insertOrderUserHandle($bundleData);
            } else {
                $compareField = array_keys($bundleData);
                $arrBind = $this->db->get_binding(DBTableField::tableOrderUserHandle(), $bundleData, 'insert', $compareField);
                $this->db->set_insert_db(DB_ORDER_USER_HANDLE, $arrBind['param'], $arrBind['bind'], 'y');
                $userHandleSno[$orderGoods['sno']] = $this->db->insert_id();
                unset($arrBind);
            }
            


            // 주문 상품테이블에 handleSno 번호를 업데이트 한다.
            $setData['userHandleSno'] = $this->db->insert_id();
            $compareField = array_keys($setData);
            $arrBind = $this->db->get_binding(DBTableField::tableOrderGoods(), $setData, 'update', $compareField);
            $arrWhere = 'orderNo = ? AND sno = ?';
            $this->db->bind_param_push($arrBind['bind'], 's', $orderNo);
            $this->db->bind_param_push($arrBind['bind'], 'i', $orderGoods['sno']);
            $this->db->set_update_db(DB_ORDER_GOODS, $arrBind['param'], $arrWhere, $arrBind['bind']);
            unset($arrBind);

            // deliveryRefundType 추가 
            if($bundleData['deliveryRefundType']){
                
                $wib = new WibSql();
                
                $data = [
                    'es_orderUserHandle',
                    array('deliveryRefundType' => [$bundleData['deliveryRefundType'],'s']),
                    array('sno'                => [$userHandleSno[$orderGoods['sno']],'i'])
                ];
                
                $wib->WibUpdate($data);
                
            }
        }

        if ($this->paycoConfig['paycoFl'] == 'y') {
            // 페이코쇼핑 결제데이터 전달
            $payco = \App::load('\\Component\\Payment\\Payco\\Payco');
            $payco->paycoShoppingRequest(Request::post()->get('orderNo'));
        }

        return $userHandleSno;
    }
}