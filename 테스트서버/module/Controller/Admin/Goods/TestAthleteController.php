<?php
namespace Controller\Admin\Goods;

use Request;

class TestAthleteController extends \Controller\Admin\Controller
{

    public function index()    
    {
        // --- 메뉴 설정
		if(Request::get()->get('delFl') === 'y') {
			$this->callMenu('goods', 'goods', 'delete_list');
		} else {
			$this->callMenu('goods', 'goods', 'list');
		}
        $this->db = \App::load('DB');

        $key = Request::get()->get('key');
		$keyword = Request::get()->get('keyword');
		
        $wh = "";
		if($key == 'athlete' && $keyword != null){
			$wh = "WHERE athlete LIKE '%".$keyword."%'";
        }else if($key == 'brandNm' && $keyword != null){
            $wh = "WHERE brandNm LIKE '%".$keyword."%'";
        }else if($key == 'brandCd' && $keyword){
            $wh = "WHERE brandCd LIKE '%".$keyword."$'";
        }
        else{
            $wh = "";
        }
        $page = Request::get()->get('page');
		if($page == ''){$page = 1;}
		$start = ($page-1)*10;
		
		$strSQL = "SELECT * FROM es_athlete ".$wh." ORDER BY sno DESC LIMIT ".$start.",10";
        $result = $this->db->query_fetch($strSQL, null);
		
		$ct = "SELECT COUNT(*) as cnt FROM es_athlete ".$wh;
		$re = $this->db->query($ct);
		$cnt = $this->db->fetch($re);

        $total = $cnt['cnt'];
		$pcnt = $total-$start;
        
		$ab = ($page-1)%10;
		$startpage = $page-$ab;
		$endpage = $startpage+9;
		$pagecount = $total/10;
		$nowpage = $page;
		$kk ='&key='.$key.'&keyword='.$keyword;
		if($endpage>ceil($pagecount)){$endpage = ceil($pagecount);}
		//print_r($kk);

		$this->setData('startpage', $startpage);
        $this->setData('endpage', $endpage);
        
		$this->setData('pagecount', ceil($pagecount));
		$this->setData('nowpage', $nowpage);
		$this->setData('pcnt', $pcnt);
		
     
		$testpage = '';
		$testpage .= '<ul class="pagination pagination-sm">';
		if($startpage != 1){ 
			//$nowpage = $page-1;
			$testpage .= '<li class="front-page front-page-first">';
			$testpage .= '<a aria-label="First" href="/goods/test_athlete.php?page=1'.$kk.'"><<맨앞</a>';
			$testpage .= '</li>';
			$testpage .= '<li class="front-page front-page-prev">';
			$testpage .= '	<a aria-label="Previous" href="/goods/test_athlete.php?page='.($nowpage-1).$kk.'"><이전</a>';
			$testpage .= '</li>';
		}

 
		for($i = $startpage; $i <= $endpage; $i++){
			if($nowpage == $i){$act = "active";}else{$act = "";}
			
			$testpage .= '<li class="'.$act.'">';
			$testpage .= '<a href="/goods/test_athlete.php?page='.($i).$kk.'">';
			$testpage .= '<span> '.$i.' </span>';
			$testpage .= '</a>';
			$testpage .= '</li>';
		}

		if($endpage < ceil($pagecount)){ 
			
			$testpage .= '<li class="front-page front-page-next">';
			$testpage .= '<a aria-label="Next" href="/goods/test_athlete.php?page='.($nowpage+1).$kk.' ">>다음</a>';
			$testpage .= '</li>';
			$testpage .= '<li class="front-page front-page-last">';
			$testpage .= '<a aria-label="Last" href="/goods/test_athlete.php?page='.ceil($pagecount).$kk.'">>>맨뒤</a>';
			$testpage .= '</li>';
		} 
        $testpage .= '</ul>';   
		
		
		$this->setData('testpage', $testpage); 
        $this->setData("result", $result);

				
    }
}