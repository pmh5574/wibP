<?php
namespace Controller\Front\Frame;

use Request;

class PopupController extends \Bundle\Controller\Front\Goods\GoodsViewController
{
    public function index()
    {
        
        $goodsNo = Request::post()->get('goodsNo');
        Request::get()->set('goodsNo', $goodsNo);
        
        parent::index();

        // 상품 상세정보
        $goodsView = $this->getData("goodsView");
        //print_r($goodsView);
        $this->setData('goodsView', $goodsView);
        
        $page = Request::post()->get('page');
        
        $this->getView()->setDefine("tpl", "frame/".$page.".html");
    }
}
