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
        // 장바구니 class
        $cart = \App::Load(\Component\Cart\Cart::class);
        $postValue = Request::request()->toArray();

        if($postValue['mode'] == 'packDelete'){
            $cart->setCartDelete($postValue['sno']);
            if (Request::isAjax()) {
                $this->json([
                    'error' => 0,
                    'message' => __('성공'),
                ]);
            }
            exit;
        }
        
        if($postValue['mode'] == 'packUpdate'){
            $db = \App::load(\DB::class);
            $arrBind = [];
            $strSQL = "UPDATE es_cart SET goodsCnt = ? WHERE sno = ?";
            $db->bind_param_push($arrBind, 'i', $postValue['goodsCnt']);
            $db->bind_param_push($arrBind, 'i', $postValue['sno']);
            $db->bind_query($strSQL, $arrBind);
            
            if (Request::isAjax()) {
                $this->json([
                    'error' => 0,
                    'message' => __('성공'),
                ]);
            }
            exit;
        }
        
        parent::index();
        
    }
}