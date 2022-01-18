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

use Component\Goods\Goods;
use Component\Naver\NaverPay;
use Component\Payment\Payco\Payco;
use Component\GoodsStatistics\GoodsStatistics;
use Component\Policy\Policy;
use Component\Database\DBTableField;
use Component\Delivery\EmsRate;
use Component\Delivery\OverseasDelivery;
use Component\Mall\Mall;
use Component\Mall\MallDAO;
use Component\Member\Util\MemberUtil;
use Component\Member\Group\Util;
use Component\Validator\Validator;
use Cookie;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Debug\Exception\AlertRedirectCloseException;
use Framework\Debug\Exception\Except;
use Framework\Utility\ArrayUtils;
use Framework\Utility\NumberUtils;
use Framework\Utility\SkinUtils;
use Framework\Utility\StringUtils;
use Globals;
use Request;
use Session;
use Component\Godo\GodoCenterServerApi;
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
        $cateInfo = parent::getCartGoodsData($cartIdx, $address, $tmpOrderNo, $isAddGoodsDivision, $isCouponCheck, $postValue, $setGoodsCnt, $setAddGoodsCnt, $setDeliveryMethodFl, $setDeliveryCollectFl, $deliveryBasicInfoFl);

        foreach($cateInfo as $key1 => &$val1) {
            foreach($val1 as $key2 => &$val2) {
                foreach($val2 as $key3 => &$val3) {
                    if(is_array($val3['option'])) {
                        foreach($val3['option'] as $key4 => &$val4) {
                            if(strpos($val4['optionValue'], '#') !== false) {
                                $val4['optionValue'] = '<div style="width:15px; height:15px; background:'. $val4['optionValue'] . '; display:inline-block; vertical-align:middle;"></div>';
                            }
                        }
                    }
                }
            }
        }

      
        return $cateInfo;
    }
    
    public function setOrderSettleCalculation($requestData)
    {
        if($requestData['codePrice'] > 0){
            $this->totalSettlePrice = $requestData['settlePrice'];
            
            //$requestData['couponApplyOrderNo'] = $requestData['codePsno'];
            //$requestData['totalCouponOrderDcPrice'] = $requestData['codePrice'];
            //$requestData['settlePrice'] = $requestData['settlePrice'] - $requestData['codePrice'];
            //$requestData['totalEnuriDcPrice'] = $requestData['codePrice'];
        }
        
        $wibprice = parent::setOrderSettleCalculation($requestData);
        $wibprice['totalEnuriDcPrice'] = $requestData['codePrice'];
        
        return $wibprice;
    }
    
}