<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Front\Goods;

use Request;

class WibLayerCouponApplyController extends \Bundle\Controller\Front\Goods\LayerCouponApplyController
{
    public function index()
    {
        parent::index();
        
        $goodsNoKey = Request::post()->get('goodsNoKey');
        
        $this->setData('goodsNoKey', $goodsNoKey);
    }
}