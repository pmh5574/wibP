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
namespace Component\Board;
use Component\Order\Order;
use Component\Database\DBTableField;
use Framework\StaticProxy\Proxy\App;
use Request;
class BoardBuildQuery extends \Bundle\Component\Board\BoardBuildQuery
{
	/*
	 * 매장 등록 게시판 DB에서 값 꺼내오는 함수 추가
     * @author INBET Matthew 2019-03-22
     */
	  public function selectStoreList($search = null, array $addWhereQuery = null, $offset = 0, $limit = 10, $arrInclude = null,$orderByField = null)
    {
		
		if ($search) {
            list($strWhere, $arrBind) = self::getQueryWhere($search);
		
            $strWhere = (!$strWhere) ? "" : " AND " . $strWhere;
			
        }
        if(!$orderByField) {
            if($search['sort']) {
                $orderByField = $search['sort'];
            } else {
                $orderByField = 'b.storeSiDo asc';
            }
        }
	
        $joinGoods = false;
        $joinGoodsImage = false;
		
        if ($arrInclude) {

            foreach ($arrInclude as $_field) {
                switch (substr($_field, 0, 2)) {
                    case 'gi.' :
                        if (self::$_cfg['bdGoodsFl'] == 'y') {
                            $joinGoods = true;
                            $joinGoodsImage = true;
                            $gField[] = $_field;
                        }
                        break;
                    case 'g.':
                        if (self::$_cfg['bdGoodsFl'] == 'y') {
                            $joinGoods = true;
                            $gField[] = $_field;
                        }
                        break;
                    case 'm.' :
                        $mField[] = $_field;
                    case 'b.' :
                        $bField[] = $_field;
                        break;
                    default :
                        $bField[] = 'b.' . $_field;
                }
            }

            $boardFields = implode(',', $bField);
            if($gField) {
                $goodsFields = ',' . implode(',', $gField);
            }

        } else {
			
            $boardFields = implode(',', DBTableField::setTableField('tableBdStore', null, null, 'b'));
			
        }
        $boardField = 'b.sno,b.regDt,b.modDt,' . $boardFields;
	
        $strSQL = " SELECT " . $boardField . " FROM " . DB_BD_ . self::$_bdId . " as b  ";
        
        
        $strSQL .= " WHERE 1 " . $strWhere;
		
        $limit = $limit ?? 10;
		
        $strSQL .= " ORDER BY " . $orderByField . " LIMIT {$offset},{$limit} ";
		
        $result = self::$_db->query_fetch($strSQL, $arrBind);
		
        return $result;
    }
	/**
     * 게시글갯수 노출
     *
     * @param null $search
     * @param array|null $addWhereQuery
     * @return mixed
	 * @author INBET Matthew 2019-03-22 매장 등록 게시판일 경우 분기 추가
     */
    public function selectCount($search = null, array $addWhereQuery = null)
    {
        if ($search) {
            list($strWhere, $arrBind, $arrJoin) = self::getQueryWhere($search);
            $strWhere = (!$strWhere) ? "" : " AND " . $strWhere;
        }

        //검색결과로 조인여부 결정
        foreach($addWhereQuery as $_val) {
            if(strpos($_val,'g.')!==false){ //상품필드가 조건절에 포함되어있으면 조인
                $goodsJoin = true;
            }
        }

        if(self::$_cfg['bdGoodsFl'] == 'y') {
            $goodsJoin = true;
        }

        if (in_array(DB_GOODS, $arrJoin) || $goodsJoin) {
            $leftJoinGoods = " LEFT OUTER  JOIN " . DB_GOODS . " AS g
                    ON g.goodsNo = b.goodsNo ";

            if (\Request::getSubdomainDirectory() !== 'admin') {
                //접근권한 체크
                if (gd_check_login()) {
                    $strGoodsWhere = ' (g.goodsAccess !=\'group\'  OR (g.goodsAccess=\'group\' AND FIND_IN_SET(\''.\Session::get('member.groupSno').'\', REPLACE(g.goodsAccessGroup,"'.INT_DIVISION.'",","))) OR (g.goodsAccess=\'group\' AND !FIND_IN_SET(\''.\Session::get('member.groupSno').'\', REPLACE(g.goodsAccessGroup,"'.INT_DIVISION.'",",")) AND g.goodsAccessDisplayFl =\'y\'))';
                } else {
                    $strGoodsWhere = '  (g.goodsAccess=\'all\' OR (g.goodsAccess !=\'all\' AND g.goodsAccessDisplayFl =\'y\'))';
                }

                //성인인증안된경우 노출체크 상품은 노출함
                if (gd_check_adult() === false) {
                    $strGoodsWhere .= ' AND (onlyAdultFl = \'n\' OR (onlyAdultFl = \'y\' AND onlyAdultDisplayFl = \'y\'))';
                }

                $strWhere .=" AND (b.goodsNo = '0' OR (b.goodsNo > 0 AND " .$strGoodsWhere."))";
            }
        }

        if (in_array(DB_MEMBER, $arrJoin)) {
            $leftJoinMember = " LEFT OUTER  JOIN " . DB_MEMBER . " AS m
                    ON m.memNo = b.memNo ";
        }
        $strSQL = " SELECT count(*) AS cnt FROM " . DB_BD_ . self::$_bdId . " as b  ";
		

        $strSQL .= $leftJoinGoods . $leftJoinMember;
        $strSQL .= " WHERE 1 " . $strWhere;

		if($search['bdId']!="store"){
			if ($addWhereQuery) {
				$strSQL .= ' AND ' . implode(' AND ', $addWhereQuery);
			}
		}
        $result = self::$_db->query_fetch($strSQL, $arrBind, false);
		
        return $result['cnt'];
    }
	
	/**
	 *
	 * @author INBET Matthew 2019-03-22 매장 등록 게시판일 경우 검색 키워드 추가
     */

	 public function getQueryWhere($search = null)
    {
		//gd_debug($search);
		$arrBind = [];
        $strWhere = "";
        $arrWhere = [];
        $arrJoin = [];
        //회원검색
        if ($search['mypageFl'] == 'y') {
            $arrWhere[] = " (b.memNo  = ? OR b.parentSno  = ?) ";
            self::$_db->bind_param_push($arrBind, 'i', \Session::get('member.memNo'));
            self::$_db->bind_param_push($arrBind, 'i', \Session::get('member.memNo'));
        }
        else if (gd_isset($search['memNo'])) {
            if(self::$_cfg['bdKind'] == Board::KIND_QA){
                $arrWhere[] = " (b.memNo  = ?) ";
                self::$_db->bind_param_push($arrBind, 'i', $search['memNo']);
            }
            else {
                // CRM 고객관리 게시판 탭 쿼리 조건 추가  - navTabs, managerNo
                if($search['navTabs'] == 'board') {
                    $arrWhere[] = " (b.memNo  = ?) ";
                    self::$_db->bind_param_push($arrBind, 'i', $search['memNo']);
                }
                else {
                    $arrWhere[] = " (b.memNo  = ? OR b.parentSno  = ?) ";
                    self::$_db->bind_param_push($arrBind, 'i', $search['memNo']);
                    self::$_db->bind_param_push($arrBind, 'i', $search['memNo']);
                }
            }
        }

        if ($search['recentlyDate']) {
            $arrWhere[] = "TO_DAYS(now()) - TO_DAYS(b.regDt) <= " . $search['recentlyDate'];
        }

        if (gd_isset($search['scmNo'])) {
            $arrWhere[] = "g.scmNo = ?";
            self::$_db->bind_param_push($arrBind, 'i', $search['scmNo']);
            $arrJoin[] = DB_GOODS;
        }
		if(gd_isset($search['searchStoreSiDo'])  ){
			self::$_db->bind_param_push($arrBind, 's', $search['searchStoreSiDo']);
			$arrWhere[] = " storeSiDo = ? ";
		}
		if(gd_isset($search['searchStoreType']) ){
			self::$_db->bind_param_push($arrBind, 's', $search['searchStoreType']);
			$arrWhere[] = " storeType = ? ";
		}
        if (gd_isset($search['searchWord'])) {
            switch ($search['searchField']) {
                case 'subject' :
                    self::$_db->bind_param_push($arrBind, 's', $search['searchWord']);
                    $arrWhere[] = "subject LIKE concat('%',?,'%')";
                    break;
                    break;
                case 'contents' :
                    self::$_db->bind_param_push($arrBind, 's', $search['searchWord']);
                    $arrWhere[] = "contents LIKE concat('%',?,'%')";
                    break;
                case 'writerNm' :
                    self::$_db->bind_param_push($arrBind, 's', $search['searchWord']);
                    $arrWhere[] = "writerNm  LIKE concat('%',?,'%')";
                    break;
                case 'writerNick' :
                    self::$_db->bind_param_push($arrBind, 's', $search['searchWord']);
                    $arrWhere[] = "writerNick  LIKE concat('%',?,'%')";
                    break;
                case 'writerId' :
                    self::$_db->bind_param_push($arrBind, 's', $search['searchWord']);
                    $arrWhere[] = "writerId  LIKE concat('%',?,'%')";
                    break;
                case 'subject_contents' :
                    $arrWhere[] = "(subject LIKE concat('%',?,'%') or contents LIKE concat('%',?,'%') )";
                    self::$_db->bind_param_push($arrBind, 's', $search['searchWord']);
                    self::$_db->bind_param_push($arrBind, 's', $search['searchWord']);
                    break;
                case 'goodsNm' :
                    if (self::$_cfg['bdGoodsFl'] == 'y') {
                        $arrJoin[] = DB_GOODS;
                        self::$_db->bind_param_push($arrBind, 's', $search['searchWord']);
                        $arrWhere[] = "g.goodsNm  LIKE concat('%',?,'%')";
                    }
                    break;
                case 'goodsNo' :
                    if (self::$_cfg['bdGoodsFl'] == 'y') {
                        $arrJoin[] = DB_GOODS;
                        self::$_db->bind_param_push($arrBind, 's', $search['searchWord']);
                        $arrWhere[] = "g.goodsNo  LIKE concat('%',?,'%')";
                    }
                    break;
                case 'goodsCd' :
                    if (self::$_cfg['bdGoodsFl'] == 'y') {
                        $arrJoin[] = DB_GOODS;
                        self::$_db->bind_param_push($arrBind, 's', $search['searchWord']);
                        $arrWhere[] = "g.goodsCd  LIKE concat('%',?,'%')";
                        break;
				
                    }
				case 'storeTitle' :
                    self::$_db->bind_param_push($arrBind, 's', $search['searchWord']);
                    $arrWhere[] = "storeTitle LIKE concat('%',?,'%')";
                    break;
				case 'storeSiDo' :
                    self::$_db->bind_param_push($arrBind, 's', $search['searchWord']);
                    $arrWhere[] = "storeSiDo LIKE concat('%',?,'%')";
                    break;
				case 'address' :
                    self::$_db->bind_param_push($arrBind, 's', $search['searchWord']);
                    $arrWhere[] = "address LIKE concat('%',?,'%')";
                    break;
            }
        }

        if (gd_isset($search['goodsPt'])) {
            $arrWhere[] = "goodsPt = ?";
            self::$_db->bind_param_push($arrBind, 'i', $search['goodsPt']);
        }


        if (gd_isset($search['replyStatus'])) {
            $arrWhere[] = " replyStatus = ?";
            self::$_db->bind_param_push($arrBind, 's', $search['replyStatus']);
        }

        switch ($search['period']) {
            case 'current' :
                $arrWhere[] = " now() between eventStart and eventEnd ";
                break;
            case 'end' :
                $arrWhere[] = " now() > eventEnd ";
                break;
        }

        //일자 검색
        if (gd_isset($search['rangDate'][0]) && gd_isset($search['rangDate'][1])) {
            if ($search['searchDateFl'] == 'modDt') {
                $dateField = 'b.modDt';
            } else {
                $dateField = 'b.regDt';
            }

            $arrWhere[] = $dateField . " between ? and ?";
            self::$_db->bind_param_push($arrBind, 's', $search['rangDate'][0]);
            self::$_db->bind_param_push($arrBind, 's', $search['rangDate'][1] . ' 23:59');
        }

        //이벤트 기간검색
        if (gd_isset($search['rangEventDate'][0]) && gd_isset($search['rangEventDate'][1])) {
            $arrWhere[] = " eventStart < ? AND eventEnd > ? ";
            self::$_db->bind_param_push($arrBind, 's', $search['rangEventDate'][0]);
            self::$_db->bind_param_push($arrBind, 's', $search['rangEventDate'][1] . ' 23:59');
        }

        if (self::$_cfg['bdCategoryFl'] == 'y') {
            if (gd_isset($search['category'])) {
                $arrWhere[] = "category = ?";
                self::$_db->bind_param_push($arrBind, self::$_fieldTypes['board']['category'], $search['category']);
            }
        }

        if (self::$_cfg['bdGoodsFl'] == 'y') {
            if (gd_isset($search['goodsNo'])) {
                $arrWhere[] = " b.goodsNo  = ?";
                self::$_db->bind_param_push($arrBind, 'i', $search['goodsNo']);
            }
        }

        if (gd_isset($search['isNotice'])) {
            $arrWhere[] = "isNotice = ?";
            self::$_db->bind_param_push($arrBind, self::$_fieldTypes['board']['isNotice'], $search['isNotice']);
        }

        $strWhere .= implode(" AND ", $arrWhere);
		
        return [$strWhere, $arrBind, $arrJoin];
    }
}