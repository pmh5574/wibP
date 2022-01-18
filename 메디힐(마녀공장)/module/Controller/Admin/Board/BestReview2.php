<?php
namespace Controller\Admin\Board;

use Request;

class BestReview2Controller extends \Controller\Admin\Controller
{
	public function index()
	{
		$this->db = \App::load('DB');

		$sno = Request::post()->get('sno');
		$resultfirm = Request::post()->get('resultfirm');
		$arrBind = [];
		
		if($resultfirm == true){
			$strSQL = "SELECT bestReview FROM es_plusReviewArticle WHERE sno = ".$sno;
			$result = $this->db->query_fetch($strSQL,null);
			$bestReview = $result[0]['bestReview'];
			if($bestReview == "n" || $bestReview == null){
				$bestReview="y";
			}else{$bestReview = "n";}
			$strSQL = "UPDATE es_plusReviewArticle SET bestReview = ? WHERE sno = ?";
			$this->db->bind_param_push($arrBind, 's', $bestReview);
			$this->db->bind_param_push($arrBind, 'i', $sno);
			$this->db->bind_query($strSQL, $arrBind);						
		}exit;
		
		
	}
}
