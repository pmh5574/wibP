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
 * @link      http://www.godo.co.kr
 */
namespace Component\Order;

/**
 * 주문 class
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class Order extends \Bundle\Component\Order\Order
{
    public function saveOrderInfo($cartInfo, $orderInfo, $orderPrice)
    {
        
        // 직접입력 쿠폰 사용시 사용한 쿠폰정리
        if($orderPrice['totalEnuriDcPrice'] && $orderInfo['insertCode'] && $orderInfo['codePsno']){
            $updateCodeCouponSql = "update es_couponPaper set type = 'y' where code = '".$orderInfo['insertCode']."' and psno = '".$orderInfo['codePsno']."' ";
            $result = $this->db->query($updateCodeCouponSql);
            $this->db->fetch($result);
        }
        
        //print_r($orderInfo);
        //exit;
        
        return parent::saveOrderInfo($cartInfo, $orderInfo, $orderPrice, $checkSumData = true);
    }
}