<?php
namespace Controller\Admin\Goods;

use App;
use Exception;
use Request;
use Globals;


class TestGoodsController extends \Controller\Admin\Controller
{

	protected $db = null;
    
    public function __construct() {
        
        parent::__construct();
        
        if (!is_object($this->db)) {
            $this->db = \App::load('DB');
        }
    }


    public function index()    {

		// --- 메뉴 설정
		if(Request::get()->get('delFl') === 'y') {
			$this->callMenu('goods', 'goods', 'delete_list');
		} else {
			$this->callMenu('goods', 'goods', 'list');
		}

		// --- 상품 리스트 데이터		
		$key = Request::get()->get('key');
		$keyword = Request::get()->get('keyword');
		
		
        $wh = "";
		if($key == 'goodsNm' && $keyword != null){
			$wh = "AND eg.goodsNm LIKE '%".$keyword."%'";
			$wwh = "WHERE goodsNm LIKE '%".$keyword."%'";
		}
		if($key == 'goodsNo' && $keyword != null){
			$wh = "AND eg.goodsNo LIKE '%".$keyword."%'";
			$wwh = "WHERE goodsNo LIKE '%".$keyword."%'";
		}	
		$page = Request::get()->get('page');
		if($page == ''){$page = 1;}
		$start = ($page-1)*7;
		
		$strSQL = "SELECT eg.*, egi.imageName FROM es_goods AS eg JOIN es_goodsImage AS egi ON eg.goodsNo = egi.goodsNo WHERE egi.imageKind = 'list' {$wh} ORDER BY goodsNo DESC LIMIT {$start},7";
		$aa = $this->db->query_fetch($strSQL, null);
		
		$ct = "SELECT COUNT(*) as cnt FROM es_goods {$wwh}";
		$result = $this->db->query($ct);
		$cnt = $this->db->fetch($result);

        $total = $cnt['cnt'];
		$pcnt = $total-$start;
        
		$ab = ($page-1)%3;
		$startpage = $page-$ab;
		$endpage = $startpage+2;
		$pagecount = $total/7;
		$nowpage = $page;
		$kk ='&key='.$key.'&keyword='.$keyword;
		if($endpage>ceil($pagecount)){$endpage = ceil($pagecount);}
		//print_r($kk);

		$this->setData('startpage', $startpage);
		$this->setData('endpage', $endpage);
		$this->setData('pagecount', ceil($pagecount));
		$this->setData('nowpage', $nowpage);
		$this->setData('pcnt', $pcnt);
		$this->setData('aa', $aa);
     
		$testpage = '';
		$testpage .= '<ul class="pagination pagination-sm">';
		if($startpage != 1){ 
			//$nowpage = $page-1;
			$testpage .= '<li class="front-page front-page-first">';
			$testpage .= '<a aria-label="First" href="/goods/test_goods.php?page=1'.$kk.'"><<맨앞</a>';
			$testpage .= '</li>';
			$testpage .= '<li class="front-page front-page-prev">';
			$testpage .= '	<a aria-label="Previous" href="/goods/test_goods.php?page='.($nowpage-1).$kk.'"><이전</a>';
			$testpage .= '</li>';
		}

 
		for($i = $startpage; $i <= $endpage; $i++){
			if($nowpage == $i){$act = "active";}else{$act = "";}
			
			$testpage .= '<li class="'.$act.'">';
			$testpage .= '<a href="/goods/test_goods.php?page='.($i).$kk.'">';
			$testpage .= '<span> '.$i.' </span>';
			$testpage .= '</a>';
			$testpage .= '</li>';
		}

		if($endpage < ceil($pagecount)){ 
			
			$testpage .= '<li class="front-page front-page-next">';
			$testpage .= '<a aria-label="Next" href="/goods/test_goods.php?page='.($nowpage+1).$kk.' ">>다음</a>';
			$testpage .= '</li>';
			$testpage .= '<li class="front-page front-page-last">';
			$testpage .= '<a aria-label="Last" href="/goods/test_goods.php?page='.ceil($pagecount).$kk.'">>>맨뒤</a>';
			$testpage .= '</li>';
		} 
        $testpage .= '</ul>';   
		
		
		$this->setData('testpage', $testpage);
		
				
    }
}