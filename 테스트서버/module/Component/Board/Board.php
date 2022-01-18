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

use Component\Board\Board;
use Component\Storage\Storage;
use Session;

abstract class Board extends \Bundle\Component\Board\Board
{
    public function __construct($req)
    {
        parent::__construct($req);//상속받기
        //아래처럼 필요한부분 냅두고 하면 보고싶은거 볼수있음.

        $boardConfig = new BoardConfig($req['bdId']);
        $this->cfg = &$boardConfig->cfg;    //게시판 설정 세팅
        //print_r($this->cfg);//보고싶은거 print_r
        if (!$this->cfg) {
            throw new \Exception(__('게시판 설정이 되지않았습니다.'));
        }
        $this->storage = Storage::disk(Storage::PATH_CODE_BOARD, $this->cfg['bdUploadStorage']);    //파일저장소세팅
        //$this->buildQuery = BoardBuildQuery::init($req['bdId'], $boardConfig);    //DAO세팅 //필요없는거 주석
        //print_r($this->cfg);
        //print_r($this->storage);
        //print_r(Storage::PATH_CODE_BOARD);exit;
        //print_r($this->cfg['bdUploadStorage']);exit; //보고싶은거

        
    }
	
	public function saveData()
	{
		$aa=parent::saveData();
        $arrBind = [];
        
        if($this->req['mode']=='write'){

            $strSQL = "SELECT sno FROM es_bd_goodsreview ORDER BY sno DESC LIMIT 1";
            $sresult = $this->db->query_fetch($strSQL,null);
            
            $sno = $sresult[0]['sno'];

            $query = "UPDATE es_bd_goodsreview SET goodsSpeedPt = ?, goodsQualityPt = ?, goodsStatePt = ?, goodsColorPt = ?, goodsMaterialPt = ? WHERE sno = ?";
            $this->db->bind_param_push($arrBind2, 'i', $this->req['goodsSpeedPt']);
            $this->db->bind_param_push($arrBind2, 'i', $this->req['goodsQualityPt']);
            $this->db->bind_param_push($arrBind2, 'i', $this->req['goodsStatePt']);
            $this->db->bind_param_push($arrBind2, 'i', $this->req['goodsColorPt']);
            $this->db->bind_param_push($arrBind2, 'i', $this->req['goodsMaterialPt']);
            $this->db->bind_param_push($arrBind2, 'i', $sno);
            $this->db->bind_query($query, $arrBind2);
        }
        
		if($this->req['mode']=='modify'){
			$abc = 'sno';
			$sno = $this->req['sno'];			
		}else {
			$abc = 'groupNo';
		    $sno = Session::get('groupNo_' . $this->cfg['bdId']);		
		}

		$strSQL ="UPDATE es_bd_event SET minho = ? WHERE {$abc} = ?";
		$this->db->bind_param_push($arrBind, 's', $this->req['minho']);
		$this->db->bind_param_push($arrBind, 'i', $sno);
		$this->db->bind_query($strSQL, $arrBind);
		
		return $aa;
    } // end func
    


}