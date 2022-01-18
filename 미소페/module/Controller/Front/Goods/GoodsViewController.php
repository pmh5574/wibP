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

use Component\Coupon\Coupon;
use Request;
use Session;
use Component\Wib\WibSql;

class GoodsViewController extends \Bundle\Controller\Front\Goods\GoodsViewController
{
    public function index() 
    {
        parent::index();

        $wib = new WibSql();
        $coupon = new Coupon();

        $memberNo = Session::get('member.memNo');

        $goodsView = $this->getData('goodsView');        

        //아이디마다 쿠폰있는거 뿌려주기
        if ($memberNo) {//회원일시 보유하고있는 주문쿠폰
            //사용자 쿠폰보유하고 있는 것 중에 쓸수있는 쿠폰 모두 보여줌
            $memberData = $coupon->getOrderMemberCouponList($memberNo);

            $this->setData("memberData", $memberData['order']);
        } else {//비회원이면 회원 가입시 주는 쿠폰 보여주기
            $noMemberQuery = "SELECT * FROM es_coupon WHERE couponEventType = 'join' AND couponType = 'y'";
            $noMemberData = $wib->WibAll($noMemberQuery);

            $this->setData("noMemberData", $noMemberData);
        }

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
