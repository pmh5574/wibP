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

use Bundle\Component\Admin\AdminMenu;
use Component\Member\Util\MemberUtil;
use Component\Goods\AddGoodsAdmin;
use Component\Goods\Goods;
use Component\Mail\MailAuto;
use Component\Order\Order;
use Component\Page\Page;
use Component\Storage\Storage;
use Component\Validator\Validator;
use Component\Database\DBTableField;
use Framework\Debug\Exception\RequiredLoginException;
use Framework\Utility\ArrayUtils;
use Framework\Utility\ImageUtils;
use Framework\Utility\SkinUtils;
use Framework\Utility\StringUtils;
use Request;
use App;
use Session;
use Respect\Validation\Rules\MyValidator;

abstract class Board extends \Bundle\Component\Board\Board
{
	/*
	 * 매장 등록 게시판에서 작성 할 때 DB에 insert하는 함수 추가 
     * @author INBET Matthew 2019-03-22
     */
		
	public function saveStoreData(){

		BoardUtil::checkForbiddenWord($this->req['store_title']);
		$validator = new Validator();
        $arrData = [];
		
		 switch ($this->req['mode']) {
            case 'write' :
                $canWrite = $this->canWrite();
                if ($canWrite == 'n') {
                    throw new \Exception(__('접근 권한이 없습니다.'));
                } else if (is_array($canWrite)) {
                    if ($canWrite['result'] === false) {
                        throw new \Exception($canWrite['msg']);
                    }
                }

                break;
				 case "modify":
                {
                    if ($this->canModify($modify) == 'n') {
                        throw new \Exception(__('접근 권한이 없습니다.'));
                    }
                    if (empty($this->channel)) {
                        $this->handleBeforeModify($modify);
                    }
                   
                    break;
                }
		 }
		 switch ($this->req['mode']) {
			case "write":
			case "modify":
				$address=$this->req['address']."^|^".$this->req['addressSub'];
               	$storePhoneNo=$this->req['storePhoneNo1']."-".$this->req['storePhoneNo2']."-".$this->req['storePhoneNo3'];
				$this->setStoreSaveData('storeTitle', $this->req['storeTitle'], $arrData, $validator);
				$this->setStoreSaveData('storeSiDo', $this->req['storeSiDo'], $arrData, $validator);
				$this->setStoreSaveData('address', $address, $arrData, $validator);
				$this->setStoreSaveData('storeType', $this->req['storeType'], $arrData, $validator);
				$this->setStoreSaveData('storePhoneNo', $storePhoneNo, $arrData, $validator);

			


		 }
		 if ($validator->act($arrData, true) === false) {
            $validKeyName = ['storeTitle' => __('매장명'), 'address' => __('주소'),'storeType' => __('매장타입'),'storePhoneNo' => __('매장번호'),'storeSiDo' => __('지역')];
            foreach ($validator->errors as $key => $row) {
                if (array_key_exists($key, $validKeyName)) {
                    $errorMsg = sprintf(__('%1$s 을 입력하시기 바랍니다.'), $validKeyName[$key]);
                    break;
                }
            }

            if (!$errorMsg) {
                $errorMsg = sprintf(__('%1$s 은(는) 유효하지 않는 값입니다.'), implode("\n", $validator->errors));
            }
            throw new \Exception($errorMsg);
        }

		switch ($this->req['mode']) {
            case 'write':
				$address=$this->req['address']."^|^".$this->req['addressSub'];
               	$storePhoneNo=$this->req['storePhoneNo1']."-".$this->req['storePhoneNo2']."-".$this->req['storePhoneNo3'];
				$arrBind=[];
				$strSQL = "INSERT INTO es_bd_store SET storeTitle = ?, storeSiDo = ?,  storeType = ?, storePhoneNo = ?, address = ?, storeDisplayFl =?, regDt=now()";
				$this->db->bind_param_push($arrBind, 's', $this->req['storeTitle']);
				$this->db->bind_param_push($arrBind, 's', $this->req['storeSiDo']);
				
				$this->db->bind_param_push($arrBind, 's', $this->req['storeType']);
				$this->db->bind_param_push($arrBind, 's', $storePhoneNo);
				$this->db->bind_param_push($arrBind, 's', $address);
				$this->db->bind_param_push($arrBind, 's',  $this->req['storeDisplayFl']);
				
			
				$this->db->bind_query($strSQL, $arrBind);
                break;
         
            case 'modify':
                $storePhoneNo=$this->req['storePhoneNo1']."-".$this->req['storePhoneNo2']."-".$this->req['storePhoneNo3'];
				$address=$this->req['address']."^|^".$this->req['addressSub'];
				$arrBind=[];
				$strSQL = "UPDATE es_bd_store SET storeTitle = ?, storeSiDo = ?,  storeType = ?, storePhoneNo = ?, address = ?, storeDisplayFl = ?, modDt=now() WHERE sno = ? ";
				$this->db->bind_param_push($arrBind, 's', $this->req['storeTitle']);
				$this->db->bind_param_push($arrBind, 's', $this->req['storeSiDo']);
				
				$this->db->bind_param_push($arrBind, 's', $this->req['storeType']);
				$this->db->bind_param_push($arrBind, 's', $storePhoneNo);
				$this->db->bind_param_push($arrBind, 's', $address);
				$this->db->bind_param_push($arrBind, 's',  $this->req['storeDisplayFl']);
				$this->db->bind_param_push($arrBind, 'i', $this->req['sno']);
				
				$this->db->bind_query($strSQL, $arrBind);
                break;
        }
		return $msg;
	}
	/*
	 * 리스트에 뿌려주는 함수 매장 등록 게시판 일떄 분기 추가
     * @author INBET Matthew 2019-03-22
     */
	protected function getList($isPaging = true, $listCount = 10, $subjectCut = 0, $arrWhere = [], $arrInclude = null, $displayNotice)
    {
        //리스트 권한체크
        if ($this->canList() == 'n') {
            if (MemberUtil::isLogin() === false) {
                throw new RequiredLoginException();
            }
            throw new \Exception(__('접근 권한이 없습니다.'), Board::EXCEPTION_CODE_AUTH);
        }
        if ($this->isAdmin() === false) {
            $arrWhere[] = " isDelete = 'n'";
            if($this->cfg['bdAnswerStatusFl'] == 'y'){
                $arrWhere[] = "(( memNo < 0 AND replyStatus = '3' ) OR ( memNo >= 0 ))";
            }
        }
        $this->cfg['bdSubjectLength'] = $subjectCut ? $subjectCut : $this->cfg['bdSubjectLength'];
        $offset = ($this->req['page'] - 1) * $listCount;

        if ($displayNotice === true) {
            if ($this->cfg['bdOnlyMainNotice'] == 'n' || ($this->cfg['bdOnlyMainNotice'] == 'y' && $this->req['page'] == 1)) {
                $noticeArticleData = $this->getNoticeList($this->cfg['bdNoticeCount'], $arrInclude);
            }

            $getData['noticeData'] = gd_htmlspecialchars_stripslashes($noticeArticleData);
            $this->applyConfigList($getData['noticeData']);
        }
        $bdIncludeReplayInSearchTypeKey = $this->isAdmin() ? 'admin' : 'front';
        $checkBdIncludeReplayInSearchTypeKey = $this->cfg['bdIncludeReplayInSearchType'][$bdIncludeReplayInSearchTypeKey] == 'y' && $this->req['searchWord'];

        //CRM 고객관리 게시판 탭 의 경우 조건 실행
        if($this->req['navTabs'] == 'board' && $this->req['memNo']) {
            if($this->cfg['bdIncludeReplayInSearchType'][$bdIncludeReplayInSearchTypeKey] == 'y') {
                $checkBdIncludeReplayInSearchTypeKey = true;
            }
        }
		
		if($this->req['bdId']=="store"){
			$articleData = $this->buildQuery->selectStoreList($this->req, $arrWhere, $offset, $listCount, $arrInclude);
		}else{
			$articleData = $this->buildQuery->selectList($this->req, $arrWhere, $offset, $listCount, $arrInclude);
		}
        $getData['data'] = gd_htmlspecialchars_stripslashes($articleData);
		
		
        if ($checkBdIncludeReplayInSearchTypeKey) {
            foreach ($articleData as $val) {
                if ($val['parentSno'] == 0) {
                    $parentSno[] = $val['sno'];
                }
            }
            foreach ($getData['data'] as $val) {
                $migrationArticleData[] = $val;
                if ((in_array($val['sno'], $parentSno))) {
                    $childData = $this->getChildListByGroupNo($val['groupNo']);
                    foreach ($childData as $_val) {
                        $_val['noCount'] = 'y';
                        if ($val['goodsNo'] == $_val['goodsNo']) {
                            $_val['imageName'] = $val['imageName'];
                            $_val['imagePath'] = $val['imagePath'];
                            $_val['imageStorage'] = $val['imageStorage'];
                        }
                        $migrationArticleData[] = $_val;
                    }
                }
            }
            $getData['data'] = $migrationArticleData;
        }

        //웹서비스형태로 데이터 가공
        $this->applyConfigList($getData['data']);
        if (gd_array_empty($getData['data']) === true) return $getData;

        //페이징에 필요한 데이터 가공
        if ($isPaging) {
			
            $searchCnt = $totalCnt = $this->buildQuery->selectCount($this->req,$arrWhere);  //front
			
            $listNo = $searchCnt - $offset;
			
            if ($getData['data']) {
                foreach ($getData['data'] as &$articleData) {
                    if (!$articleData['noCount']) {
						
                        $articleData['listNo'] = $listNo;
                        $articleData['articleListNo'] = $listNo + $this->cfg['bdStartNum'] - 1;
						
                        $listNo--;
                    } else {
                        $articleData['listNo'] = $listNo + $this->cfg['bdStartNum'];
                        $articleData['articleListNo'] = '-';
                    }
                }
            }

            if ($this->isAdmin()) {
                // SCM 게시판의 경우 회원번호기준으로 조건 추가
                $thisCallController = \App::getInstance('ControllerNameResolver')->getControllerName();
                if($thisCallController == 'Controller\Admin\Share\MemberCrmBoardController' && ($this->req['memNo'] && $this->req['navTabs'] == 'board')) {
                    $totalCnt = $this->buildQuery->selectCount(['bdId' => $this->req['bdId'], 'memNo' =>$this->req['memNo'], 'navTabs' =>$this->req['navTabs'], 'managerNo' =>$this->req['managerNo']], $arrWhere);
                } else {
                    $totalCnt = $this->buildQuery->selectCount(['bdId' => $this->req['bdId']], $arrWhere);
                }
            }

            $this->pagination = new Page($this->req['page'], $searchCnt, $totalCnt, $listCount, self::PAGINATION_BLOCK_COUNT);
            $this->pagination->setUrl(Request::getQueryString());
            $getData['pagination'] = $this->pagination;
            $getData['cnt']['search'] = $searchCnt;
            $getData['cnt']['total'] = $totalCnt;
            $getData['cnt']['totalPage'] = $this->pagination->page['total'];

		if($this->req['bdId']=="store"){
			 $getData['sort'] = [
                'b.storeSiDo asc' => __('지역↓'),
                'b.storeSiDo desc' => __('지역↑'),
                'b.regDt desc' => __('등록일↓'),
                'b.regDt asc' => __('등록일↑'),
            ];
		}else{
			 $getData['sort'] = [
                'b.groupNo asc' => __('번호↓'),
                'b.groupNo desc' => __('번호↑'),
                'b.regDt desc' => __('등록일↓'),
                'b.regDt asc' => __('등록일↑'),
            ];

		}
       }
		
        return $getData;
    }
	/*
	 * 매장 등록 게시판에서 작성 할 때 DB에 insert하기 전 Validate관련 함수
     * @author INBET Matthew 2019-03-22
     */
	protected function setStoreSaveData($key, &$val, &$data, &$validator)
    {
        $requiredExpect = ['storeTitle', 'storeSiDo','address','storePhoneNo','storeType'];
       
        $required = false;
        if (isset($key) && isset($val)) {
            if (in_array($key, $requiredExpect)) {
                $required = true;
            }
            $data[$key] = $val;
            $validator->add($key, '', $required);
        }
    }
}