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
namespace Controller\Mobile\Goods;

use Component\Coupon\Coupon;
use Request;
use Session;
use Component\Wib\WibSql;

class GoodsViewController extends \Bundle\Controller\Mobile\Goods\GoodsViewController
{
    public function index() 
    {
        parent::index();

        $wib = new WibSql();
        $coupon = new Coupon();

        $memberNo = Session::get('member.memNo');

        $goodsView = $this->getData('goodsView');


        

        //상품 할인율 on/off
        $goodsNo = $goodsView['goodsNo'];

        $data = [
            'es_goods',
            'goodsDiscountPer',
            array('goodsNo' => [$goodsNo, 'i'])
        ];

        $result = $wib->WibQuery($data);

        $goodsView['goodsDiscountPer'] = $result['goodsDiscountPer'];

        $this->setData("goodsView", $goodsView);
        
    }
}