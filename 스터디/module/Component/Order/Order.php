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

use Request;
use Component\Wib\WibSql;

/**
 * 주문 class
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class Order extends \Bundle\Component\Order\Order
{
    public function saveOrder($cartInfo, $orderInfo, $order, $isWrite = false)
    {
        parent::saveOrder($cartInfo, $orderInfo, $order, $isWrite);
        
        echo '<!--';
        print_r($orderInfo);
        echo '-->';
        //exit;
        $orderNo = $this->orderNo;
        $conn = new WibSql();
        $sql = "update es_order set testCode = '{$orderInfo['testMemo']}' where orderNo = '{$orderNo}'";
        $conn->WibNobind($sql);
        
    }
}