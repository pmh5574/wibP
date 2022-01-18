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
use Component\Wib\WibSql;
/**
 * 교환 접수 리스트 페이지
 * [관리자 모드] 교환 접수 리스트 페이지
 *
 * @package Bundle\Controller\Admin\Order
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 */
class OrderListUserExchangeController extends \Bundle\Controller\Admin\Order\OrderListUserExchangeController
{
    public function index()
    {
        parent::index();

        $wib = new WibSql();

        $orderGridConfigList = $this->getData('orderGridConfigList');

        $orderGridConfigList['userHandleDetailReason'] = '상세사유';
        $orderGridConfigList['deliveryRefundType']     = '택배지불 방법';
        
        $this->setData('orderGridConfigList',$orderGridConfigList);

        $data = $this->getData('data');

        foreach ($data as $aKey => $aVal){
            foreach($aVal['goods'] as $bKey => $bVal){
                foreach($bVal as $cKey => $cVal){
                    foreach($cVal as $key => $val){
                        
                        $strSQL = "SELECT * FROM es_orderUserHandle WHERE sno = ".$val['userHandleSno'];
                        $result = $wib->WibNobind($strSQL);

                        switch($result['deliveryRefundType']){
                            case 'b':
                                $result['deliveryRefundType'] = '상자동봉';
                                
                                break;
                            case 'd':
                                $result['deliveryRefundType'] = '입금';
                                
                                break;
                            default:
                                $result['deliveryRefundType'] = '';
                                break;
                        }
                   
                        

                        $data[$aKey]['goods'][$bKey][$cKey][$key]['deliveryRefundType'] = $result['deliveryRefundType'];
                    }
                }
            }
        }
        

        $this->setData('data',$data);

    }
}