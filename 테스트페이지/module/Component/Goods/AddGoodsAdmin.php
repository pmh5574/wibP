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

use Component\Storage\Storage;
use Component\Database\DBTableField;
use Component\Validator\Validator;
use Exception;
use Globals;
use LogHandler;
use Request;
use UserFilePath;
use Session;
use Framework\Debug\Exception\AlertBackException;

/**
 * 추가 상품 관련 관리자 클래스
 * @author Jung Youngeun <atomyang@godo.co.kr>
 */
class AddGoodsAdmin extends \Bundle\Component\Goods\AddGoodsAdmin
{
    public function getAdminListAddGoods()
    {
        $getValue = Request::get()->toArray();

        // --- 검색 설정
        $this->setSearchAddGoods($getValue);

        // --- 정렬 설정
        $sort = gd_isset($getValue['sort']);
        if (empty($sort)) {
            $sort = 'ag.regDt desc';
        }

        // --- 페이지 기본설정
        gd_isset($getValue['page'], 1);
        gd_isset($getValue['pageNum'], 10);


        $page = \App::load('\\Component\\Page\\Page', $getValue['page'],0,0,$getValue['pageNum']);
        $page->setCache(true); // 페이지당 리스트 수
        if (Session::get('manager.isProvider')) {
//            $strSQL = ' SELECT COUNT(*) AS cnt FROM ' . DB_ADD_GOODS . ' WHERE scmNo = \'' . Session::get('manager.scmNo') . '\'';
            $strSQL = ' SELECT COUNT(*) AS cnt FROM ' . DB_ADD_GOODS;
        } else {
            $strSQL = ' SELECT COUNT(*) AS cnt FROM ' . DB_ADD_GOODS;
        }

        if ($page->hasRecodeCache('amount') === false) {
            $res = $this->db->query_fetch($strSQL, null, false);
            $page->recode['amount'] = $res['cnt']; // 전체 레코드 수
        }

        // 현 페이지 결과
        $join[] = ' LEFT JOIN ' . DB_SCM_MANAGE . ' as sm ON sm.scmNo = ag.scmNo ';
        $join[] = ' LEFT JOIN ' . DB_CATEGORY_BRAND . ' as cb ON cb.cateCd = ag.brandCd ';
        $this->db->strField = "ag.*, sm.companyNm as scmNm, cb.cateNm as brandNm";

        if (gd_is_provider() === false && gd_is_plus_shop(PLUSSHOP_CODE_PURCHASE) === true) {
            $join[] = ' LEFT JOIN ' . DB_PURCHASE . ' as p ON p.purchaseNo = ag.purchaseNo  AND p.delFl = "n"';
            $this->db->strField .= ",p.purchaseNm";
        }
        $this->db->strJoin = implode('', $join);

        if (gd_isset($this->arrWhere)){
            $this->db->strWhere = implode(' AND ', gd_isset($this->arrWhere));
        }
        $this->db->strOrder = $sort;
        $this->db->strLimit = $page->recode['start'] . ',' . $getValue['pageNum'];

        // 검색 카운트
        if (Session::get('manager.isProvider')) {
            $strSQL = ' SELECT COUNT(*) AS cnt FROM ' . DB_ADD_GOODS . ' as ag ' . implode('', $join);
            if($this->db->strWhere) {
                $strSQL.=' WHERE ' . $this->db->strWhere;
            }
        } else {
            $strSQL = ' SELECT COUNT(*) AS cnt FROM ' . DB_ADD_GOODS . ' as ag ' . implode('', $join);
            if($this->db->strWhere) {
                $strSQL.=' WHERE ' . $this->db->strWhere;
            }
        }

        if ($page->hasRecodeCache('total') === false) {
            $res = $this->db->query_fetch($strSQL, $this->arrBind, false);
            $page->recode['total'] = $res['cnt']; // 검색 레코드 수
        }
        $page->setUrl(\Request::getQueryString());
        $page->setPage();

        $query = $this->db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_ADD_GOODS . ' as ag ' . implode(' ', $query);
        print_r($strSQL);
        print_r($this->arrBind);
        $data = $this->db->query_fetch($strSQL, $this->arrBind);

        // 각 데이터 배열화
        $getData['data'] = gd_htmlspecialchars_stripslashes(gd_isset($data));
        $getData['sort'] = $sort;
        $getData['search'] = gd_htmlspecialchars($this->search);
        $getData['checked'] = $this->checked;
        $getData['selected'] = $this->selected;

        return $getData;
    }

    /**
     * setSearchAddGoods
     *
     * @param $searchData
     * @param int $searchPeriod
     */
    public function setSearchAddGoods($searchData, $searchPeriod = '-1')
    {
        // 검색을 위한 bind 정보
        $fieldType = DBTableField::getFieldTypes('tableAddGoods');
        /* @formatter:off */
        $this->search['combineSearch'] =[
            'ag.goodsNm' => __('상품명'),
            'ag.addGoodsNo' => __('상품코드'),
            'ag.goodsCd' => __('자체상품코드'),
            'ag.makerNm' => __('제조사'),
            'ag.goodsModelNo' => __('모델번호'),
        ];
        /* @formatter:on */

        if(gd_is_provider() === false) {
            $this->search['combineSearch']['companyNm'] = __('공급사명');
            if(gd_is_plus_shop(PLUSSHOP_CODE_PURCHASE) === true ) $this->search['combineSearch']['purchaseNm'] = __('매입처명');
        }

        /* @formatter:off */
        $this->search['sortList'] = [
            'ag.regDt desc' => __('등록일 ↓'),
            'ag.regDt asc' => __('등록일 ↑'),
            'ag.goodsNm asc' => __('상품명 ↓'),
            'ag.goodsNm desc' => __('상품명 ↑'),
            'ag.goodsPrice asc' => __('판매가 ↓'),
            'ag.goodsPrice desc' => __('판매가 ↑'),
            'sm.companyNm asc' => __('공급사 ↓'),
            'sm.companyNm desc' => __('공급사 ↑'),
            'ag.makerNm asc' => __('제조사 ↓'),
            'ag.makerNm desc' => __('제조사 ↑')
        ];
        /* @formatter:on */

        // --- 검색 설정
        $this->search['sort'] = gd_isset($searchData['sort'], 'ag.regDt desc');
        $this->search['key'] = gd_isset($searchData['key']);
        $this->search['keyword'] = gd_isset($searchData['keyword']);
        $this->search['detailSearch'] = gd_isset($searchData['detailSearch']);
        $this->search['searchDateFl'] = gd_isset($searchData['searchDateFl'], 'ag.regDt');
        $this->search['searchPeriod'] = gd_isset($searchData['searchPeriod'],'-1');
        $this->search['scmFl'] = gd_isset($searchData['scmFl'], Session::get('manager.isProvider')? 'n' : 'all');
        $this->search['scmNo'] = gd_isset($searchData['scmNo'], (string)Session::get('manager.scmNo'));
        $this->search['scmNoNm'] = gd_isset($searchData['scmNoNm']);
        $this->search['purchaseNo'] = gd_isset($searchData['purchaseNo']);
        $this->search['purchaseNoNm'] = gd_isset($searchData['purchaseNoNm']);
        $this->search['makerNm'] = gd_isset($searchData['makerNm']);
        $this->search['stockUseFl'] = gd_isset($searchData['stockUseFl'], 'all');
        $this->search['goodsPrice'][] = gd_isset($searchData['goodsPrice'][0]);
        $this->search['goodsPrice'][] = gd_isset($searchData['goodsPrice'][1]);
        $this->search['brandCd'] = gd_isset($searchData['brandCd']);
        $this->search['brandCdNm'] = gd_isset($searchData['brandCdNm']);
        $this->search['viewFl'] = gd_isset($searchData['viewFl'],'all');
        $this->search['soldOutFl'] = gd_isset($searchData['soldOutFl'],'all');
        $this->search['applyType'] = gd_isset($searchData['applyType'], 'all');
        $this->search['applyFl'] = gd_isset($searchData['applyFl'], 'all');
        $this->search['brandNoneFl'] = gd_isset($searchData['brandNoneFl']);
        $this->search['purchaseNoneFl'] = gd_isset($searchData['purchaseNoneFl']);


        if( $this->search['searchPeriod']  < 0) {
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0]);
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][1]);
        } else {
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0], date('Y-m-d', strtotime('-6 day')));
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][1], date('Y-m-d'));
        }
        $this->checked['searchPeriod'][$this->search['searchPeriod']] ="active";
        $this->checked['purchaseNoneFl'][$this->search['purchaseNoneFl']]= $this->checked['brandNoneFl'][$this->search['brandNoneFl']]= $this->checked['applyType'][$this->search['applyType']] = $this->checked['applyFl'][$this->search['applyFl']] = $this->checked['viewFl'][$searchData['viewFl']] = $this->checked['soldOutFl'][$searchData['soldOutFl']] = $this->checked['scmFl'][$searchData['scmFl']] = $this->checked['stockUseFl'][$searchData['stockUseFl']] = "checked='checked'";
        $this->selected['searchDateFl'][$this->search['searchDateFl']] = $this->selected['sort'][$this->search['sort']] = "selected='selected'";

        // 처리일자 검색
        if ($this->search['searchDateFl'] && $this->search['searchDate'][0] && $this->search['searchDate'][1]) {
            $this->arrWhere[] =$this->search['searchDateFl'] . ' BETWEEN ? AND ?';
            $this->db->bind_param_push($this->arrBind, 's', $this->search['searchDate'][0] . ' 00:00:00');
            $this->db->bind_param_push($this->arrBind, 's', $this->search['searchDate'][1] . ' 23:59:59');
        }

        // 테마명 검색
        if ($this->search['key'] && $this->search['keyword']) {
            if ($this->search['key'] == 'all') {
                $tmpWhere = array('ag.goodsNm', 'ag.addGoodsNo', 'ag.goodsCd', 'ag.makerNm');
                $arrWhereAll = array();
                foreach ($tmpWhere as $keyNm) {
                    $arrWhereAll[] = '(' . $keyNm . ' LIKE concat(\'%\',?,\'%\'))';
                    $this->db->bind_param_push($this->arrBind, 's', $this->search['keyword']);
                }

                if(gd_is_provider() === false && gd_is_plus_shop(PLUSSHOP_CODE_PURCHASE) === true) {
                    /* 매입처명 검색 추가 */
                    $arrWhereAll[] = 'p.purchaseNm LIKE concat(\'%\',?,\'%\')';
                    $this->db->bind_param_push($this->arrBind, 's', $this->search['keyword']);
                }

                $this->arrWhere[] = '(' . implode(' OR ', $arrWhereAll) . ')';
            } else {
                if ($this->search['key'] == 'companyNm') {
                    $this->arrWhere[] = 'sm.' . $this->search['key'] . ' LIKE concat(\'%\',?,\'%\')';
                    $this->db->bind_param_push($this->arrBind, 's', $this->search['keyword']);
                } else {
                    $this->arrWhere[] = '' . $this->search['key'] . ' LIKE concat(\'%\',?,\'%\')';
                    $this->db->bind_param_push($this->arrBind, 's', $this->search['keyword']);
                }
            }
        }

        if ($this->search['scmFl'] != 'all') {
            if (is_array($this->search['scmNo'])) {
                foreach ($this->search['scmNo'] as $val) {
                    $tmpWhere[] = 'ag.scmNo = ?';
                    $this->db->bind_param_push($this->arrBind, 's', 1);
                }
                $this->arrWhere[] = '(' . implode(' OR ', $tmpWhere) . ')';
                unset($tmpWhere);
            } else {
                $this->arrWhere[] = 'ag.scmNo = ?';
                $this->db->bind_param_push($this->arrBind, $fieldType['scmNo'], 1);

                $this->search['scmNo'] = array($this->search['scmNo']);
                $this->search['scmNoNm'] = array($this->search['scmNoNm']);
            }

        }


        if (($this->search['brandCd'] && $this->search['brandCdNm'])) {
            if (!$this->search['brandCd'] && $this->search['brand'])
                $this->search['brandCd'] = $this->search['brand'];
            $this->arrWhere[] = 'ag.brandCd = ?';
            $this->db->bind_param_push($this->arrBind, $fieldType['brandCd'], $this->search['brandCd']);
        } else $this->search['brandCd'] = '';


        //브랜드 미지정
        if ($this->search['brandNoneFl']) {
            $this->arrWhere[] = 'ag.brandCd  = ""';
        }

        // 매입처 검색
        if (($this->search['purchaseNo'] && $this->search['purchaseNoNm'])) {
            if (is_array($this->search['purchaseNo'])) {
                foreach ($this->search['purchaseNo'] as $val) {
                    $tmpWhere[] = 'ag.purchaseNo = ?';
                    $this->db->bind_param_push($this->arrBind, 's', $val);
                }
                $this->arrWhere[] = '(' . implode(' OR ', $tmpWhere) . ')';
                unset($tmpWhere);
            }
        }

        //매입처 미지정
        if ($this->search['purchaseNoneFl']) {
            $this->arrWhere[] = '(ag.purchaseNo IS NULL OR ag.purchaseNo  = "")';
        }

        if ($this->search['makerNm']) {
            $this->arrWhere[] = 'ag.makerNm = ?';
            $this->db->bind_param_push($this->arrBind, $fieldType['makerNm'], $this->search['makerNm']);
        }

        if ($searchData['goodsPrice'][0] && $searchData['goodsPrice'][1]) {
            $this->arrWhere[] = '(ag.goodsPrice >= ? and ag.goodsPrice <= ?)';
            $this->db->bind_param_push($this->arrBind, $fieldType['goodsPrice'], $this->search['goodsPrice'][0]);
            $this->db->bind_param_push($this->arrBind, $fieldType['goodsPrice'], $this->search['goodsPrice'][1]);
        }


        if ($this->search['stockUseFl'] != 'all') {
            switch ($this->search['stockUseFl']) {
                case 'n': {
                    $this->arrWhere[] = 'ag.stockUseFl = ?';
                    $this->db->bind_param_push($this->arrBind, $fieldType['stockUseFl'], '0');
                    break;
                }
                case 'u' : {
                    $this->arrWhere[] = '(ag.stockUseFl = ? and ag.stockCnt > 0)';
                    $this->db->bind_param_push($this->arrBind, $fieldType['stockUseFl'], '1');
                    break;
                }
                case 'z' : {
                    $this->arrWhere[] = '(ag.stockUseFl = ? and ag.stockCnt = 0)';
                    $this->db->bind_param_push($this->arrBind, $fieldType['stockUseFl'], '1');
                    break;
                }

            }
        }

        //노출여부
        if ($this->search['viewFl'] != 'all') {
            $this->arrWhere[] = 'ag.viewFl = ?';
            $this->db->bind_param_push($this->arrBind, $fieldType['viewFl'], $this->search['viewFl']);
        }

        //품절여부
        if ($this->search['soldOutFl'] != 'all') {
            $this->arrWhere[] = 'ag.soldOutFl = ?';
            $this->db->bind_param_push($this->arrBind, $fieldType['soldOutFl'], $this->search['soldOutFl']);
        }


        //승인구분
        if ($this->search['applyType'] != 'all') {
            $this->arrWhere[] = 'ag.applyType = ?';
            $this->db->bind_param_push($this->arrBind, $fieldType['applyType'], $this->search['applyType']);
        }

        //승인상태
        if ($this->search['applyFl'] != 'all') {
            $this->arrWhere[] = 'ag.applyFl = ?';
            $this->db->bind_param_push($this->arrBind, $fieldType['applyFl'], $this->search['applyFl']);
        }


        if (empty($this->arrBind)) {
            $this->arrBind = null;
        }

    }
}