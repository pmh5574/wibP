<?php
namespace Controller\Admin\Wecom;



class GoodsListController extends \Controller\Admin\Controller 
{
    public function index()
    {
        $this->callMenu('partner', 'goods', 'list');
    }
}