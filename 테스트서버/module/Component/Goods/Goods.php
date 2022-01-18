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
namespace Component\Goods;

use Component\PlusShop\PlusReview\PlusReviewConfig;
use Component\Database\DBTableField;
use Component\ExchangeRate\ExchangeRate;
use Component\Member\Group\Util;
use Component\Validator\Validator;
use Cookie;
use Exception;
use Framework\Utility\ArrayUtils;
use Framework\Utility\SkinUtils;
use Framework\Utility\StringUtils;
use Globals;
use Request;
use Session;
use UserFilePath;
use Framework\Utility\DateTimeUtils;
/**
 * 상품 class
 */
class Goods extends \Bundle\Component\Goods\Goods
{
    public function getGoodsView($goodsNo)
    {
        $getData = parent::getGoodsView($goodsNo);

        if ($getData['optionFl'] === 'y') {
            if($getData['option']) {
                if ($getData['optionDisplayFl'] == 'c') {
                    // 옵션명
                    $getData['optionName'] = explode(STR_DIVISION, $getData['optionName']);
                    $getData['optionGubun'] = explode(STR_DIVISION, $getData['wbOptionGubun']);

                    $getData['option_tmp'] = gd_htmlspecialchars($this->getGoodsOption($goodsNo, $getData));
                    // 첫번째 옵션 값
                    $getData['optionDivision'] = $getData['option_tmp']['optVal'][1];

                    unset($getData['option_tmp']);
                }
            }
        }


        return $getData;
    }
    public function setGoodsListField()
    {
        $this->goodsListField = 'g.plusReviewCnt,g.goodsNo, g.cateCd, g.scmNo, g.brandCd, g.goodsNmFl, g.goodsNmMain, g.goodsNmList, g.goodsNm, g.mileageFl, g.goodsPriceString, g.optionName, \'\' as optionValue,g.optionFl,g.minOrderCnt , g.stockFl,g.goodsModelNo,g.onlyAdultFl,g.onlyAdultImageFl,g.orderCnt,
            g.makerNm, g.shortDescription, g.imageStorage, g.imagePath,g.goodsCd,g.soldOutFl,
            ( if (g.soldOutFl = \'y\' , \'y\', if (g.stockFl = \'y\' AND g.totalStock <= 0, \'y\', \'n\') ) ) as soldOut,
            ( if (g.' . $this->goodsDisplayFl . ' = \'y\' , if (g.' . $this->goodsSellFl . ' = \'y\', g.' . $this->goodsSellFl . ', \'n\') , \'n\' ) ) as orderPossible,
            g.goodsPrice, g.fixedPrice, g.mileageGoods, g.mileageGoodsUnit, g.hitCnt, g.goodsDiscountFl, g.goodsDiscount, g.goodsDiscountUnit, g.goodsPermission, g.goodsPermissionGroup, g.goodsPermissionPriceStringFl, g.goodsPermissionPriceString, g.mileageGroup, g.mileageGroupInfo, g.mileageGroupMemberInfo, g.fixedGoodsDiscount, g.goodsDiscountGroup, g.goodsDiscountGroupMemberInfo, g.exceptBenefit, g.exceptBenefitGroup, g.exceptBenefitGroupInfo, g.salesUnit, g.fixedSales, g.fixedOrderCnt,
            g.goodsBenefitSetFl,g.benefitUseType,g.newGoodsRegFl,g.newGoodsDate,g.newGoodsDateFl,g.periodDiscountStart,g.periodDiscountEnd,g.regDt,g.modDt, g.goodsColor
            ';
        // ( if (g.stockFl = \'y\' , if (g.totalStock = 0, \'y\', \'n\') , if (g.soldOutFl = \'y\', \'y\', \'n\') ) ) as soldOut,
    }
    
    public function setSearchGoodsList($getValue = null, $searchTerms = null)
    {
        parent::setSearchGoodsList($getValue = null, $searchTerms = null);
        
        $keywords = Request::get()->get('keyword');
        
        // 변형할 WHERE절
        $addWhere = "(    (REPLACE(g.goodsNm, ' ', '') LIKE concat('%',?,'%') OR g.goodsNm LIKE concat('%',?,'%')) or  (REPLACE(g.goodsCd, ' ', '') LIKE concat('%',?,'%') OR g.goodsCd LIKE concat('%',?,'%'))  )"; 
        // 고도몰 기본 WHERE절
        $oriWhere = "((REPLACE(g.goodsNm, ' ', '') LIKE concat('%',?,'%') OR g.goodsNm LIKE concat('%',?,'%')))";
        
        // 기존 고도몰 쿼리를 변경후 저장
        $changeWhere = $this->arrWhere[0];
        $changeWhere = str_replace($oriWhere, $addWhere, $changeWhere);
        $this->arrWhere[0] = $addWhere;
        
        // 검색 파라미터 바인딩
        $this->db->bind_param_push($this->arrBind,'s',$keywords);
        $this->db->bind_param_push($this->arrBind,'s',$keywords);
    }
}