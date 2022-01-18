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

use Component\Wib\WibSql;
/**
 * 주문통합리스트
 *
 * @package Bundle\Controller\Admin\Order
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 */
class OrderListAllController extends \Bundle\Controller\Admin\Order\OrderListAllController
{
    public function index()
    {
        parent::index();
        
        $wib = new WibSql();
        
        $orderGridConfigList = $this->getData('orderGridConfigList');
        $data = $this->getData('data');
        
        foreach($orderGridConfigList as $gridKey => $gridName){
            $orderGridConfigList['nmCompanyNm'] = '회사명';
        }
        
        foreach ($data as $orderNo => $orderData) {
            foreach ($orderData['goods'] as $sKey => $sVal) {
                foreach ($sVal as $dKey => $dVal) {
                    foreach ($dVal as $key => $val) {
                        
                        $strSQL = "SELECT nmCompanyNm FROM es_order WHERE orderNo = {$val['orderNo']}";
                        $result = $wib->WibNobind($strSQL);
                        
                        $data[$orderNo]['goods'][$sKey][$dKey][$key]['nmCompanyNm'] = $result['nmCompanyNm'];


                    }
                }
            }
        }
        
        $this->setData('data',$data);
        $this->setData('orderGridConfigList', $orderGridConfigList);
        
    }
}