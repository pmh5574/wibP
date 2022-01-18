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
namespace Controller\Front\Goods;

use Request;
use Controller\Front\Order\PackCartListController as cart;

class GoodsListController extends \Bundle\Controller\Front\Goods\GoodsListController
{
    public function index()
    {
        parent::index();
        
        $cateCd = Request::get()->get('cateCd');
        $server = Request::server()->toArray();
        if(substr($cateCd, 0, 3) == '023'){
                
            $cart = new cart();
            $list = $cart->getCartList();

            $this->setData('packcart', $list);

            $this->getView()->setDefine("tpl", "goods/goods_list_pack.html");
            $this->getView()->setDefine('goodsTemplate', 'goods/list/list_pack.html');

        }
    }
}