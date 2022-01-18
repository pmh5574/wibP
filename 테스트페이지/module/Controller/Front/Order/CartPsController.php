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

use Request;
/**
 * 장바구니 처리 페이지
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class CartPsController extends \Bundle\Controller\Front\Order\CartPsController
{
    public function index()
    {
        $postValue = Request::request()->toArray();
//        print_r($postValue);exit;
        parent::index();
        
    }
}