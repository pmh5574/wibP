<?php
namespace Controller\Front\Main;

use Globals;
use Session;
use Response;
use Request;
use Exception;

/**
 * 팝업창
 */
class IndexViewController extends \Controller\Front\Controller
{
	protected $db = null;
    
    public function __construct() 
	{
        
        parent::__construct();
        
        if (!is_object($this->db)) {
            $this->db = \App::load('DB');
        }
    }
  
    public function index()
    {
		$sno = Request::get()->get('sno');
        
		$strSQL = "SELECT epr.*, eg.imagePath, eg.goodsNm FROM es_plusReviewArticle AS epr JOIN es_goods AS eg ON eg.goodsNo = epr.goodsNo WHERE epr.sno=".$sno;
		$result = $this->db->query_fetch($strSQL,null);

		foreach($result as $key => $value){
			$aa = "SELECT imageName FROM es_goodsImage WHERE imageKind = 'main' AND goodsNo = ".$value['goodsNo'];
			$re = $this->db->query_fetch($aa,null);
			//print_r($re);
			$result[$key]['popimage'] = $re;
			$strSSQL = "SELECT COUNT(*) AS cnt FROM es_plusReviewArticle WHERE goodsNo = ".$value['goodsNo'];
			$rre = $this->db->query_fetch($strSSQL,null);
			$sstrSSQL = "SELECT goodsPt FROM es_plusReviewArticle WHERE goodsNo = ".$value['goodsNo'];
			$rree = $this->db->query_fetch($sstrSSQL,null);
			//print_r($rree);
			$qwe=0;
			foreach($rree as $k => $v){
				$qwe += $v['goodsPt'];
			}
			$qqq = ($qwe/$rre[0]['cnt']);
			$qq = round($qqq, 1);
			//print_r($qq);
			$result[$key]['avgpt'] = $qq;
			$result[$key]['pophap'] = $rre[0]['cnt'];
		}
		
		
		$this->setData("sno",$sno);
		$this->setData("result",$result); 
    }
}




