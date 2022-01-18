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
    public function goodsDataDisplay2($getMethod = 'all', $extractKey = null, $displayCnt = 10, $displayOrder = 'sort asc', $imageType = 'main', $optionFl = false, $soldOutFl = true, $brandFl = false, $couponPriceFl = false, $viewWidthSize = null, $usePage = false, $goodsNo = null)
    {
        $mallBySession = SESSION::get(SESSION_GLOBAL_MALL);

        $where = [];
        $join = [];
        $arrBind = [];
        $sortField = '';
        $viewName = "";

        // --- 상품 추출 방법에 따른 처리
        switch ($getMethod) {

            // --- 모든 상품
            case 'all':

                // 정렬 처리
                if ($displayOrder == 'sort asc') {
                    $displayOrder = 'g.goodsNo desc';
                } else if ($displayOrder == 'sort desc') {
                    $displayOrder = 'g.goodsNo asc';
                }

                break;

            // --- 카테고리
            case 'category':

                // 카테고리 코드가 없는 경우 리턴
                if (is_null($extractKey)) {
                    return;
                }

                // 정렬 처리
                if ($displayOrder == 'sort asc') {
                    $displayOrder = 'gl.goodsSort desc';
                } else if ($displayOrder == 'sort desc') {
                    $displayOrder = 'gl.goodsSort asc';
                }

                $this->db->bind_param_push($arrBind, 's', $extractKey);
                $join[] = ' INNER JOIN ' . DB_GOODS_LINK_CATEGORY . ' gl ON g.goodsNo = gl.goodsNo ';
                $where[] = 'gl.cateCd = ?';

                break;

            // --- 브랜드
            case 'brand':

                // 카테고리 코드가 없는 경우 리턴
                if (is_null($extractKey)) {
                    return;
                }

                // 정렬 처리
                if ($displayOrder == 'sort asc') {
                    $displayOrder = 'gl.goodsSort desc';
                } else if ($displayOrder == 'sort desc') {
                    $displayOrder = 'gl.goodsSort asc';
                }

                $this->db->bind_param_push($arrBind, 's', $extractKey);
                $join[] = ' INNER JOIN ' . DB_GOODS_LINK_BRAND . ' gl ON g.goodsNo = gl.goodsNo ';
                $where[] = 'gl.cateCd = ?';

                break;

            // --- 상품테마
            case 'theme':

                // 상품테마 코드가 없는 경우 리턴
                if (is_null($extractKey)) {
                    return;
                }

                // 상품 테마 테이타
                $data = $this->getDisplayThemeInfo($extractKey);

                // 데이타가 없으면 리턴
                if (empty($data['goodsNo'])) {
                    return;
                }

                // 쿼리 생성
                $queryData = $this->setGoodsListQueryForGoodsno($data['goodsNo'], $displayOrder, $displayCnt, $arrBind);
                $sortField = $queryData['sortField'];
                $where[] = $queryData['where'];
                unset($queryData);

                break;

            // --- 관련 상품
            case 'relation_a':
            case 'relation_m':

                // 코드(카테고리 코드 및 상품 코드)가 없는 경우 리턴
                if (is_null($extractKey)) {
                    return;
                }

                $relationMode = explode('_', $getMethod);


                // 자동인 경우
                if ($relationMode[1] == 'a') {

                    // 정렬 설정
                    $displayOrder = 'rand()';

                    // 관련 상품 출력 갯수 체크
                    if (is_null($displayCnt)) {
                        return;
                    }
                    $this->db->bind_param_push($arrBind, 's', $extractKey);
                    $join[] = ' INNER JOIN (SELECT g.goodsNo FROM '.DB_GOODS_LINK_CATEGORY.' glc INNER JOIN '.DB_GOODS.' g ON glc.goodsNo=g.goodsNo AND g.delFl=\'n\' WHERE glc.cateCd=? limit 0,1000) gl ON g.goodsNo = gl.goodsNo ';

                    // 수동인 경우
                } else if ($relationMode[1] == 'm') {

                    // 쿼리 생성
                    $queryData = $this->setGoodsListQueryForGoodsno($extractKey, $displayOrder, $displayCnt, $arrBind);
                    $sortField = $queryData['sortField'];
                    $where[] = $queryData['where'];
                    unset($queryData);
                }

                break;

            // --- 상품 번호별 출력
            case 'goods':
                
                $wibPage = Request::get()->get('page');
                
                if ($usePage === true) {
                    $page = \App::load('\\Component\\Page\\Page', $wibPage);
                    $page->page['list'] = $displayCnt; // 페이지당 리스트 수
                    $page->block['cnt'] = !Request::isMobile() ? 10 : 5; // 블록당 리스트 개수
                    $page->setPage();
                    $page->setUrl(\Request::getQueryString());
                }


                // 상품 코드가 없는 경우 리턴
                if (is_null($extractKey)) {
                    return;
                }


                $viewName = "main";

                // 쿼리 생성
                $queryData = $this->setGoodsListQueryForGoodsno($extractKey, $displayOrder, $displayCnt, $arrBind);
                $sortField = gd_isset($queryData['sortField']);
                $where[] = $queryData['where'];
                unset($queryData);

                break;

            // --- 상품 번호별 출력
            case 'event':

                $tmpKey = explode(MARK_DIVISION, $extractKey);

                // 상품 코드가 없는 경우 리턴
                if (is_null($tmpKey[0])) {
                    return;
                }

                $arrGoodsNo = explode(STR_DIVISION, $tmpKey[0]);
                $displayCnt = count($arrGoodsNo);

                // 쿼리 생성
                $queryData = $this->setGoodsListQueryForGoodsno($tmpKey[0], $displayOrder, $displayCnt, $arrBind);
                $sortField = gd_isset($queryData['sortField']);
                $where[] = $queryData['where'];

                if (empty($tmpKey[1]) === false) {
                    $this->db->bind_param_push($arrBind, 's', $tmpKey[1]);
                    $where[] = 'g.cateCd LIKE concat(?,\'%\')';
                }
                if (empty($tmpKey[2]) === false) {
                    $this->db->bind_param_push($arrBind, 's', $tmpKey[2]);
                    $where[] = 'g.brandCd LIKE concat(?,\'%\')';
                }
                unset($queryData);

                break;

            // 그외는 리턴
            default:
                return;
                break;
        }

        // 품절 처리 여부
        if ($soldOutFl === false) {
            $where[] = 'NOT(g.stockFl = \'y\' AND g.totalStock = 0) AND NOT(g.soldOutFl = \'y\')';
        }


        //접근권한 체크
        if (gd_check_login()) {
            $where[] = '(g.goodsAccess !=\'group\'  OR (g.goodsAccess=\'group\' AND FIND_IN_SET(\''.Session::get('member.groupSno').'\', REPLACE(g.goodsAccessGroup,"'.INT_DIVISION.'",","))) OR (g.goodsAccess=\'group\' AND !FIND_IN_SET(\''.Session::get('member.groupSno').'\', REPLACE(g.goodsAccessGroup,"'.INT_DIVISION.'",",")) AND g.goodsAccessDisplayFl =\'y\'))';
        } else {
            $where[] = '(g.goodsAccess=\'all\' OR (g.goodsAccess !=\'all\' AND g.goodsAccessDisplayFl =\'y\'))';
        }

        //성인인증안된경우 노출체크 상품은 노출함
        if (gd_check_adult() === false) {
            $where[]= '(onlyAdultFl = \'n\' OR (onlyAdultFl = \'y\' AND onlyAdultDisplayFl = \'y\'))';
        }


        // 출력 여부
        $where[] = 'g.' . $this->goodsDisplayFl . ' = \'y\'';
        $where[] = 'g.delFl = \'n\'';
        $where[] = 'g.applyFl = \'y\'';
        $where[] = '(UNIX_TIMESTAMP(g.goodsOpenDt) IS NULL  OR UNIX_TIMESTAMP(g.goodsOpenDt) = 0 OR UNIX_TIMESTAMP(g.goodsOpenDt) < UNIX_TIMESTAMP())';

        if(strpos($displayOrder, "goodsNo") === false) $displayOrder = $displayOrder.', goodsNo desc ';
        if(strpos($displayOrder, "soldOut") !== false) $addField = ",( if (g.soldOutFl = 'y' , 'y', if (g.stockFl = 'y' AND g.totalStock <= 0, 'y', 'n') ) ) as soldOut";

        if ($usePage === true) {
            $this->db->strLimit = $page->recode['start'] . ',' . $displayCnt;
        } else {
            if (is_null($displayCnt) === false) {
                $this->db->strLimit = '0, ' . $displayCnt;
            }
        }

        // 상품 데이타 처리
        $this->setGoodsListField(); // 상품 리스트용 필드
        $this->db->strField = $this->goodsListField . gd_isset($addField) . $sortField;
        $this->db->strJoin = implode('', $join);
        $this->db->strWhere = implode(' AND ', $where);
        $this->db->strOrder = $displayOrder;

        if($getMethod =='relation_a') {
            $goodsNo = ($goodsNo) ? $goodsNo : null;
            $getData = $this->getGoodsAutoRelation($goodsNo, null, $arrBind, true, $getMethod);
        } else {
            $getData = $this->getGoodsInfo(null, null, $arrBind, true, $usePage);
        }

        if (empty($getData)) {
            return;
        }
        // 상품 정보 세팅
        if (empty($getData) === false) {
            $this->setGoodsListInfo($getData, $imageType, $optionFl, $couponPriceFl, $viewWidthSize, $viewName,$brandFl);
        }

        return gd_htmlspecialchars_stripslashes($getData);
    }
}