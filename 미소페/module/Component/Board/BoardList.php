<?php

/**
 * 게시판 리스트 Class
 */
namespace Component\Board;

use Component\Database\DBTableField;
use Component\Goods\Goods;
use Component\Member\Util\MemberUtil;
use Component\Order\Order;
use Component\Page\Page;
use Component\Storage\Storage;
use Framework\Debug\Exception\RequiredLoginException;
use Framework\StaticProxy\Proxy\Request;
use Framework\Utility\ArrayUtils;
use Framework\Utility\FileUtils;
use Framework\Utility\ImageUtils;
use Framework\Utility\SkinUtils;
use Framework\Utility\StringUtils;

class BoardList extends \Bundle\Component\Board\BoardList
{
	/**
     * getList
     *
     * @param bool $isPaging 페이징여부
     * @param int $listCount 출력 게시글 수
     * @param int $subjectCut 제목길이( 0 이면 모두노출)
     * @param array $arrWhere
     * @param null $arrInclude (출력필드 . 값이 없으면 모든정보 노출)
     * @param bool $displayNotice (공지사항 출력여부)
     *
     * @return mixed
     * @throws RequiredLoginException
     * @throws \Exception
     */
    public function getList($isPaging = true, $listCount = 0, $subjectCut = 0, $arrWhere = [], $arrInclude = null, $displayNotice = true)
    {
        if($this->req['self'] == 'y') {
            $this->req['memNo'] = $this->member['memNo'];
        }

        if (empty($this->req['memNo']) === false) { //마이페이지 접근 시 회원고유값 체크
            if ($this->req['memNo'] != $this->member['memNo']) {
                throw new \Exception(__('권한이 없습니다.'));
            }
        }

        if ($listCount == null) {
            if ($this->cfg['bdKind'] == Board::KIND_GALLERY) {  //유형이 갤러리일때 노출타입 가로*세로 계산
                $listCount = $this->cfg['bdListColsCount'] * $this->cfg['bdListRowsCount'];
                $this->cfg['bdListCount'] = $listCount;
            } else {
                $listCount = $this->cfg['bdListCount'];
            }
        }

        if ($this->cfg['bdKind'] == Board::KIND_GALLERY && $this->req['gboard'] != 'y' && !$this->req['goodsNo']) {
            $arrWhere[] = "groupThread = '' ";
        }

        if ($this->cfg['bdListInNotice'] == 'n') {
            $this->req['isNotice'] = 'n';
        }

        $data = parent::getList($isPaging, $listCount, $subjectCut, $arrWhere, $arrInclude, $displayNotice);
		

        //이벤트일경우 종료일 카운트
        if ($this->cfg['bdEventFl'] == 'y') {
            $totalEventCount = $this->buildQuery->selectCount([" isDelete = 'n' "]);
            $currentEventCount = $this->buildQuery->selectCount([" isDelete = 'n' "], ["  now() BETWEEN eventStart AND eventEnd "]);
            $endEventCount = $this->buildQuery->selectCount([" isDelete = 'n' "], ["  now() >  eventEnd "]);

            $data['cnt']['totalEventCount'] = $totalEventCount;
            $data['cnt']['currentEventCount'] = $currentEventCount;
            $data['cnt']['endEventCount'] = $endEventCount;
        }

        return $data;
    }
	 /**
     * [게시판] store 게시판 프론트 검색에 쓰일 셀렉트박스 값
     *
     * @author INBET matthew 2019-03-22
     * 
     */
	public function getStoreAddress(){
		$arrBind = [];
        $this->db->strField = "storeSiDo";
		
        $query = $this->db->query_complete();
        $strSQL = "SELECT " . array_shift($query) . " FROM " . 'es_bd_store' . " " . implode(" ", $query)."group by storeSiDo";
        $data = $this->db->query_fetch($strSQL, $arrBind);
       
		unset($arrBind);
	
        return gd_htmlspecialchars_stripslashes($data);
	}
	 /**
     * [게시판] store 게시판 프론트 검색에 쓰일 셀렉트박스 값
     *
     * @author INBET matthew 2019-03-22
     * 
     */
	public function getStoreType(){
		$arrBind = [];
        $this->db->strField = "storeType";
		
        $query = $this->db->query_complete();
        $strSQL = "SELECT " . array_shift($query) . " FROM " . 'es_bd_store' . " " . implode(" ", $query)."group by storeType";
        $data = $this->db->query_fetch($strSQL, $arrBind);
       
		unset($arrBind);
	
        return gd_htmlspecialchars_stripslashes($data);
	}
	
}