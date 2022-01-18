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
namespace Controller\Admin\Order;

use Request;
use Vendor\Spreadsheet\Excel\Reader as SpreadsheetExcelReader;

/**
 * 주문통합리스트
 *
 * @package Bundle\Controller\Admin\Order
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 */
class OrderListAllController extends \Bundle\Controller\Admin\Order\OrderListAllController
{
    public $ex_eng;
    public $ex_title;

	public function index()
	{
            $files = Request::files()->get('orderExcel')['tmp_name'];
            
            $excelFile = new SpreadsheetExcelReader();
            $excelFile->setOutputEncoding('CP949');
            $excelFile->read(Request::files()->get('orderExcel')['tmp_name']);
            // 엑셀업로드용 추가개발
            if ($files) {
                $db = \App::load('DB');
                $this->excelArraySet();
                //echo $excelFile->sheets[0]['numRows'].'<br>';
                for ($i = 2; $i <= $excelFile->sheets[0]['numRows']; $i++) {
                    //print_r( iconv('EUC-KR', 'UTF-8', gd_isset($excelFile->sheets[0]['cells'][$i][4])) );
                    $num = 1;
                    $in_sql = "INSERT INTO es_order SET ";
                    $arrBind = [];
                    foreach ($this->ex_title as $value) {
                        $data = iconv('EUC-KR', 'UTF-8', gd_isset($excelFile->sheets[0]['cells'][$i][$num]));
                        
                        
                        //$inlog = "INSERT INTO es_order SET memNo = ?, couponNo = ?, prdName = ?, eventNum = ?, useDt = ?";
                        if($value[1] && $value[1] != ''){
                            $in_sql .= $value[1].'= ? ,';
                            if( $value[1] == 'memNo'){
                                $db->bind_param_push($arrBind, 'i', 23);
                            }else{
                                $db->bind_param_push($arrBind, 's', $data);
                            }
                            $in_sql_txt .= $value[0].',';
                        }
                        
                        $num++;
                        
                    }
                    
                    //echo '<br>';
                    $indata = substr($in_sql, 0, -2);
                    echo print_r($arrBind).'<br>';
                    echo $indata.'<br>';
                    echo $in_sql_txt.'<br>';
                    //$db->bind_query($indata, $arrBind);
                }
            }
            
            
            
		try {
			// 부모클래스 상속
			parent::index();

			// 추가기능 소스 삽입
			$addSource = "추가기능";
			$this->setData("addSource", $addSource);
		} catch (\Exception $e) {
			throw $e;
		}
	}
        
        public function excelArraySet()
        {
            $this->ex_eng = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
            $this->ex_title = array(
                array('주문번호', 'orderNo'), array('주문일자', 'regDt'), array('주문 타입', 'orderTypeFl'), array('회원아이디', 'memNo'),
                array('주문자전화번호', ''), array('주문자핸드폰', ''), array('주문자 우편번호', ''), array('주문자 구역 번호', ''), array('주문자 주소', ''), 
                array('주문자 상세주소', ''), 
                array('받는분이름', ''), array('받는분전화번호', ''), array('받는분핸드폰', ''), array('받는분 우편번호', ''), array('받는분 구역번호', ''), 
                array('받는분 주소', ''), array('받는분 상세 주소', ''), array('결제수단', 'settleKind'), 
                array('상품, 배송비 총 합계 금액', 'settlePrice'), array('상품 합계금액', 'totalGoodsPrice'), array('배송비', 'totalDeliveryCharge'), array('주문상태', 'orderStatus'), 

                array('주문자 이메일', ''), array('배송메세지', ''), array('배송업체', ''), array('송장번호', ''), array('착불여부', ''), array('결제금액', ''), 
                array('사용 적립금', ''), array('적립된 적립금', 'totalMileage'), array('쿠폰 금액', 'totalCouponGoodsDcPrice'), array('회원레벨 할인', ''), array('은행계좌', ''), 
                array('입금자명', ''), array('결제일', ''), 
                array('배송일', ''), array('배송완료일', ''), array('결재확인일', ''), array('주문자IP', 'orderIp'), array('에스크로 번호', ''), array('결제PG사명', ''), 
                array('pg사 거래번호', ''), array('PG 승인번호', ''), array('PG 승인카드사 코드', ''), array('승인일자', ''), array('PG 취소여부', ''), array('취소일', ''), array('관리자 메모', '')
            );
        }
}