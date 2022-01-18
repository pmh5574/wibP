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
    $db = \App::load('DB');
    $orderGridConfigList = $this->getData('orderGridConfigList');
    $data = $this->getData('data');
    foreach ($data as $aKey => $aVal){
        foreach($aVal['goods'] as $bKey => $bVal){
            foreach($bVal as $cKey => $cVal){
                foreach($cVal as $key => $val){
                    
                    $strSQL = "SELECT * FROM es_orderUserHandle WHERE sno = ".$val['userHandleSno'];
                    $result = $db->query_fetch($strSQL);
                    
                    if($result[0]['deliveryRefundType'] === 'y') $result[0]['deliveryRefundType'] = '환불금차감';
                    if($result[0]['deliveryRefundType'] === 'b') $result[0]['deliveryRefundType'] = '상자동봉';
                    if($result[0]['deliveryRefundType'] === 'd') $result[0]['deliveryRefundType'] = '입금';
                    
                    $data[$aKey]['goods'][$bKey][$cKey][$key]['deliveryRefundType'] = $result[0]['deliveryRefundType'];
                }
            }
        }
    }
    
    $orderGridConfigList['userHandleDetailReason'] = '상세사유';
    $orderGridConfigList['deliveryRefundType'] = '택배지불 방법';
    $this->setData('data',$data);
    $this->setData('orderGridConfigList',$orderGridConfigList);
  }
}
