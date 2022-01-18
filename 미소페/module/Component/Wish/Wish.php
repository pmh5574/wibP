<?php

/**
 * 상품 보관함 class
 *
 * @author    artherot
 * @version   1.0
 * @since     1.0
 * @copyright ⓒ 2016, NHN godo: Corp.
 */
namespace Component\Wish;

use Component\Cart\CartAdmin;
use Component\Goods\Goods;
use Component\GoodsStatistics\GoodsStatistics;
use Component\Member\Util\MemberUtil;
use Component\Database\DBTableField;
use Framework\Utility\ArrayUtils;
use Framework\Utility\SkinUtils;
use Component\Validator\Validator;
use Component\Mall\Mall;
use Session;
use Exception;
use App;

class Wish extends \Bundle\Component\Wish\Wish
{
    /**
     * 상품 보관함 상품 정보
     * 현재 상품 보관함에 담긴 상품의 정보를 출력합니다.
     *
     * @param  array $memInfo   회원 정보 (관리자>회원CRM에서만 사용)
     *
     * @return array 상품 보관함 상품 정보
     */
    public function getWishGoodsData($memInfo = null)
    {
        if($memInfo) {
            $memNo = $memInfo['memNo'];
            $groupSno = $memInfo['groupSno'];
            $cart = new CartAdmin($memNo, true);
        } else {
            $memNo = Session::get('member.memNo');
            $groupSno = Session::get('member.groupSno');
            $cart = \App::load('\\Component\\Cart\\Cart');
            $mallBySession = SESSION::get(SESSION_GLOBAL_MALL);
        }

        // 회원 로그인 체크
        if (gd_is_login() === true || $memInfo) {
            $arrWhere[] = 'w.memNo = \'' . $memNo . '\'';
        } else {
            MemberUtil::logoutGuest();
            $moveUrl = URI_HOME . 'member/login.php?returnUrl=' . urlencode(Request::getReturnUrl());
            throw new AlertRedirectException(null, null, null, $moveUrl);
        }

        if($mallBySession) {
            $arrWhere[] = 'w.mallSno = \'' . $mallBySession['sno'] . '\'';
        }

        $globalWhere = implode(' AND ', $arrWhere);

        //접근권한 체크
        if (gd_check_login() || $memInfo) {
            $arrWhere[] = '(g.goodsAccess !=\'group\'  OR (g.goodsAccess=\'group\' AND FIND_IN_SET(\''.$groupSno.'\', REPLACE(g.goodsAccessGroup,"'.INT_DIVISION.'",","))) OR (g.goodsAccess=\'group\' AND !FIND_IN_SET(\''.$groupSno.'\', REPLACE(g.goodsAccessGroup,"'.INT_DIVISION.'",",")) AND g.goodsAccessDisplayFl =\'y\'))';
        } else {
            $arrWhere[] = '(g.goodsAccess=\'all\' OR (g.goodsAccess !=\'all\' AND g.goodsAccessDisplayFl =\'y\'))';
        }

        //성인인증안된경우 노출체크 상품은 노출함
        if (gd_check_adult() === false) {
            $arrWhere[] = '(onlyAdultFl = \'n\' OR (onlyAdultFl = \'y\' AND onlyAdultDisplayFl = \'y\'))';
        }

        $imageSize = SkinUtils::getGoodsImageSize('list');

        // 세로사이즈고정 체크
        $imageConf = gd_policy('goods.image');
        if ($imageConf['imageType'] != 'fixed') {
            $imageSize['hsize1'] = '';
        }

        // 정렬 방식
        $strOrder = 'w.sno DESC';

        // 장바구니 디비 및 상품 디비의 설정 (필드값 설정)
        $getData = [];

        $arrExclude['wish'] = [];
        $arrExclude['option'] = [
            'goodsNo',
            'optionNo',
        ];
        $arrExclude['addOptionName'] = [
            'goodsNo',
            'optionCd',
            'mustFl',
        ];
        $arrExclude['addOptionValue'] = [
            'goodsNo',
            'optionCd',
        ];
        $arrInclude['goods'] = [
            'goodsNm',
            'scmNo',
            'goodsCd',
            'cateCd',
            'goodsOpenDt',
            'goodsState',
            'imageStorage',
            'imagePath',
            'brandCd',
            'makerNm',
            'originNm',
            'goodsModelNo',
            'goodsPermission',
            'goodsPermissionGroup',
            'goodsPermissionPriceStringFl',
            'goodsPermissionPriceString',
            'onlyAdultFl',
            'goodsAccess',
            'goodsAccessGroup',
            'taxFreeFl',
            'taxPercent',
            'goodsWeight',
            'totalStock',
            'stockFl',
            'soldOutFl',
            'fixedSales',
            'fixedOrderCnt',
            'salesUnit',
            'minOrderCnt',
            'maxOrderCnt',
            'salesStartYmd',
            'salesEndYmd',
            'mileageFl',
            'mileageGoods',
            'mileageGoodsUnit',
            'goodsDiscountFl',
            'goodsDiscount',
            'goodsDiscountUnit',
            'payLimitFl',
            'payLimit',
            'goodsPriceString',
            'goodsPrice',
            'fixedPrice',
            'costPrice',
            'optionFl',
            'optionName',
            'optionTextFl',
            'addGoodsFl',
            'addGoods',
            'deliverySno',
            'delFl',
            'goodsSellFl',
            'goodsSellMobileFl',
            'goodsDisplayFl',
            'goodsDisplayMobileFl',
            'mileageGroup',
            'mileageGroupInfo',
            'mileageGroupMemberInfo',
            'fixedGoodsDiscount',
            'goodsDiscountGroup',
            'goodsDiscountGroupMemberInfo',
            'exceptBenefit',
            'exceptBenefitGroup',
            'exceptBenefitGroupInfo',
            'onlyAdultImageFl',
            'goodsBenefitSetFl',
            'benefitUseType',
            'newGoodsRegFl',
            'newGoodsDate',
            'newGoodsDateFl',
            'periodDiscountStart',
            'periodDiscountEnd',
            'regDt',
            'modDt'
        ];
        $arrInclude['image'] = [
            'imageSize',
            'imageName',
        ];

        $arrFieldWish = DBTableField::setTableField('tableWish', null, $arrExclude['wish'], 'w');
        $arrFieldGoods = DBTableField::setTableField('tableGoods', $arrInclude['goods'], null, 'g');
        $arrFieldOption = DBTableField::setTableField('tableGoodsOption', null, $arrExclude['option'], 'go');
        $arrFieldImage = DBTableField::setTableField('tableGoodsImage', $arrInclude['image'], null, 'gi');
        unset($arrExclude);

        // 장바구니 상품 기본 정보
        $strSQL = "SELECT w.sno,
                " . implode(', ', $arrFieldWish) . ", w.regDt,
                " . implode(', ', $arrFieldGoods) . ",
                " . implode(', ', $arrFieldOption) . ",
                " . implode(', ', $arrFieldImage) . "
            FROM " . DB_WISH . " w
            INNER JOIN " . DB_GOODS . " g ON w.goodsNo = g.goodsNo
            LEFT JOIN " . DB_GOODS_OPTION . " go ON w.optionSno = go.sno AND w.goodsNo = go.goodsNo
            LEFT JOIN " . DB_GOODS_IMAGE . " as gi ON g.goodsNo = gi.goodsNo AND gi.imageKind = 'list'
            WHERE " . implode(' AND ', $arrWhere) . "
            ORDER BY " . $strOrder;

        if($mallBySession) {
            $arrFieldGoodsGlobal = DBTableField::setTableField('tableGoodsGlobal',null,['mallSno']);
            $strSQLGlobal = "SELECT gg." . implode(', gg.', $arrFieldGoodsGlobal) . " FROM ".DB_WISH." as w INNER JOIN ".DB_GOODS_GLOBAL." as gg ON  w.goodsNo = gg.goodsNo AND gg.mallSno = '".$mallBySession['sno']."'  WHERE " . $globalWhere ;
            $tmpData = $this->db->query_fetch($strSQLGlobal);
            $globalData = array_combine (array_column($tmpData, 'goodsNo'), $tmpData);
        }

        $result = $this->db->query($strSQL);
        unset($arrWhere, $strOrder);

        // 삭제 상품에 대한 cartNo
        $_delCartSno = [];

        //상품 가격 노출 관련
        $goodsPriceDisplayFl = gd_policy('goods.display')['priceFl'];

        //품절상품 설정
        if(\Request::isMobile()) {
            $soldoutDisplay = gd_policy('soldout.mobile');
        } else {
            $soldoutDisplay = gd_policy('soldout.pc');
        }

        // 관심상품 기본설정
        $wishInfo = gd_policy('order.cart');

        /**해외몰 관련 **/

        $goods = new Goods();
        //상품 혜택 모듈
        $goodsBenefit = \App::load('\\Component\\Goods\\GoodsBenefit');
        while ($data = $this->db->fetch($result)) {

            //상품혜택 사용시 해당 변수 재설정
            $data = $goodsBenefit->goodsDataFrontConvert($data);

            $data['isCart'] = true;
            // stripcslashes 처리
            $data = gd_htmlspecialchars_stripslashes($data);

            if($mallBySession && $globalData[$data['goodsNo']]) {
                $data = array_replace_recursive($data, array_filter(array_map('trim',$globalData[$data['goodsNo']])));
            }

            // 상품 삭제 여부에 따른 처리
            if ($data['delFl'] === 'y') {
                $_delCartSno[] = $data['sno'];
                unset($data);
                continue;
            } else {
                unset($data['delFl']);
            }

            // 텍스트옵션 상품 정보
            $goodsOptionText = $goods->getGoodsOptionText($data['goodsNo']);
            if (empty($data['optionText']) === false && gd_isset($goodsOptionText)) {
                $optionTextKey = array_keys(json_decode($data['optionText'], true));
                foreach ($goodsOptionText as $goodsOptionTextInfo) {
                    if (in_array($goodsOptionTextInfo['sno'], $optionTextKey) === true) {
                        $data['optionTextInfo'][$goodsOptionTextInfo['sno']] = [
                            'optionSno' => $goodsOptionTextInfo['sno'],
                            'optionName' => $goodsOptionTextInfo['optionName'],
                            'baseOptionTextPrice' => $goodsOptionTextInfo['addPrice'],
                        ];
                    }
                }
            }

            // 추가 상품 정보
            if ($data['addGoodsFl'] === 'y' && empty($data['addGoodsNo']) === false) {
                $data['addGoodsNo'] = json_decode($data['addGoodsNo']);
                $data['addGoodsCnt'] = json_decode($data['addGoodsCnt']);
            } else {
                $data['addGoodsNo'] = '';
                $data['addGoodsCnt'] = '';
            }

            // 추가 상품 필수 여부
            if ($data['addGoodsFl'] === 'y' && empty($data['addGoods']) === false) {
                $data['addGoods'] = json_decode(gd_htmlspecialchars_stripslashes($data['addGoods']), true);
                foreach ($data['addGoods'] as $k => $v) {
                    if ($v['mustFl'] == 'y') {
                        if (is_array($data['addGoodsNo']) === false) {
                            $data['addGoodsSelectedFl'] = 'n';
                            break;
                        } else {
                            $addGoodsResult = array_intersect($v['addGoods'], $data['addGoodsNo']);
                            if (empty($addGoodsResult) === true) {
                                $data['addGoodsSelectedFl'] = 'n';
                                break;
                            }
                        }
                    }
                }
            }

            // 텍스트 옵션 정보 (sno, value)
            $data['optionTextSno'] = [];
            $data['optionTextStr'] = [];
            if ($data['optionTextFl'] === 'y' && empty($data['optionText']) === false) {
                $arrText = json_decode($data['optionText']);
                foreach ($arrText as $key => $val) {
                    $data['optionTextSno'][] = $key;
                    $data['optionTextStr'][$key] = $val;
                    unset($tmp);
                }
            }
            unset($data['optionText']);

            // 텍스트옵션 필수 사용 여부
            if ($data['optionTextFl'] === 'y') {
                if (gd_isset($goodsOptionText)) {
                    foreach ($goodsOptionText as $k => $v) {
                        if ($v['mustFl'] == 'y' && !in_array($v['sno'], $data['optionTextSno'])) {
                            $data['optionTextEnteredFl'] = 'n';
                        }
                    }
                }
            }
            unset($optionText);

            // 상품 구매 가능 여부
            $data = $cart->checkOrderPossible($data, true);
            if($data['orderPossible'] != 'y'){
                $data['isCart'] = false;
            }

            // 정책설정에서 품절상품 보관설정의 보관상품 품절시 자동삭제로 설정한 경우
            if ($wishInfo['wishSoldOutFl'] == 'n' && ($data['soldOutFl'] === 'y' || $data['optionSellFl'] === 'n' || ($data['soldOutFl'] === 'n' && $data['stockFl'] === 'y' && (($data['stockCnt'] != null && $data['stockCnt'] <= 0) || $data['totalStock'] <= 0 || $data['totalStock'] < $data['goodsCnt'])))) {
                $_delWishSno[] = $data['sno'];
                unset($data);
                continue;
            }

            //구매불가 대체 문구 관련
            if($data['goodsPermissionPriceStringFl'] =='y' && $data['goodsPermission'] !='all' && (($data['goodsPermission'] =='member'  && gd_is_login() === false) || ($data['goodsPermission'] =='group'  && !in_array(Session::get('member.groupSno'),explode(INT_DIVISION,$data['goodsPermissionGroup']))))) {
                $data['goodsPriceString'] = $data['goodsPermissionPriceString'];
            }

            //품절일경우 가격대체 문구 설정
            if (($data['soldOutFl'] === 'y' || ($data['soldOutFl'] === 'n' && $data['stockFl'] === 'y' && ($data['totalStock'] <= 0 || $data['totalStock'] < $data['goodsCnt']))) && $soldoutDisplay['soldout_price'] !='price'){
                if($soldoutDisplay['soldout_price'] =='text' ) {
                    $data['goodsPriceString'] = $soldoutDisplay['soldout_price_text'];
                } else if($soldoutDisplay['soldout_price'] =='custom' ) {
                    $data['goodsPriceString'] = "<img src='".$soldoutDisplay['soldout_price_img']."'>";
                }
            }

            $data['goodsPriceDisplayFl'] = 'y';
            if (empty($data['goodsPriceString']) === false && $goodsPriceDisplayFl =='n') {
                $data['goodsPriceDisplayFl'] = 'n';
            }

            $data['goodsMileageExcept'] = 'n';
            $data['couponBenefitExcept'] =  'n';
            $data['memberBenefitExcept'] =  'n';

            //타임세일 할인 여부
            $data['timeSaleFl'] = false;
            if (gd_is_plus_shop(PLUSSHOP_CODE_TIMESALE) === true) {
                $timeSale = \App::load('\\Component\\Promotion\\TimeSale');
                $timeSaleInfo = $timeSale->getGoodsTimeSale($data['goodsNo']);

                //추가 ||기준으로 (배열로만듬)자른후 goodsNo번호에 맞는 번호를 대입
                $mEachBenefit                        = explode('||',$timeSaleInfo['eachBenefit']);
                $findnum                             = ($timeSaleInfo['eachBenefitFindNo']-1);
                $timeSaleInfo['timeSaleEachBenefit'] = $mEachBenefit[$findnum];
                
                if ($timeSaleInfo) {
                    
                    $data['timeSaleFl'] = true;
                    if ($timeSaleInfo['mileageFl'] == 'n') {
                        $data['goodsMileageExcept'] = "y";
                    }
                    if ($timeSaleInfo['couponFl'] == 'n') {
                        $data['couponBenefitExcept'] = "y";
                    }
                    if ($timeSaleInfo['memberDcFl'] == 'n') {
                        $data['memberBenefitExcept'] = "y";
                    }

                    if ($data['goodsPrice'] > 0) {
                        if($timeSaleInfo['timeSaleEachBenefit']){
                            $data['goodsPrice'] = $data['goodsPrice'] - (($timeSaleInfo['timeSaleEachBenefit'] / 100) * $data['goodsPrice']);
                        }else{
                            $data['goodsPrice'] = $data['goodsPrice'] - (($timeSaleInfo['benefit'] / 100) * $data['goodsPrice']);
                        }

                    }
                }
            }

            // 비회원시 담은 상품과 회원로그인후 담은 상품이 중복으로 있는경우 재고 체크
            $data['duplicationGoods'] = 'n';
            if (isset($tmpStock[$data['goodsNo']][$data['optionSno']]) === false) {
                $tmpStock[$data['goodsNo']][$data['optionSno']] = $data['goodsCnt'];
            } else {
                $data['duplicationGoods'] = 'y';
                $chkStock = $tmpStock[$data['goodsNo']][$data['optionSno']] + $data['goodsCnt'];
                if ($data['stockFl'] == 'y' && $data['stockCnt'] < $chkStock) {
                    $data['stockOver'] = 'y';
                }
            }

            // 상품 이미지 처리 @todo 상품 사이즈 설정 값을 가지고 와서 이미지 사이즈 변경을 할것
            if ($data['onlyAdultFl'] == 'y' && gd_check_adult() === false && $data['onlyAdultImageFl'] =='n') {
                if (\Request::isMobile()) {
                    $data['goodsImageSrc'] = "/data/icon/goods_icon/only_adult_mobile.png";
                } else {
                    $data['goodsImageSrc'] = "/data/icon/goods_icon/only_adult_pc.png";
                }

                $data['goodsImage'] = SkinUtils::makeImageTag($data['goodsImageSrc'], $imageSize['size1']);
            } else {
                $data['goodsImage'] = gd_html_preview_image($data['imageName'], $data['imagePath'], $data['imageStorage'], $imageSize['size1'], 'goods', $data['goodsNm'], 'class="imgsize-s"', false, false, $imageSize['hsize1']);
            }


            unset($data['imageStorage'], $data['imagePath'], $data['imageName'], $data['imagePath']);

            $data = $cart->getMemberDcFlInfo($data);

            $getData[] = $data;
            unset($data);
        }

        // 삭제 상품 및 품절 상품이 있는 경우 관심상품 삭제
        if (empty($_delWishSno) === false) {
            $this->setWishDelete($_delWishSno);
        }

        $getData = $cart->setWishData($getData);

        if (is_array($getData)) {
            $scmClass = \App::load(\Component\Scm\Scm::class);
            $this->wishScmCnt = count($getData);
            $this->wishScmInfo = $scmClass->getCartScmInfo(array_keys($getData));
        }

        return $getData;
    }
}