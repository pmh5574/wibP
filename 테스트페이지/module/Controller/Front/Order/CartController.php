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
 * 장바구니
 *
 * @author Ahn Jong-tae <qnibus@godo.co.kr>
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class CartController extends \Bundle\Controller\Front\Order\CartController
{
    public function index()
    {
        parent::index();
        $cartInfo = $this->getData('cartInfo');
        
        // try {
        //     // 부모 클래스 상속
        //     parent::index();

        //     $displayBox = '박스를 출력';
        //     $this->setData('displayBox', $displayBox);

        // } catch (\Exception $e) {
        //     throw $e;
        // }

//        $totalScmGoodsDeliveryCharge = $this->getData('totalScmGoodsDeliveryCharge'); // 상품 배송정책별 총 배송 금액
//        print_r($cartInfo);
    }
}