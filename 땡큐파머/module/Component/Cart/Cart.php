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
namespace Component\Cart;

use App;

/**
 * 장바구니 class
 *
 * 상품과 추가상품을 분리하는 작업에서 추가상품을 기존과 동일하게 상품에 종속시켜놓은 이유는
 * 상품과 같이 배송비 및 다양한 조건들을 아직은 추가상품에 설정할 수 없어서
 * 해당 상품으로 부터 할인/적립등의 조건을 상속받아서 사용하기 때문이다.
 * 따라서 추후 추가상품쪽에 상품과 동일한 혜택과 기능이 추가되면
 * 장바구니 테이블에서 상품이 별도로 담길 수 있도록 개발되어져야 한다.
 *
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class Cart extends \Bundle\Component\Cart\Cart
{
	public function getCartGoodsData($cartIdx = null, $address = null, $tmpOrderNo = null, $isAddGoodsDivision = false, $isCouponCheck = false, $postValue = [], $setGoodsCnt = [], $setAddGoodsCnt = [], $setDeliveryMethodFl = [], $setDeliveryCollectFl = [], $deliveryBasicInfoFl = false)
    {
		$getCart = parent::getCartGoodsData($cartIdx, $address, $tmpOrderNo, $isAddGoodsDivision, $isCouponCheck, $postValue, $setGoodsCnt, $setAddGoodsCnt, $setDeliveryMethodFl, $setDeliveryCollectFl, $deliveryBasicInfoFl);
		
		/* 웹앤모바일 튜닝 - 2019-11-04, 링크 프라이스 쿠키정보 체크 lpinfo */
		$linkPrice = App::load(\Component\LinkPrice\LinkPrice::class);
		$lpinfo = $linkPrice->getLpInfo();
		if ($lpinfo) {
			foreach ($getCart as $keys => $values) {
				foreach ($values as $key => $value) {
					foreach ($value as $k => $v) {
						$v['lpinfo'] = $lpinfo;
						$getCart[$keys][$key][$k] = $v;
					} // endforeach 
				} // endforeach 
			} // endforeach 
		} // endif 
		/* 튜닝 END */
		
		return $getCart;
	}
}