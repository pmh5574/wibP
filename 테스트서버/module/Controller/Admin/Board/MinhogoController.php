<?php
namespace Controller\Admin\Board;

use Request;

class MinhogoController extends \Controller\Admin\Controller
{
	public function index()
	{
		$this->db = \App::load('DB');

		$sno = Request::post()->get('sno');
		$resultfirm = Request::post()->get('resultfirm');
		$arrBind = [];
		
		//if($mh){print_r('asdasdaqwe');exit;}
		if($resultfirm == true){
			$strSQL = "SELECT bestReview FROM es_plusReviewArticle WHERE sno = ".$sno;
			$result = $this->db->query_fetch($strSQL,null);
			$bestReview = $result[0]['bestReview'];
			if($bestReview == "n" || $bestReview == null){
				$bestReview="y";
			}else{$bestReview = "n";}
				//print_r($rea);exit;
			$strSQL = "UPDATE es_plusReviewArticle SET bestReview = ? WHERE sno = ?";
			$this->db->bind_param_push($arrBind, 's', $bestReview);
            $this->db->bind_param_push($arrBind, 'i', $sno);
            print_r($arrBind);
			$this->db->bind_query($strSQL, $arrBind);
			//print_r($arrBind);exit;			
		}exit;//html문서가 없어서 끊어야 오류안남!
		//if($rea == 'ntrue'){
		//	$rea='n';
		//	//print_r($rea);exit;
		//	$aa = "UPDATE es_plusReviewArticle SET bestReview = ? WHERE sno = ?";
		//	$this->db->bind_param_push($arrBind, 's', $rea);
		//	$this->db->bind_param_push($arrBind, 's', $sno);
		//	$this->db->bind_query($aa, $arrBind);
		//	//print_r($arrBind);exit; exit이렇게쓰면 privew에서 볼수있음
		//}		
		
		
	}
}
