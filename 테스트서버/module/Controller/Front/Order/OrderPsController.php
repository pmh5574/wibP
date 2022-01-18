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
namespace Controller\Front\Order;

use App;
use Request;
use Component\Order\Order;
use Component\Cart\Cart;

/**
 * 주문완료 처리
 *
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class OrderPsController extends \Bundle\Controller\Front\Order\OrderPsController
{
    public function index()
    {
        parent::index();
        
        //$cart = App::load(\Component\Cart\Cart::class);
        
        //$checker = Request::post()->toArray();
        
        //$order = App::load(\Component\Order\Order::class);
        //$postValue = $order->setOrderDataValidation($checker, true);
        
        
        //$address = $checker['receiverCountryCode'];
        //$cart = App::load(\Component\Cart\Cart::class);
        //$cartInfo = $cart->getCartGoodsData($checker['cartSno'], $address, null, true, false, $checker);
        //$cart->totalSettlePrice = $checker['settlePrice'];
        //$orderPrice = $cart->setOrderSettleCalculation($checker);
        //echo $checker['couponApplyOrderNo'].'<br>';
        
        //print_r($orderPrice);
    }
}