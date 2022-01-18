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

/**
 * 주문 쿠폰 적용
 *
 * @author  su
 */
class LayerCouponApplyOrderController extends \Bundle\Controller\Front\Order\LayerCouponApplyOrderController
{
    public function index()
    {
        parent::index();
        //$cartCouponNoArr = $this->getData('memberCouponArrData');
        //print_r($cartCouponNoArr);
    }
    
}