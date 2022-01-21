<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Admin\Goods;

class GoodsScrollListController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('goods', 'displayConfig', 'scrollImg');
    }
}
