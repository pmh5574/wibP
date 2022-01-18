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
    // 검색조건
    public function setSearchGoodsList($getValue = null, $searchTerms = null)
    {
        parent::setSearchGoodsList($getValue, $searchTerms);
        
        $req = Request::get()->toArray();
        
        // 컬러 체크
        $filterColor = $req['filterColor'];
        if($filterColor != ''){
            $this->arrWhere[] = $this->setColorQuery($filterColor, 'g.');
        }
        
        // 가격 체크
        $filterPriceS = $req['fpStart'];
        $filterPriceE = $req['fpEnd'];
        if($filterPriceS != '' && $filterPriceE != ''){
            $this->arrWhere[] = $this->setPriceQuery($filterPriceS, $filterPriceE, 'g.');
        }
        
        // 사이즈 체크
        $filterPattern = $req['filterPattern'];
        if($filterPattern != ''){
            $this->arrWhere[] = $this->setSizeQuery($filterPattern, 'pattern', 'g.');
        }
        
        // 시즌 체크
        $filterSeason = $req['filterSeason'];
        if($filterSeason != ''){
            $this->arrWhere[] = $this->setOtherQuery($filterSeason, 'season', 'g.');
        }
        
    }
    
    
    // 컬러 쿼리 생성
    public function setColorQuery($filterColor, $join = '')
    {
        $db = \App::load('DB');
        $color_sql = "select itemNm from es_code where groupCd = 05001 ";
        $result = $db->query_fetch($color_sql);
        
        $colorArr = [];
        foreach ($result as $value) {
            $data = explode("^|^", $value['itemNm']);
            $colorArr[$data[0]] =$data[1];
        }
        
        $addColor = '(';
        $addColorSub = '';
        foreach ($filterColor as $fvalue) {
            $addColorSub .= " ".$join."goodsColor REGEXP ? or";
            $this->db->bind_param_push($this->arrBind,'s',$colorArr[$fvalue]);
        }
        $addColor .= substr($addColorSub, 0, -2);
        $addColor .= ')';
        
        return $addColor;
    }
    
    // 가격 쿼리 생성
    public function setPriceQuery($filterPriceS, $filterPriceE, $join = '')
    {
        
        $addprice = "( ".$join."goodsPrice >= ? and ".$join."goodsPrice <= ? )";
        $this->db->bind_param_push($this->arrBind,'i',$filterPriceS);
        $this->db->bind_param_push($this->arrBind,'i',$filterPriceE);

        return $addprice;
    }
    
    public function setOtherQuery($data, $collum, $join = '')
    {
        
        $addQuery = '(';
        $addQuerySub = '';
        foreach ($data as $value) {
            $addQuerySub .= " ".$join.$collum." REGEXP ? or";
            $this->db->bind_param_push($this->arrBind,'s',$value);
        }
        $addQuery .= substr($addQuerySub, 0, -2);
        $addQuery .= ')';

        return $addQuery;
    }
    
    /**
     * 사이즈 쿼리 생성
     * 현재 미소페는 옵션사이즈를 optionValue1과 optionValue2 에 두가지 형식으로 사용중
     * @param type $data array => 사이즈 배열
     * @return string
     */
    public function setSizeQuery($data, $collum = '', $join = '')
    {
        $addQuery = "( g.goodsNo =  (select goodsNo from es_goodsOption where ( optionValue1 REGEXP '";
        $addQuerySub = '';
        foreach ($data as $value) {
            if(strpos($value, '~') !== false){
                $explodeSize = explode("~", $value);
                for($i = intval($explodeSize[0]); $i < intval($explodeSize[1])+1; $i++){
                    $addQuerySub .= $i."|";
                }
            }else{
                $addQuerySub .= $value."|";
            }
            
        }
        $addQuery .= substr($addQuerySub, 0, -1);
        
        $addQuery .= "' or optionValue2 REGEXP '";
        $addQuerySub2 = '';
        foreach ($data as $value) {
            if(strpos($value, '~') !== false){
                $explodeSize = explode("~", $value);
                for($i = intval($explodeSize[0]); $i < intval($explodeSize[1])+1; $i++){
                    $addQuerySub2 .= $i."|";
                }
            }else{
                $addQuerySub2 .= $value."|";
            }
            
        }
        $addQuery .= substr($addQuerySub2, 0, -1);
        
        
        $addQuery .= "') and goodsNo = g.goodsNo limit 0,1) )";
//echo $addQuery; exit;
        return $addQuery;
    }

    /**
     * 상품 정보 출력 (상품 상세)
     *
     * @param string $goodsNo 상품 번호
     *
     * @return array 상품 정보
     * @throws Except
     */
    public function getGoodsView($goodsNo)
    {
        $mallBySession = SESSION::get(SESSION_GLOBAL_MALL);

        // Validation - 상품 코드 체크
        if (Validator::required($goodsNo, true) === false) {
            throw new Exception(__('상품 코드를 확인해주세요.'));
        }

        // 필드 설정
        $arrExcludeGoods = ['goodsIconStartYmd', 'goodsIconEndYmd', 'goodsIconCdPeriod', 'goodsIconCd', 'memo'];
        $arrFieldGoods = DBTableField::setTableField('tableGoods', null, $arrExcludeGoods, 'g');
        $this->db->strField = implode(', ', $arrFieldGoods) . ',
            ( if (g.soldOutFl = \'y\' , \'y\', if (g.stockFl = \'y\' AND g.totalStock <= 0, \'y\', \'n\') ) ) as soldOut,
            ( if (g.' . $this->goodsSellFl . ' = \'y\', g.' . $this->goodsSellFl . ', \'n\')  ) as orderPossible';

        // 조건절 설정
        //if(!Session::has('manager.managerId')) $arrWhere[] = 'g.' . $this->goodsDisplayFl . ' = \'y\'';
        $arrWhere[] = 'g.delFl = \'n\'';
        $arrWhere[] = 'g.applyFl = \'y\'';

        $this->db->strWhere = implode(' AND ', $arrWhere);

        // 상품 기본 정보
        $getData = $this->getGoodsInfo($goodsNo);

        // 삭제된 상품에 접근시 예외 처리
        if(empty($getData)) {
            throw new Exception(__('해당 상품은 현재 구매가 불가한 상품입니다.'));
        }

        // 아이콘 테이블 분리로 인한 추가
        $tmpGoodsIcon = [];
        $iconList = $this->getGoodsDetailIcon($goodsNo);
        foreach ($iconList as $iconKey => $iconVal) {
            if ($iconVal['iconKind'] == 'pe') {
                if (empty($iconVal['goodsIconStartYmd']) === false && empty($iconVal['goodsIconEndYmd']) === false && empty($iconVal['goodsIconCd']) === false && strtotime($iconVal['goodsIconStartYmd']) <= time() && strtotime($iconVal['goodsIconEndYmd']) >= time()) {
                    $tmpGoodsIcon[] = $iconVal['goodsIconCd'];
                }
            }

            if ($iconVal['iconKind'] == 'un') {
                $tmpGoodsIcon[] = $iconVal['goodsIconCd'];
            }
        }

        $getData['goodsIcon'] = implode(INT_DIVISION,$tmpGoodsIcon);

        //상품 혜택 정보
        $goodsBenefit = \App::load('\\Component\\Goods\\GoodsBenefit');
        $getData = $goodsBenefit->goodsDataFrontConvert($getData,null,'goodsIcon');

        if (empty($getData) === true && !Session::has('manager.managerId')) {
            throw new Exception(__('해당 상품은 쇼핑몰 노출안함 상태로 검색되지 않습니다.'));
        }

        // 승인중인 상품에 대한 접근 예외 처리
        if ($getData['applyFl'] != 'y') {
            throw new Exception(__('본 상품은 접근이 불가능 합니다.'));
        }

        // 브랜드 정보
        if (empty($getData['brandCd']) === false) {
            $brand = \App::load('\\Component\\Category\\Brand');
            $getData['brandNm'] = $brand->getCategoryData($getData['brandCd'], null, 'cateNm')[0]['cateNm'];
        } else {
            $getData['brandNm'] = '';
        }

        if($mallBySession) {
            $arrFieldGoodsGlobal = DBTableField::setTableField('tableGoodsGlobal',null,['mallSno']);
            $strSQLGlobal = "SELECT gg." . implode(', gg.', $arrFieldGoodsGlobal) . " FROM ".DB_GOODS_GLOBAL." as gg WHERE   gg.goodsNo  = ".$getData['goodsNo']." AND gg.mallSno = ".$mallBySession['sno'];
            $tmpData = $this->db->query_fetch($strSQLGlobal,null,false);
            if($tmpData) $getData = array_replace_recursive($getData, array_filter(array_map('trim',$tmpData)));
        }
        //카테고리 정보
        $cate = \App::load('\\Component\\Category\\Category');
        $tmpCategoryList = $cate->getCateCd($getData['goodsNo']);
        if($tmpCategoryList) {
            foreach($tmpCategoryList as $k => $v) {
                $categoryList[$v] = gd_htmlspecialchars_decode($cate->getCategoryPosition($v));
            }
        }
        if($categoryList) $getData['categoryList'] = $categoryList;

        // 대표카테고리명 정보
        if (empty($getData['cateCd']) === false) {
            $getData['cateNm'] = $cate->getCategoryData($getData['cateCd'], null, 'cateNm')[0]['cateNm'];
        } else {
            $getData['cateNm'] = '';
        }

        // 추가항목 정보
        $getData['addInfo'] = $this->getGoodsAddInfo($goodsNo); // 추가항목 정보

        // 이미지 정보
        $tmp['image'] = $this->getGoodsImage($goodsNo, ['detail', 'magnify']);

        // 상품 아이콘
        if ($getData['goodsIcon']) {
            $tmp['goodsIcon'] = $this->getGoodsIcon($getData['goodsIcon']);
        }

        // 상품 아이콘
        if ($getData['goodsBenefitIconCd']) {
            $tmp['goodsBenefitIconCd'] = $this->getGoodsIcon($getData['goodsBenefitIconCd']);
        }

        $imgConfig = gd_policy('goods.image');

        //품절상품 설정
        if(Request::isMobile()) {
            $soldoutDisplay = gd_policy('soldout.mobile');
        } else {
            $soldoutDisplay = gd_policy('soldout.pc');
        }

        // 상품 이미지 처리
        $getData['magnifyImage'] = 'n';
        if (empty($tmp['image'])) {
            $getData['image']['detail'][0] = '';
            $getData['image']['thumb'][0] = '';
        } else {
            foreach ($tmp['image'] as $key => $val) {
                $imageHeightSize = '';
                if ($imgConfig['imageType'] == 'fixed') {
                    foreach ($imgConfig[$val['imageKind']] as $k => $v) {
                        if (stripos($k, 'size') === 0) {
                            if ($val['imageSize'] == $v) {
                                $imageHeightSize = $imgConfig[$val['imageKind']]['h' . $k];
                                break;
                            }
                        }
                    }
                }

                // 이미지 사이즈가 없는 경우
                if (empty($val['imageSize']) === true) {
                    $imageSize = $imgConfig[$val['imageKind']]['size1'];
                } else {
                    $imageSize = $val['imageSize'];
                }

                //실제 이미지 사이즈가 있는 경우
                if($val['imageRealSize']) {
                    $imageSize = explode(",",$val['imageRealSize'])[0];
                }

                // 모바일샵 접속인 경우
                if (Request::isMobile()) {
                    $imageSize = 140;
                    $imageHeightSize = '';
                }

                $getData['image'][$val['imageKind']]['img'][] = gd_html_preview_image($val['imageName'], $getData['imagePath'], $getData['imageStorage'], $imageSize, 'goods', $getData['goodsNm'], null, false, false, $imageHeightSize);

                $getData['image'][$val['imageKind']]['thumb'][] = gd_html_preview_image($val['imageName'], $getData['imagePath'], $getData['imageStorage'], 68, 'goods', $getData['goodsNm'], null, false, true);

                if ($val['imageKind'] == 'magnify') {
                    $getData['magnifyImage'] = 'y';
                }
            }
            if (isset($getData['image']) === false) {
                $getData['image']['detail'][0] = '';
                $getData['image']['thumb'][0] = '';
            }
        }

        // 소셜 공유용 이미지 처리(이미지 없는경우 빈 이미지 출력되도록 수정)
        $socialShareImage = SkinUtils::imageViewStorageConfig($tmp['image'][0]['imageName'], $getData['imagePath'], $getData['imageStorage'], $imageSize, 'goods');
        $getData['social'] = $socialShareImage[0];


        // 상품 혜택 아이콘 처리
        $getData['goodsIcon'] = '';
        $getData['goodsBenefitIconCd'] = '';
        if (empty($tmp['goodsBenefitIconCd']) === false) {
            foreach ($tmp['goodsBenefitIconCd'] as $key => $val) {
                $getData['goodsIcon'] .= gd_html_image(UserFilePath::icon('goods_icon', $val['iconImage'])->www(), $val['iconNm']) . ' ';
            }
        }

        // 상품 아이콘 처리
        if (empty($tmp['goodsIcon']) === false) {
            foreach ($tmp['goodsIcon'] as $key => $val) {
                $getData['goodsIcon'] .= gd_html_image(UserFilePath::icon('goods_icon', $val['iconImage'])->www(), $val['iconNm']) . ' ';
            }
        }


        // 옵션 체크, 옵션 사용인 경우
        if ($getData['optionFl'] === 'y') {
            // 옵션 & 가격 정보
            $getData['option'] = gd_htmlspecialchars($this->getGoodsOption($goodsNo, $getData));
            if($getData['option']) {
                $getData['optionEachCntFl'] = 'many'; // 옵션 개수
                if (empty($getData['option']['optVal'][2]) === true) {
                    $getData['optionEachCntFl'] = 'one'; // 옵션 개수

                    // 분리형 옵션인데 옵션이 하나인 경우 일체형으로 변경
                    if ($getData['optionDisplayFl'] == 'd') {
                        $getData['optionDisplayFl'] = 's';
                    }
                }


                // 상품 옵션 아이콘
                $tmp['optionIcon'] = $this->getGoodsOptionIcon($goodsNo);

                if (empty($tmp['optionIcon']) === false) {
                    $imageSize = $imgConfig['detail'];
                    foreach ($tmp['optionIcon'] as $key => $val) {
                        if (empty($val['goodsImage']) === false) {
                            $getData['optionIcon']['goodsImage'][$val['optionValue']] =SkinUtils::imageViewStorageConfig($val['goodsImage'], $getData['imagePath'], $getData['imageStorage'], '100', 'goods')[0];
                            if( $getData['optionImageDisplayFl'] =='y') {
                                $optionImagePreview = gd_html_preview_image($val['goodsImage'], $getData['imagePath'], $getData['imageStorage'], $imageSize, 'goods', $getData['goodsNm'], null, false, false);;
                                $getData['image']['detail']['img'][] =$optionImagePreview;
                                $getData['image']['detail']['thumb'][] = $optionImagePreview;
                            }
                        }
                    }
                    // 옵션 값을 json_encode 처리함
                    //$getData['optionIcon'] = json_encode($getData['optionIcon']);
                }
                // 분리형 옵션인 경우
                if ($getData['optionDisplayFl'] == 'd') {
                    // 옵션명
                    $getData['optionName'] = explode(STR_DIVISION, $getData['optionName']);

                    // 첫번째 옵션 값
                    $getData['optionDivision'] = $getData['option']['optVal'][1];

                    // 분리형 다중옵션 경우 첫번째 옵션의 재고량 및 옵션품절상태 조회
                    if (method_exists($this, 'getOptionValueStock') && is_array($getData['optionDivision']) === true) {
                        foreach ($getData['optionDivision'] as $key => $value) {
                            $getData['optionDivisionStock'][$key] = $this->getOptionValueStock($goodsNo, [$value]);
                        }
                    }

                    unset($getData['option']['optVal']);
                    // 일체형 옵션인 경우
                } else if ($getData['optionDisplayFl'] == 's') {
                    unset($getData['option']['optVal']);

                    // 옵션명
                    $getData['optionName'] = str_replace(STR_DIVISION, '/', $getData['optionName']);

                    foreach ($getData['option'] as $key => $val) {

                        if($getData['optionIcon']['goodsImage'][$val['optionValue1']]) {
                            $getData['option'][$key]['optionImage'] = $getData['optionIcon']['goodsImage'][$val['optionValue1']];
                        }

                        $optionValue[$key] = [];
                        for ($i = 1; $i <= DEFAULT_LIMIT_OPTION; $i++) {
                            if (is_null($val['optionValue' . $i]) === false && strlen($val['optionValue' . $i]) > 0) {
                                $optionValue[$key][] = $val['optionValue' . $i];
                            }
                            unset($getData['option'][$key]['optionValue' . $i]);
                        }
                        $getData['option'][$key]['optionValue'] = implode('/', $optionValue[$key]);
                    }
                }

                $getData['stockCnt'] = $getData['option'][0]['stockCnt'];

            } else {
                throw new Exception(__('상품 옵션을 확인해주세요.'));
            }
        } else {
            $getData['option'] = gd_htmlspecialchars($this->getGoodsOption($goodsNo, $getData));
            $getData['stockCnt'] = $getData['totalStock'];
            if($getData['option'][0]['optionPrice'] > 0) $getData['option'][0]['optionPrice'] = 0; //옵션사용안함으로 가격 없음
            if($getData['stockFl'] =='y' && $getData['minOrderCnt'] > $getData['totalStock'])  $getData['orderPossible'] = 'n';
        }

        //상품 상세 설명 관련
        if($getData['goodsDescriptionSameFl'] =='y') {
            $getData['goodsDescriptionMobile'] = $getData['goodsDescription'];
        }

        /* 타임 세일 관련 */
        $getData['timeSaleFl'] = false;
        if (gd_is_plus_shop(PLUSSHOP_CODE_TIMESALE) === true) {
            $timeSale = \App::load('\\Component\\Promotion\\TimeSale');
            $timeSaleInfo = $timeSale->getGoodsTimeSale($goodsNo);
            if($timeSaleInfo) {
                $getData['timeSaleFl'] = true;
                if($timeSaleInfo['timeSaleCouponFl'] =='n') $couponConfig['couponUseType']  = "n";
                $timeSaleInfo['timeSaleDuration'] = strtotime($timeSaleInfo['endDt'])- time();
                if($timeSaleInfo['orderCntDisplayFl'] =='y' ) { //타임세일 진행기준 판매개수
                    $arrTimeSaleBind = [];
                    $strTimeSaleSQL = "SELECT sum(orderCnt) as orderCnt FROM " . DB_GOODS_STATISTICS . " WHERE goodsNo = ?";
                    $this->db->bind_param_push($arrTimeSaleBind, 'i', $goodsNo);
                    if($timeSaleInfo['orderCntDateFl'] =='y' ) {
                        $strTimeSaleSQL .= " AND UNIX_TIMESTAMP(regDt) <  ? AND  UNIX_TIMESTAMP(regDt)  > ?";
                        $this->db->bind_param_push($arrTimeSaleBind, 'i', strtotime($timeSaleInfo['endDt']));
                        $this->db->bind_param_push($arrTimeSaleBind, 'i', strtotime($timeSaleInfo['startDt']));
                    }
                    $timeSaleInfo['orderCnt'] = $this->db->query_fetch($strTimeSaleSQL, $arrTimeSaleBind, false)['orderCnt'];
                    unset($arrTimeSaleBind,$strTimeSaleSQL);
                }

                //추가
                $mEachBenefit = explode('||',$timeSaleInfo['eachBenefit']);
                $findnum = ($timeSaleInfo['eachBenefitFindNo']-1);
                $timeSaleInfo['timeSaleEachBenefit'] = $mEachBenefit[$findnum];

                $getData['timeSaleInfo'] = $timeSaleInfo;
                if($getData['goodsPrice'] > 0 ) {
                    $getData['oriGoodsPrice'] = $getData['goodsPrice'] ;
                    if($timeSaleInfo['timeSaleEachBenefit']){
                        $getData['goodsPrice'] = gd_number_figure($getData['goodsPrice'] - (($timeSaleInfo['timeSaleEachBenefit'] / 100) * $getData['goodsPrice']), $this->trunc['unitPrecision'], $this->trunc['unitRound']);
                    }else{
                        $getData['goodsPrice'] = gd_number_figure($getData['goodsPrice'] - (($timeSaleInfo['benefit'] / 100) * $getData['goodsPrice']), $this->trunc['unitPrecision'], $this->trunc['unitRound']);
                    }
                }

                //상품 옵션가(일체형) 타임세일 할인율 적용 ( 텍스트 옵션가 / 추가상품가격 제외)
                if($getData['optionFl'] === 'y'){
                    foreach ($getData['option'] as $key => $val){
                        $getData['option'][$key]['optionPrice'] = gd_number_figure($val['optionPrice'] - (($timeSaleInfo['benefit'] / 100) * $val['optionPrice']), $this->trunc['unitPrecision'], $this->trunc['unitRound']);
                    }
                }
            }
        }
        $couponConfig = gd_policy('coupon.config');
        // 쿠폰가 회원만 노출
        if ($couponConfig['couponDisplayType'] == 'member') {
            if (gd_check_login()) {
                $couponPriceYN = true;
            } else {
                $couponPriceYN = false;
            }
        } else {
            $couponPriceYN = true;
        }

        // 혜택제외 체크 (쿠폰)
        $exceptBenefit = explode(STR_DIVISION, $getData['exceptBenefit']);
        $exceptBenefitGroupInfo = explode(INT_DIVISION, $getData['exceptBenefitGroupInfo']);
        if (in_array('coupon', $exceptBenefit) === true && ($getData['exceptBenefitGroup'] == 'all' || ($getData['exceptBenefitGroup'] == 'group') && in_array(Session::get('member.memNo'), $exceptBenefitGroupInfo) === true)) {
            $couponPriceYN = false;
        }

        // 쿠폰 할인 금액
        if ($couponConfig['couponUseType'] == 'y' && $couponPriceYN  && $getData['goodsPrice'] > 0 && empty($getData['goodsPriceString']) === true) {
            // 쿠폰 모듈 설정
            $coupon = \App::load('\\Component\\Coupon\\Coupon');
            // 해당 상품의 모든 쿠폰
            $couponArrData = $coupon->getGoodsCouponDownList($getData['goodsNo']);

            // 해당 상품의 쿠폰가
            $getData['couponDcPrice'] = $couponSalePrice = $coupon->getGoodsCouponDisplaySalePrice($couponArrData, $getData['goodsPrice']);
            if ($couponSalePrice) {
                $getData['couponPrice'] = $getData['goodsPrice'] - $couponSalePrice;
                $getData['couponSalePrice'] = $couponSalePrice;
                if ($getData['couponPrice'] < 0) {
                    $getData['couponPrice'] = 0;
                }
            }
        }

        //내 쿠폰적용가 추가 (회원 별 보유중인 상품 적용 쿠폰(마일리지X) 중 최고 할인가로 노출)
        //$couponConfig['couponUseType'] : 쿠폰 사용 유무,  $getData['goodsPrice'] : 판매가, $getData['goodsPriceString'] : 가격대체문구
        if (gd_check_login() === 'member' && $couponConfig['couponUseType'] == 'y' && $getData['goodsPrice'] > 0 && empty($getData['goodsPriceString']) === true) {
            if (gettype($coupon) !== 'object') { $coupon = \App::load('\\Component\\Coupon\\Coupon'); }
            //상품에 적용 가능한 쿠폰 리스트 가져오기
            $myCouponArrData = $coupon->getGoodsMemberCouponList($getData['goodsNo'], Session::get('member.memNo'), $this->_memInfo['groupSno'], null, null, 'goods', false, [], ['sale']);
            // 위에서 추출한 데이터를 가지고 최대 할인가를 구함.
            $myCouponSalePrice = $coupon->getGoodsCouponDisplaySalePrice($myCouponArrData, $getData['goodsPrice']);
            if ($myCouponSalePrice > 0) {
                $getData['myCouponSalePrice']   = $getData['goodsPrice'] - $myCouponSalePrice;
                $getData['myCouponPrice']       = $myCouponSalePrice;
                if ($getData['myCouponSalePrice'] < 0) { $getData['myCouponPrice'] = 0; }
            }
            unset($myCouponArrData);
            unset($myCouponSalePrice);
        }

        $getData['displayCouponPrice'] = 'n'; //쿠폰적용가 노출 여부
        if ($getData['myCouponSalePrice'] > 0 || $getData['couponPrice'] > 0) {
            $getData['displayCouponPrice'] = 'y';
        }

        //추가 상품 정보
        if ($getData['addGoodsFl'] === 'y' && empty($getData['addGoods']) === false) {

            $getData['addGoods'] = json_decode(gd_htmlspecialchars_stripslashes($getData['addGoods']), true);

            //필수 추가상품 중 승인완료가 아닌 상품이 있는 경우 구매 불가
            $addGoods = \App::load('\\Component\\Goods\\AddGoods');
            if ($getData['addGoods']) {
                foreach ($getData['addGoods'] as $k => $v) {

                    if($v['addGoods']) {
                        if($v['mustFl'] =='n') $addGoods->arrWhere[] = "applyFl = 'y'";
                        else {
                            $applyCheckCnt = $this->db->getCount(DB_ADD_GOODS, 'addGoodsNo', 'WHERE applyFl !="y"  AND addGoodsNo IN ("' . implode('","', $v['addGoods']) . '")');
                            if($applyCheckCnt > 0 ) {
                                $getData['orderPossible'] = 'n';
                                break;
                            } else {
                                $addGoods->arrWhere[] = "applyFl != ''";
                            }
                        }

                        foreach ($v['addGoods']as $k1 => $v1) {
                            $tmpField[] = 'WHEN \'' . $v1 . '\' THEN \'' . sprintf("%0".strlen(count($v['addGoods']))."d",$k1) . '\'';
                        }

                        $sortField = ' CASE ag.addGoodsNo ' . implode(' ', $tmpField) . ' ELSE \'\' END ';
                        unset($tmpField);

                        $getData['addGoods'][$k]['addGoodsList'] = $addGoods->getInfoAddGoodsGoods($v['addGoods'],null,$sortField,"viewFl = 'y' ");
                        $getData['addGoods'][$k]['addGoodsImageFl'] = "n";
                        if($getData['addGoods'][$k]['addGoodsList']) {
                            foreach($getData['addGoods'][$k]['addGoodsList'] as $k1 => $v1) {
                                // strip_tags 처리를 통해 결제오류 수정
                                $getData['addGoods'][$k]['addGoodsList'][$k1]['goodsNm'] = htmlentities(stripslashes(StringUtils::stripOnlyTags($getData['addGoods'][$k]['addGoodsList'][$k1]['goodsNm'])));
                                $getData['addGoods'][$k]['addGoodsList'][$k1]['optionNm'] = htmlentities(stripslashes(StringUtils::stripOnlyTags($getData['addGoods'][$k]['addGoodsList'][$k1]['optionNm'])));

                                //추가 상품등록페이지 - 추가 상품명
                                if($v1['globalGoodsNm']) $getData['addGoods'][$k]['addGoodsList'][$k1]['goodsNm'] = htmlentities(stripslashes(StringUtils::stripOnlyTags($v1['globalGoodsNm'])));
                                if($v1['imageNm']) {
                                    $getData['addGoods'][$k]['addGoodsList'][$k1]['imageSrc'] = SkinUtils::imageViewStorageConfig($v1['imageNm'], $v1['imagePath'], $v1['imageStorage'], '50', 'add_goods')['0'];
                                    $getData['addGoods'][$k]['addGoodsImageFl'] = "y";
                                }
                            }
                        }
                    }
                }
            }
        }


        // 텍스트 옵션 정보
        if ($getData['optionTextFl'] === 'y') {
            $getData['optionText'] = gd_htmlspecialchars($this->getGoodsOptionText($goodsNo));
        }

        // QR코드
        if (gd_is_plus_shop(PLUSSHOP_CODE_QRCODE) === true) {
            $qrcode = gd_policy('promotion.qrcode'); // QR코드 설정
            if ($qrcode['useGoods'] !== 'y') {
                $getData['qrCodeFl'] = 'n';
            }
        } else {
            $getData['qrCodeFl'] = 'n';
        }

        // 상품 정보 처리
        $getData['goodsNmDetail'] = StringUtils::htmlSpecialCharsStripSlashes($this->getGoodsName($getData['goodsNmDetail'], $getData['goodsNm'], $getData['goodsNmFl'])); // 상품 상세 페이지 -  상품명
        if (Validator::date($getData['makeYmd'], true) === false) { // 제조일 체크
            $getData['makeYmd'] = null;
        }
        if (Validator::date($getData['launchYmd'], true) === false) { // 출시일 체크
            $getData['launchYmd'] = null;
        }

        //배송비 관련
        if ($getData['deliverySno']) {
            $delivery = \App::load('\\Component\\Delivery\\Delivery');
            $deliveryData = $delivery->getDataSnoDelivery($getData['deliverySno']);
            if ($deliveryData['basic']['areaFl'] == 'y' && gd_isset($deliveryData['basic']['areaGroupNo'])) {
                $deliveryData['areaDetail'] = $delivery->getSnoDeliveryArea($deliveryData['basic']['areaGroupNo']);
            }

            $deliveryData['basic']['fixFlText'] = $delivery->getFixFlText($deliveryData['basic']['fixFl']);
            $deliveryData['basic']['goodsDeliveryFlText'] = $delivery->getGoodsDeliveryFlText($deliveryData['basic']['goodsDeliveryFl']);
            $deliveryData['basic']['collectFlText'] = $delivery->getCollectFlText($deliveryData['basic']['collectFl']);
            $deliveryData['basic']['areaFlText'] = $delivery->getAddFlText($deliveryData['basic']['areaFl']);
            $deliveryData['basic']['pricePlusStandard'] = explode(STR_DIVISION, $deliveryData['basic']['pricePlusStandard']);
            $deliveryData['basic']['priceMinusStandard'] = explode(STR_DIVISION, $deliveryData['basic']['priceMinusStandard']);
            // 가공된 배송 방식 데이터
            $deliveryData['basic']['deliveryMethodFlData'] = [];
            $deliveryMethodFlArr = array_values(array_filter(explode(STR_DIVISION, $deliveryData['basic']['deliveryMethodFl'])));
            if($deliveryMethodFlArr > 0){
                foreach($deliveryMethodFlArr as $key => $value){
                    if($value === 'etc'){
                        $deliveryMethodListName = gd_get_delivery_method_etc_name();
                    }
                    else {
                        $deliveryMethodListName = $delivery->deliveryMethodList['name'][$value];
                    }
                    $deliveryData['basic']['deliveryMethodFlData'][$value] = $deliveryMethodListName;

                    if($key === 0){
                        $deliveryData['basic']['deliveryMethodFlFirst'] = [
                            'code' => $value,
                            'name' => $deliveryMethodListName,
                        ];
                    }
                }
            }
            //배송방식 방문수령지
            if($deliveryData['basic']['dmVisitTypeDisplayFl'] !== 'y'){
                $deliveryData['basic']['deliveryMethodVisitArea'] = $delivery->getVisitAddress($getData['deliverySno'], true);
            }

            $getData['delivery'] = $deliveryData;

            // 상품판매가를 기준으로 배송비 선택해서 charge의 키를 저장한다.
            $getData['selectedDeliveryPrice'] = 0;
            if (in_array($deliveryData['basic']['fixFl'], ['price', 'weight'])) {
                // 비교할 필드값 설정
                $compareField = $getData['goods' . ucfirst($deliveryData['basic']['fixFl'])];
                foreach ($getData['delivery']['charge'] as $dKey => $dVal) {
                    // 금액 or 무게가 범위에 없으면 통과
                    if (floatval($dVal['unitEnd']) > 0) {
                        if (floatval($dVal['unitStart']) <= floatval($compareField) && floatval($dVal['unitEnd']) > floatval($compareField)) {
                            $getData['selectedDeliveryPrice'] = $dKey;
                            break;
                        }
                    } else {
                        if (floatval($dVal['unitStart']) <= floatval($compareField)) {
                            $getData['selectedDeliveryPrice'] = $dKey;
                            break;
                        }
                    }
                }
            }

            /*
             * 수량별 배송비 이면서 범위 반복 설정을 사용 할 경우 수량1의 기준으로 배송비 노출
             * @todo 추후 금액별, 무게별 배송비의 범위 반복 설정 사용일 경우도 계산해서 노출해야 하므로 임시로 노출시킨다.
             */
            if ($deliveryData['basic']['fixFl'] === 'count' && $deliveryData['basic']['rangeRepeat'] === 'y') {
                if((int)$deliveryData['charge'][0]['unitEnd'] <= 1){
                    $getData['selectedDeliveryPrice'] = 1;
                }
            }
        }

        // 상품 필수 정보
        $getData['goodsMustInfo'] = json_decode(gd_htmlspecialchars_stripslashes($getData['goodsMustInfo']), true);

        // KC인증 정보 (해외몰 적용X)
        if (Globals::get('gGlobal.isFront') == false) {
            $this->getKcmarkInfo($getData['kcmarkInfo']);
        }

        // 마일리지 설정
        $mileage = gd_mileage_give_info();

        $getData['goodsMileageFl'] = 'y';
        // 통합 설정인 경우 마일리지 설정
        if ($getData['mileageFl'] == 'c' && $mileage['give']['giveFl'] == 'y') {
            $mileagePercent = $mileage['give']['goods'] / 100;

            // 상품 기본 마일리지 정보
            $getData['mileageBasic'] = gd_number_figure($getData['goodsPrice'] * $mileagePercent, $mileage['trunc']['unitPrecision'], $mileage['trunc']['unitRound']);

            // 상품 옵션 마일리지 정보
            if ($getData['optionFl'] === 'y') {
                foreach ($getData['option'] as $key => $val) {
                    $getData['option'][$key]['mileageOption'] = gd_number_figure($val['optionPrice'] * $mileagePercent, $mileage['trunc']['unitPrecision'], $mileage['trunc']['unitRound']);
                }
            }


            // 추가 상품 마일리지 정보
            if ($getData['addGoodsFl'] === 'y' && empty($getData['addGoods']) === false && empty($getData['addGoodsGoodsNo']) === false) {
                foreach ($getData['addGoods'] as $key => $val) {
                    $getData['addGoods'][$key]['mileageAddGoods'] = gd_number_figure($val['goodsPrice'] * $mileagePercent, $mileage['trunc']['unitPrecision'], $mileage['trunc']['unitRound']);
                }
            }


            // 상품 텍스트 옵션 마일리지 정보
            if ($getData['optionTextFl'] === 'y') {
                foreach ($getData['optionText'] as $key => $val) {
                    $getData['optionText'][$key]['mileageOptionText'] = gd_number_figure($val['addPrice'] * $mileagePercent, $mileage['trunc']['unitPrecision'], $mileage['trunc']['unitRound']);
                }
            }

            // 개별 설정인 경우 마일리지 설정
        } else if ($getData['mileageFl'] == 'g') {
            $mileagePercent = $getData['mileageGoods'] / 100;

            // 상품 기본 마일리지 정보
            if ($getData['mileageGoodsUnit'] === 'percent') {
                $getData['mileageBasic'] = gd_number_figure($getData['goodsPrice'] * $mileagePercent, $mileage['trunc']['unitPrecision'], $mileage['trunc']['unitRound']);
            } else {
                // 정액인 경우 해당 설정된 금액으로
                $getData['mileageBasic'] = $getData['mileageGoods'];
            }

            // 상품 옵션 마일리지 정보
            if ($getData['optionFl'] === 'y') {
                foreach ($getData['option'] as $key => $val) {
                    if ($getData['mileageGoodsUnit'] === 'percent') {
                        $getData['option'][$key]['mileageOption'] = gd_number_figure($val['optionPrice'] * $mileagePercent, $mileage['trunc']['unitPrecision'], $mileage['trunc']['unitRound']);
                    } else {
                        // 정액인 경우 0 (상품 기본에만 있음)
                        $getData['option'][$key]['mileageOption'] = 0;
                    }
                }
            }

            // 추가 상품 마일리지 정보
            if ($getData['addGoodsFl'] === 'y' && empty($getData['addGoods']) === false && empty($getData['addGoodsGoodsNo']) === false) {
                foreach ($getData['addGoods'] as $key => $val) {
                    if ($getData['mileageGoodsUnit'] === 'percent') {
                        $getData['addGoods'][$key]['mileageAddGoods'] = gd_number_figure($val['goodsPrice'] * $mileagePercent, $mileage['trunc']['unitPrecision'], $mileage['trunc']['unitRound']);
                    } else {
                        // 정액인 경우 0 (상품 기본에만 있음)
                        $getData['addGoods'][$key]['mileageAddGoods'] = 0;
                    }
                }
            }

            // 상품 텍스트 옵션 마일리지 정보
            if ($getData['optionTextFl'] === 'y') {
                foreach ($getData['optionText'] as $key => $val) {
                    if ($getData['mileageGoodsUnit'] === 'percent') {
                        $getData['optionText'][$key]['mileageOptionText'] = gd_number_figure($val['addPrice'] * $mileagePercent, $mileage['trunc']['unitPrecision'], $mileage['trunc']['unitRound']);
                    } else {
                        // 정액인 경우 0 (상품 기본에만 있음)
                        $getData['optionText'][$key]['mileageOptionText'] = 0;
                    }
                }
            }
        } else {
            $getData['goodsMileageFl'] = 'n';
        }


        $getData['mileageConf'] = $mileage;

        //상품 가격 노출 관련
        $goodsPriceDisplayFl = gd_policy('goods.display')['priceFl'];
        $getData['goodsPriceDisplayFl'] = 'y';


        //상품별할인
        if ($getData['goodsDiscountFl'] == 'y') {
            if ($getData['goodsDiscountUnit'] == 'price') $getData['goodsDiscountPrice'] = $getData['goodsPrice'] - $getData['goodsDiscount'];
            else $getData['goodsDiscountPrice'] = $getData['goodsPrice'] - (($getData['goodsDiscount'] / 100) * $getData['goodsPrice']);
        }

        //회원관련
        if (gd_is_login() === true) {
            // 회원 그룹 설정
            $memberGroup = \App::load('\\Component\\Member\\MemberGroup');
            $getData['memberDc'] = $memberGroup->getGroupForSale($goodsNo, $getData['cateCd']);

            //회원 할인가
            if ($getData['memberDc'] && $getData['dcLine'] && $getData['dcPrice']) {
                $getData['memberDcPriceFl'] = 'y';
                if ($getData['memberDc']['dcType'] == 'price') $getData['memberDcPrice'] = $getData['memberDc']['dcPrice'];
                else $getData['memberDcPrice'] = (($getData['memberDc']['dcPercent'] / 100) * $getData['goodsPrice']);

            } else $getData['memberDcPriceFl'] = 'n';


            //회원 적립
            if ($getData['memberDc'] && $getData['mileageLine'] && $getData['mileageLine']) $getData['memberMileageFl'] = 'y';
            else $getData['memberMileageFl'] = 'n';

            //결제수한제단 체크
            if ($getData['payLimitFl'] == 'y' && gd_isset($getData['payLimit'])) {
                $getData['memberDc']['settleGb'] = Util::matchSettleGbDataToString($getData['memberDc']['settleGb']);
                $payLimit = array_intersect($getData['memberDc']['settleGb'], explode(STR_DIVISION, $getData['payLimit']));

                if(count($payLimit) == 0) {
                    $getData['orderPossible'] = 'n';
                }
            }

        } else {
            $getData['memberDcPriceFl'] = 'n';
            $getData['memberMileageFl'] = 'n';
        }

        // 구매 가능여부 체크
        if ($getData['soldOut'] == 'y') {
            $getData['orderPossible'] = 'n';
            if($goodsPriceDisplayFl =='n' && $soldoutDisplay['soldout_price'] !='price') $getData['goodsPriceDisplayFl'] = 'n';
        }

        //구매불가 대체 문구 관련
        if($getData['goodsPermission'] !='all' && (($getData['goodsPermission'] =='member'  && gd_is_login() === false) || ($getData['goodsPermission'] =='group'  && !in_array(Session::get('member.groupSno'),explode(INT_DIVISION,$getData['goodsPermissionGroup']))))) {
            if($getData['goodsPermissionPriceStringFl'] =='y' ) $getData['goodsPriceString'] = $getData['goodsPermissionPriceString'];
            $getData['orderPossible'] = 'n';
        }

        if (((gd_isset($getData['salesStartYmd']) != '' && gd_isset( $getData['salesEndYmd']) != '') && ($getData['salesStartYmd'] != '0000-00-00 00:00:00' && $getData['salesEndYmd'] != '0000-00-00 00:00:00')) && (strtotime($getData['salesStartYmd']) > time() || strtotime($getData['salesEndYmd']) < time())) {
            $getData['orderPossible'] = 'n';
        }

        if ($getData['goodsMileageFl'] == 'y' || $getData['memberMileageFl'] == 'y' || $getData['goodsDiscountFl'] == 'y' || $getData['memberDcPriceFl'] == 'y') {
            $getData['benefitPossible'] = 'y';
        } else $getData['benefitPossible'] = 'n';

        //판매기간 사용자 노출
        if (((gd_isset($getData['salesStartYmd']) != '' && gd_isset( $getData['salesEndYmd']) != '') && ($getData['salesStartYmd'] != '0000-00-00 00:00:00' && $getData['salesEndYmd'] != '0000-00-00 00:00:00'))) {
            $getData['salesData'] = $getData['salesStartYmd']." ~ ".$getData['salesEndYmd'];
        } else {
            $getData['salesData'] = __('제한없음');
        }

        // 관련 상품
        $getData['relation']['relationFl'] = $getData['relationFl'];
        $getData['relation']['relationCnt'] = $getData['relationCnt'];
        $getData['relation']['relationGoodsNo'] = $getData['relationGoodsNo'];
        $getData['relation']['cateCd'] = $getData['cateCd'];
        unset($getData['relationFl'], $getData['relationCnt'], $getData['relationGoodsNo']);

        // 상품 이용 안내
        $getData['detailInfo']['detailInfoDelivery'] = $getData['detailInfoDelivery'];
        $getData['detailInfo']['detailInfoAS'] = $getData['detailInfoAS'];
        $getData['detailInfo']['detailInfoRefund'] = $getData['detailInfoRefund'];
        $getData['detailInfo']['detailInfoExchange'] = $getData['detailInfoExchange'];
        unset($getData['detailInfoDelivery'], $getData['detailInfoAS'], $getData['detailInfoRefund'], $getData['detailInfoExchange']);

        // 가격 대체 문구가 있는 경우 주문금지
        if (empty($getData['goodsPriceString']) === false) {
            $getData['orderPossible'] = 'n';
            if($goodsPriceDisplayFl =='n') $getData['goodsPriceDisplayFl'] = 'n';
        }


        //최소구매수량 관련
        if ($getData['fixedSales'] != 'goods' && gd_isset($getData['salesUnit'], 0) > $getData['minOrderCnt']) {
            $getData['minOrderCnt'] = $getData['salesUnit'];
        }

        //초기상품수량
        $getData['goodsCnt'] = 1;
        if ($getData['fixedSales'] != 'goods') {
            if ($getData['salesUnit'] > 1) {
                $getData['goodsCnt'] = $getData['salesUnit'];
            } else {
                if ($getData['fixedOrderCnt'] == 'option') {
                    $getData['goodsCnt'] = $getData['minOrderCnt'];
                }
            }
        }

        //
        if (gd_is_plus_shop(PLUSSHOP_CODE_COMMONCONTENT) === true) {
            $commonContent = \App::load('\\Component\\Goods\\CommonContent');
            $getData['commonContent'] = $commonContent->getCommonContent($getData['goodsNo'], $getData['scmNo']);
        }

        //상품 재입고 노출여부
        if (gd_is_plus_shop(PLUSSHOP_CODE_RESTOCK) === true) {
            $getData['restockUsableFl'] = $this->setRestockUsableFl($getData);
        }

        // 재고량 체크
        $getData['stockCnt'] = $this->getOptionStock($goodsNo, null, $getData['stockFl'], $getData['soldOutFl']);

        $getData['multipleDeliveryFl'] = false;
        if ($getData['delivery']['basic']['fixFl'] != 'free' && $getData['delivery']['basic']['deliveryConfigType'] == 'etc' && (count($getData['delivery']['basic']['deliveryMethodFlData']) <= 1) === false) {
            $getData['multipleDeliveryFl'] = true;
        }

        //할인가 기본 세팅
        $getData['goodsDcPrice'] = $this->getGoodsDcPrice($getData);

        // 상품혜택관리 치환코드 생성
        $getData = $goodsBenefit->goodsDataFrontReplaceCode($getData);

        return $getData;
    }

    /**
     * 상품 정보 세팅
     *
     * @param string  $getData       상품정보
     * @param string  $imageType     이미지 타입
     * @param boolean $optionFl      옵션 출력 여부 - true or false (기본 false)
     * @param boolean $couponPriceFl 쿠폰가격 출력 여부 - true or false (기본 false)
     * @param integer $viewWidthSize 실제 출력할 이미지 사이즈 (기본 null)
     * @param array   $linkUrl 추적경로
     */
    protected function setGoodsListInfo(&$getData, $imageType, $optionFl = false, $couponPriceFl = false, $viewWidthSize = null, $viewName = null, $brandFl = false, $linkUrl = null)
    {
        $mallBySession = SESSION::get(SESSION_GLOBAL_MALL);

        // 이미지 타입에 따른 상품 이미지 사이즈
        if (empty($viewWidthSize) === true) {
            $imageSize = SkinUtils::getGoodsImageSize($imageType);
        } else {
            $imageSize['size1'] = $viewWidthSize;
        }

        // 세로사이즈고정 체크
        $imageConf = gd_policy('goods.image');
        if ($imageConf['imageType'] != 'fixed' || Request::isMobile()) {
            $imageSize['hsize1'] = '';
        }

        $strSQL = 'SELECT iconNm,iconImage,iconCd FROM ' . DB_MANAGE_GOODS_ICON .' WHERE iconUseFl = "y"';
        $tmpIcon = $this->db->slave()->query_fetch($strSQL);
        foreach($tmpIcon as $k => $v ) {
            $setIcon[$v['iconCd']]['iconImage'] = $v['iconImage'];
            $setIcon[$v['iconCd']]['iconNm'] = $v['iconNm'];
        }

        /* 이미지 설정 리스트이미지효과 사용시 여러이미지 한번에 가져옴*/
        if(gd_is_plus_shop(PLUSSHOP_CODE_LISTMOUSEOVER) === true) {
            $bindQuery = $arrBind = null;
            foreach(array_column($getData, 'goodsNo') as $val){
                $bindQuery[] = '?';
                $this->db->bind_param_push($arrBind,'i',$val);
            }
            $strImageSQL = 'SELECT goodsNo,imageName,imageKind FROM ' . DB_GOODS_IMAGE . ' g  WHERE imageNo= 0  AND goodsNo IN ('.implode(',',$bindQuery).')';

        } else {
            $bindQuery = $arrBind = null;
            $this->db->bind_param_push($arrBind,'s',$imageType);
            foreach(array_column($getData, 'goodsNo') as $val){
                $bindQuery[] = '?';
                $this->db->bind_param_push($arrBind,'i',$val);
            }
            $strImageSQL = 'SELECT goodsNo,imageName,imageKind FROM ' . DB_GOODS_IMAGE . ' g  WHERE imageKind = ?  AND goodsNo IN ('.implode(',',$bindQuery).')';
        }
        //        debug($strImageSQL);
        //        debug($arrBind,true);
        $tmpImageData = $this->db->slave()->query_fetch($strImageSQL,$arrBind);
        foreach($tmpImageData as $k => $v) {
            $imageData[$v['goodsNo']][$v['imageKind']] = stripslashes($v['imageName']);
        }
        unset($tmpImageData);

        if($brandFl) {
            $bindQuery = $arrBind = null;
            foreach(array_column($getData, 'brandCd') as $val){
                $bindQuery[] = '?';
                $this->db->bind_param_push($arrBind,'s',$val);
            }
            $strSQLGlobal = "SELECT cateNm,cateCd FROM ".DB_CATEGORY_BRAND."  WHERE cateCd IN (".implode(",",$bindQuery).")";
            $tmpData = $this->db->query_fetch($strSQLGlobal,$arrBind);
            $brandData = array_combine(array_column($tmpData, 'cateCd'), $tmpData);
        }

        if($mallBySession) {
            $arrFieldGoodsGlobal = DBTableField::setTableField('tableGoodsGlobal',null,['mallSno']);
            $bindQuery = $arrBind = null;
            foreach(array_column($getData, 'goodsNo') as $val){
                $bindQuery[] = '?';
                $this->db->bind_param_push($arrBind,'i',$val);
            }
            $strSQLGlobal = "SELECT gg." . implode(', gg.', $arrFieldGoodsGlobal) . " FROM ".DB_GOODS_GLOBAL." as gg WHERE gg.goodsNo IN (".implode(",",$bindQuery).") AND gg.mallSno = ".$mallBySession['sno'];
            $tmpData = $this->db->query_fetch($strSQLGlobal,$arrBind);
            $globalData = array_combine(array_column($tmpData, 'goodsNo'), $tmpData);

            if($brandFl) {
                //브랜드정보
                $bindQuery = $arrBind = null;
                foreach(array_column($getData, 'brandCd') as $val){
                    $bindQuery[] = '?';
                    $this->db->bind_param_push($arrBind,'s',$val);
                }
                $strSQLGlobal = "SELECT cateNm,cateCd FROM ".DB_CATEGORY_BRAND_GLOBAL."  WHERE cateCd IN (".implode(",",$bindQuery).") AND mallSno = ".$mallBySession['sno'];
                $tmpData = $this->db->query_fetch($strSQLGlobal, $arrBind);
                $brandGlobalData = array_combine(array_column($tmpData, 'cateCd'), $tmpData);
            }
        }

        // 마일리지 처리
        $mileage = gd_mileage_give_info();

        // 쿠폰 설정값 정보
        $couponConfig = gd_policy('coupon.config');

        //품절상품 설정
        if(Request::isMobile()) {
            $soldoutDisplay = gd_policy('soldout.mobile');
        } else {
            $soldoutDisplay = gd_policy('soldout.pc');
        }

        //쿠폰 관련
        if($couponConfig['couponUseType'] == 'y') {
            $coupon = \App::load('\\Component\\Coupon\\Coupon');
            $couponArrData = $coupon->getGoodsCouponDownListAll();
        }

        //상품 가격 노출 관련
        $goodsPriceDisplayFl = gd_policy('goods.display')['priceFl'];

        $GoodsBenefit = \App::load('\\Component\\Goods\\GoodsBenefit');

        // 아이콘 출력 및 옵션 출력 여부
        foreach ($getData as $key => &$val) {

            // 아이콘 테이블 분리로 인한 추가
            $tmpGoodsIcon = [];
            $iconList = $this->getGoodsDetailIcon($val['goodsNo']);
            foreach ($iconList as $iconKey => $iconVal) {
                if ($iconVal['iconKind'] == 'pe') {
                    if (empty($iconVal['goodsIconStartYmd']) === false && empty($iconVal['goodsIconEndYmd']) === false && empty($iconVal['goodsIconCd']) === false && strtotime($iconVal['goodsIconStartYmd']) <= time() && strtotime($iconVal['goodsIconEndYmd']) >= time()) {
                        $tmpGoodsIcon[] = $iconVal['goodsIconCd'];
                    }
                }

                if ($iconVal['iconKind'] == 'un') {
                    $tmpGoodsIcon[] = $iconVal['goodsIconCd'];
                }
            }

            $val['goodsIconCd'] = implode(INT_DIVISION,$tmpGoodsIcon);

            //상품혜택 적용
            $val = $GoodsBenefit->goodsDataFrontConvert($val);

            $setMileageGiveFl = $mileage['give']['giveFl'];
            $setCouponUseType = $couponConfig['couponUseType'];
            $exceptBenefit = explode(STR_DIVISION, $val['exceptBenefit']);
            $exceptBenefitGroupInfo = explode(INT_DIVISION, $val['exceptBenefitGroupInfo']);

            // 제외 혜택 대상 여부
            $exceptBenefitFl = false;
            if ($val['exceptBenefitGroup'] == 'all' || ($val['exceptBenefitGroup'] == 'group' && in_array(Session::get('member.groupSno'), $exceptBenefitGroupInfo) === true)) {
                $exceptBenefitFl = true;
            }

            $val['imageName'] = $imageData[$val['goodsNo']][$imageType];
            if($brandFl && $brandData) {
                $val['brandNm'] = $brandData[$val['brandCd']]['cateNm'];
            }

            // 상품 url 추가
            $val['goodsUrl'] = '../goods/goods_view.php?goodsNo=' . $val['goodsNo'];

            if($mallBySession) {
                if($globalData[$val['goodsNo']]) {
                    $val = array_replace_recursive($val, array_filter(array_map('trim',$globalData[$val['goodsNo']])));
                }

                if($brandFl && $brandGlobalData[$val['brandCd']]) {
                    $val['brandNm'] = $brandGlobalData[$val['brandCd']]['cateNm'];
                }
            }

            // 상품 url 추가
            if (gd_isset($viewName) && $viewName == 'main') {
                $linkUrlVal = htmlentities(urlencode($linkUrl['mainThemeSno'] . STR_DIVISION . $linkUrl['mainThemeNm'] . STR_DIVISION . $linkUrl['mainThemeDevice']));
                $val['goodsUrl'] = '../goods/goods_view.php?goodsNo=' . $val['goodsNo'] . '&mtn=' . $linkUrlVal;
            } else {
                $val['goodsUrl'] = '../goods/goods_view.php?goodsNo=' . $val['goodsNo'];
            }

            $val['oriGoodsPrice'] = $val['goodsPrice'];

            //구매불가 대체 문구 관련
            if($val['goodsPermissionPriceStringFl'] =='y' && $val['goodsPermission'] !='all' && (($val['goodsPermission'] =='member'  && gd_is_login() === false) || ($val['goodsPermission'] =='group'  && !in_array(Session::get('member.groupSno'),explode(INT_DIVISION,$val['goodsPermissionGroup']))))) {
                $val['goodsPriceString'] = $val['goodsPermissionPriceString'];
            }

            /* 타임 세일 관련 */
            $val['timeSaleFl'] = false;
            $val['cssTimeSaleIcon'] = '';
            if (gd_is_plus_shop(PLUSSHOP_CODE_TIMESALE) === true) {
                //eachBenefit추가 eachBenefitFindNo추가
                $strScmSQL = 'SELECT ts.mileageFl as timeSaleMileageFl,ts.couponFl as timeSaleCouponFl,ts.benefit as timeSaleBenefit,ts.eachBenefit as timeSaleEachBenefit,FIND_IN_SET('.$val['goodsNo'].', REPLACE(ts.goodsNo,"'.INT_DIVISION.'",",")) as eachBenefitFindNo,ts.sno as timeSaleSno,ts.goodsPriceViewFl as timeSaleGoodsPriceViewFl, ts.endDt as timeSaleEndDt, ts.leftTimeDisplayType, ts.pcDisplayFl as timeSalePC, ts.mobileDisplayFl as timeSaleMobile FROM ' . DB_TIME_SALE .' as ts WHERE FIND_IN_SET('.$val['goodsNo'].', REPLACE(ts.goodsNo,"'.INT_DIVISION.'",",")) AND UNIX_TIMESTAMP(ts.startDt) < UNIX_TIMESTAMP() AND  UNIX_TIMESTAMP(ts.endDt) > UNIX_TIMESTAMP() ';
                $tmpScmData = $this->db->query_fetch($strScmSQL,null,false);
                // ||기준 순서대로 계산
                
                $mEachBenefit = explode('||',$tmpScmData['timeSaleEachBenefit']);
                $findnum = ($tmpScmData['eachBenefitFindNo']-1);
                $tmpScmData['timeSaleEachBenefit'] = $mEachBenefit[$findnum];
                
                if($tmpScmData) {
                    //타임세일 노출 여부 (디바이스에 따라)
                    $tmpTimeSaleFl = false;
                    if ($tmpScmData['timeSalePC'] === 'y' && !Request::isMobile()) {
                        $tmpTimeSaleFl = true;
                    }
                    if ($tmpScmData['timeSaleMobile'] === 'y' && Request::isMobile()) {
                        $tmpTimeSaleFl = true;
                    }

                    if ($tmpTimeSaleFl === true) {
                        //PC or Mobile 노출에 따라 타임세일 출력.
                        $val = $val + $tmpScmData;
                        if($val['timeSaleMileageFl'] =='n') $setMileageGiveFl = "n";
                        if($val['timeSaleCouponFl'] =='n') $setCouponUseType  = "n";
                        $val['timeSaleFl'] = true;

                        //계산식 변경
                        if($val['goodsPrice'] > 0 ){
                            if($val['timeSaleEachBenefit']){
                                $val['goodsPrice'] =  gd_number_figure($val['goodsPrice'] - (($val['timeSaleEachBenefit'] / 100) * $val['goodsPrice']), $this->trunc['unitPrecision'], $this->trunc['unitRound']);
                            }else{
                                $val['goodsPrice'] =  gd_number_figure($val['goodsPrice'] - (($val['timeSaleBenefit'] / 100) * $val['goodsPrice']), $this->trunc['unitPrecision'], $this->trunc['unitRound']);
                            }
                            
                        }

                        //타임 세일 남은 기간 노출 (분 단위)
                        //노출 설정이 되어있는 경우에만 노출 (PC/MOBILE)
                        $val['cssTimeSaleIcon'] = 'time_sale_cost';
                        $tmpLeftTimeDisplayFl = false;
                        if (strpos($val['leftTimeDisplayType'], 'PC') !== FALSE && !Request::isMobile()) {
                            $tmpLeftTimeDisplayFl = true;
                        }
                        if (strpos($val['leftTimeDisplayType'], 'MOBILE') !== FALSE && Request::isMobile()) {
                            $tmpLeftTimeDisplayFl = true;
                        }
                        if ($tmpLeftTimeDisplayFl === true) {
                            $tmpBeforeDay = DateTimeUtils::dateFormat('Y-m-d G:i:s', 'now');
                            $tmpAfterDay = $val['timeSaleEndDt'];
                            $tmpTimeSaleLeftTime = DateTimeUtils::intervalDay($tmpBeforeDay, $tmpAfterDay, 'min');
                            $val['timeSaleLeftTime'] = $tmpTimeSaleLeftTime;

                            //60분 이상일 경우 일 or 시 단위로 표현.
                            $tmpLeftTimeByHour = round($tmpTimeSaleLeftTime / 60);
                            if ($tmpTimeSaleLeftTime >= (60 * 24) || $tmpLeftTimeByHour == 24) { //24시간은 1일료 표현
                                $tmpLeftTime = round($tmpTimeSaleLeftTime / (60 * 24));
                                $val['timeSaleLeftTimeTxt'] = __('%s일 남음', $tmpLeftTime);
                            } else if ($tmpTimeSaleLeftTime >= 60) {
                                $tmpLeftTime = $tmpLeftTimeByHour;
                                $val['timeSaleLeftTimeTxt'] = __('%s시간 남음', $tmpLeftTime);
                            } else {
                                $tmpLeftTime = ($tmpTimeSaleLeftTime == 0) ? 1 : $tmpTimeSaleLeftTime; //0분은 1분으로 표현
                                $val['timeSaleLeftTimeTxt'] = __('%s분 남음', $tmpLeftTime);
                            }

                            //타임세일 남은 기간 문구 노출할 경우, 기존 아이콘 노출 안하도록 css 변경.
                            $val['cssTimeSaleIcon'] = 'time_sale_cost_r';
                        }
                    }
                }
                unset($tmpScmData);
                unset($strScmSQL);
            }

            // 아이콘 테이블 분리로 인한 추가
            unset($tmpGoodsIcon);
            $tmpGoodsIcon = explode(INT_DIVISION, $val['goodsIconCd']);
            if($tmpGoodsIcon) {
                $tmpGoodsIcon = ArrayUtils::removeEmpty($tmpGoodsIcon); // 빈 배열 정리

                foreach($tmpGoodsIcon  as $iKey => $iVal) {
                    if (isset($setIcon[$iVal])) {
                        $icon = UserFilePath::icon('goods_icon', $setIcon[$iVal]['iconImage']);
                        if ($icon->isFile()) {
                            $val['goodsIcon'] .= gd_html_image($icon->www(), $setIcon[$iVal]['iconNm']) . ' ';
                        }
                    }
                }
            }

            $val['goodsBenefitIcon'] = '';
            if(empty($val['goodsBenefitIconCd']) === false) {
                $tmpGoodsBenefitIcon = explode(INT_DIVISION, $val['goodsBenefitIconCd']);
                foreach($tmpGoodsBenefitIcon  as $iKey => $iVal) {
                    if (isset($setIcon[$iVal])) {
                        $icon = UserFilePath::icon('goods_icon', $setIcon[$iVal]['iconImage']);
                        if ($icon->isFile()) {
                            $val['goodsBenefitIcon'] .= gd_html_image($icon->www(), $setIcon[$iVal]['iconNm']) . ' ';
                        }
                    }
                }
            }
            //상품혜택 아이콘 가장먼저 노출
            $val['goodsIcon'] = $val['goodsBenefitIcon'].$val['goodsIcon'];

            // 옵션 출력 및 옵션의 마일리지 처리
            if ($optionFl === true && empty($val['optionName']) === false) {
                $val['optionValue'] = $this->getGoodsOptionValue($val['goodsNo']);
            }

            if($setMileageGiveFl =='y' ) {
                $val['goodsMileageFl'] = 'y';
                //상품 마일리지
                if ($val['mileageFl'] == 'c') {
                    // 마일리지 지급 여부
                    $mileageGiveFl = true;
                    if ($val['mileageGroup'] == 'group') { //마일리지 지급대상(특정회원등급)
                        $mileageGroupInfoData = explode(INT_DIVISION, $val['mileageGroupInfo']);

                        $mileageGiveFl = in_array(Session::get('member.groupSno'), $mileageGroupInfoData);
                    }

                    if ($mileageGiveFl === true) {
                        if ($mileage['give']['giveType'] == 'priceUnit') { // 금액 단위별
                            $mileagePrice = floor($val['goodsPrice'] / $mileage['give']['goodsPriceUnit']);
                            $val['mileageBasicGoods'] = gd_number_figure($mileagePrice * $mileage['give']['goodsMileage'], $mileage['trunc']['unitPrecision'], $mileage['trunc']['unitRound']);
                        } else if ($mileage['give']['giveType'] == 'cntUnit') { // 수량 단위별 (추가상품수량은 제외)
                            $val['mileageBasicGoods'] = gd_number_figure($mileage['give']['cntMileage'], $mileage['trunc']['unitPrecision'], $mileage['trunc']['unitRound']);
                        } else { // 구매금액의 %
                            $mileagePercent = $mileage['give']['goods'] / 100;
                            $val['mileageBasicGoods'] = gd_number_figure($val['goodsPrice'] * $mileagePercent, $mileage['trunc']['unitPrecision'], $mileage['trunc']['unitRound']);
                        }
                    }
                    // 개별 설정인 경우 마일리지 설정
                } else if ($val['mileageFl'] == 'g') {
                    if ($val['mileageGroup'] == 'group') { //마일리지 지급대상(특정회원등급)
                        $mileageGroupMemberInfoData = json_decode($val['mileageGroupMemberInfo'], true);
                        $mileageKey = array_flip($mileageGroupMemberInfoData['groupSno'])[Session::get('member.groupSno')];
                        if ($mileageKey >= 0) {
                            $mileagePercent = $mileageGroupMemberInfoData['mileageGoods'][$mileageKey] / 100;
                            if ($val['mileageGoodsUnit'] === 'percent') {
                                // 상품 마일리지
                                $val['mileageBasicGoods'] = gd_number_figure($val['goodsPrice'] * $mileagePercent, $mileage['trunc']['unitPrecision'], $mileage['trunc']['unitRound']);
                            } else {
                                // 상품 마일리지 (정액인 경우 해당 설정된 금액으로)
                                $val['mileageBasicGoods'] = $mileageGroupMemberInfoData['mileageGoods'][$mileageKey];
                            }
                        }
                    } else {
                        $mileagePercent = $val['mileageGoods'] / 100;

                        // 상품 기본 마일리지 정보
                        if ($val['mileageGoodsUnit'] === 'percent') {
                            $val['mileageBasicGoods'] = gd_number_figure($val['goodsPrice'] * $mileagePercent, $mileage['trunc']['unitPrecision'], $mileage['trunc']['unitRound']);
                        } else {
                            // 정액인 경우 해당 설정된 금액으로
                            $val['mileageBasicGoods'] = gd_number_figure($val['mileageGoods'], $mileage['trunc']['unitPrecision'], $mileage['trunc']['unitRound']);
                        }
                    }

                }

                // 회원 그룹별 추가 마일리지
                if ($this->_memInfo['mileageLine'] <= $val['goodsPrice']) {
                    if (in_array('mileage', $exceptBenefit) === true && $exceptBenefitFl === true) {} else {
                        if ($this->_memInfo['mileageType'] === 'percent') {
                            $memberMileagePercent = $this->_memInfo['mileagePercent'] / 100;
                            $val['mileageBasicMember'] = gd_number_figure($val['goodsPrice'] * $memberMileagePercent, $mileage['trunc']['unitPrecision'], $mileage['trunc']['unitRound']);
                        } else {
                            $val['mileageBasicMember'] = $this->_memInfo['mileagePrice'];
                        }
                    }
                }

                $val['mileageBasic'] = $val['mileageBasicGoods'] + $val['mileageBasicMember'];

            } else {
                $val['goodsMileageFl'] = 'n';

            }

            // 쿠폰가 회원만 노출
            if ($couponConfig['couponDisplayType'] == 'member') {
                if (gd_check_login()) {
                    $couponPriceYN = true;
                } else {
                    $couponPriceYN = false;
                }
            } else {
                $couponPriceYN = true;
            }

            // 혜택제외 체크 (쿠폰)
            $exceptBenefit = explode(STR_DIVISION, $val['exceptBenefit']);
            $exceptBenefitGroupInfo = explode(INT_DIVISION, $val['exceptBenefitGroupInfo']);
            if (in_array('coupon', $exceptBenefit) === true && ($val['exceptBenefitGroup'] == 'all' || ($val['exceptBenefitGroup'] == 'group' && in_array(Session::get('member.groupSno'), $exceptBenefitGroupInfo) === true))) {
                $couponPriceYN = false;
            }

            // 쿠폰 할인 금액
            if ($setCouponUseType == 'y' && $couponPriceYN && $val['goodsPrice'] > 0 && empty($val['goodsPriceString']) === true) {
                // 쿠폰검색에 필요한 전체 카테고리 체크
                $tmpCateArr = $this->getGoodsLinkCategory($val['goodsNo']);
                if (is_array($tmpCateArr)) {
                    $val['cateCdArr'] = array_column($tmpCateArr, 'cateCd');
                }
                unset($tmpCateArr);

                //쿠폰가 계산
                $couponSalePrice = $coupon->getGoodsCouponDownListPrice($val,$couponArrData);
                $val['couponDcPrice'] = $couponSalePrice;
                if ($couponSalePrice) {
                    $val['couponPrice'] = $val['goodsPrice'] - $couponSalePrice;
                    if ($val['couponPrice'] < 0) {
                        $val['couponPrice'] = 0;
                    }
                }
            }

            // 상품 이미지 처리
            if ($val['onlyAdultFl'] == 'y' && gd_check_adult() === false && $val['onlyAdultImageFl'] =='n') {
                if (Request::isMobile()) {
                    $val['goodsImageSrc'] = "/data/icon/goods_icon/only_adult_mobile.png";
                } else {
                    $val['goodsImageSrc'] = "/data/icon/goods_icon/only_adult_pc.png";
                }

                $val['goodsImage'] = SkinUtils::makeImageTag($val['goodsImageSrc'], $imageSize['size1']);
            } else {
                $val['goodsImage'] = gd_html_preview_image($val['imageName'], $val['imagePath'], $val['imageStorage'], $imageSize['size1'], 'goods', $val['goodsNm'], null, false, true, $imageSize['hsize1'],null,$this->goodsImageLazyFl);
                $val['goodsImageSrc'] = SkinUtils::imageViewStorageConfig($val['imageName'], $val['imagePath'], $val['imageStorage'], $imageSize['size1'], 'goods')[0];
            }

            // 상품명
            if (gd_isset($viewName) && $viewName == 'main') {
                $val['goodsNm'] = $this->getGoodsName($val['goodsNmMain'], $val['goodsNm'], $val['goodsNmFl']);
            } else {
                $val['goodsNm'] = $this->getGoodsName($val['goodsNmList'], $val['goodsNm'], $val['goodsNmFl']);
            }

            //기본적으로 가격 노출함
            $val['goodsPriceDisplayFl'] = 'y';

            // 가격 대체 문구가 있는 경우 주문금지
            if (empty($val['goodsPriceString']) === false) {
                $val['orderPossible'] = 'n';
                if($goodsPriceDisplayFl =='n') $val['goodsPriceDisplayFl'] = 'n';
            }

            // 구매 가능여부 체크
            if ($val['soldOut'] == 'y') {
                $val['orderPossible'] = 'n';
                if($goodsPriceDisplayFl =='n' && $soldoutDisplay['soldout_price'] !='price') $val['goodsPriceDisplayFl'] = 'n';
            }

            // 정렬을 위한 필드가 있는 경우 삭제처리
            if (isset($val['sort'])) {
                unset($val['sort']);
            }

            // 재고량 체크
            $val['stockCnt'] = $this->getOptionStock($val['goodsNo'], null, $val['stockFl'], $val['soldOutFl']);

            //할인가 기본 세팅
            $val['goodsDcPrice'] = $this->getGoodsDcPrice($val);

            // 상품혜택관리 치환코드 생성
            $val = $GoodsBenefit->goodsDataFrontReplaceCode($val, 'goodsList');

            // 상품 대표색상 치환코드 추가
            $goodsColorList = $this->getGoodsColorList(true);
            $goodsColorWidth = $imageSize['size1'] - 10;
            $goodsColor = (Request::isMobile()) ? "<div class='color_chip'>" : "<div class='color' style='width: ".$goodsColorWidth."px'>";
            if($val['goodsColor']) $val['goodsColor'] = explode(STR_DIVISION, $val['goodsColor']);

            if(is_array($val['goodsColor'])) {
                foreach(array_unique($val['goodsColor']) as $k => $v) {
                    if (!in_array($v,$goodsColorList) ) {
                        continue;
                    }
                    $goodsColorData = array_flip($goodsColorList)[$v];
                    $goodsColor .= ($v == 'FFFFFF') ? "<div style='background-color:#{$v};' title='{$goodsColorData}'></div>" : "<div style='background-color:#{$v}; border-color:#{$v};' title='{$goodsColorData}'></div>";
                }
                $goodsColor .= "</div>";
                unset($val['goodsColor']);
                $val['goodsColor'] = $goodsColor;
            }

            if (in_array('goodsDiscount', $this->themeConfig['displayField']) === true && empty($val['goodsPriceString']) === true) {
                if (empty($this->themeConfig['goodsDiscount']) === false) {
                    if (in_array('goods', $this->themeConfig['goodsDiscount']) === true) $val['dcPrice'] += $val['goodsDcPrice'];
                    if (in_array('coupon', $this->themeConfig['goodsDiscount']) === true) $val['dcPrice'] += $val['couponDcPrice'];
                }
            }

            if ($val['dcPrice'] >= $val['goodsPrice']) {
                $val['dcPrice'] = 0;
            }

            if (in_array('dcRate', $this->themeConfig['displayAddField']) === true) {
                $val['goodsDcRate'] = round((100 * gd_isset($val['dcPrice'], 0)) / $val['goodsPrice']);
                $val['couponDcRate'] = round((100 * $val['couponDcPrice']) / $val['goodsPrice']);
            }

            try {
                if (($val['onlyAdultFl'] == 'y' && gd_check_adult() === false) === false && $imageData[$val['goodsNo']] && gd_is_plus_shop(PLUSSHOP_CODE_LISTMOUSEOVER) === true) {
                    foreach ($imageData[$val['goodsNo']] as $imageKey => $imageValue) {
                        $retData[] = 'data-image-' . $imageKey . ' = "' . SkinUtils::imageViewStorageConfig($imageValue, $val['imagePath'], $val['imageStorage'], $imageSize['size1'], 'goods')[0] . '"';
                    }
                    $val['goodsData'] = implode($retData, ' ');
                    unset($retData);
                }
            } catch (\Exception $e) {}

            // 필요없는 변수 처리
            unset($val['imageStorage'], $val['imagePath'], $val['imageName'], $val['mileageFl']);
        }
    }
    
}