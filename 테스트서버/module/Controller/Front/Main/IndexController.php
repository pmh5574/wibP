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
 * @link      http://www.godo.co.kr
 */
namespace Controller\Front\Main;

use Session;
use App;
use Exception;
use Request;
use Globals;

/**
 * 메인 페이지
 *
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class IndexController extends \Bundle\Controller\Front\Main\IndexController
{
    protected $db = null;
    
    public function __construct() {
        
        parent::__construct();
        
        if (!is_object($this->db)) {
            $this->db = \App::load('DB');
        }
    }
    

    public function index()
    {
        $arrs = [
            'mode' => 'mobile',
            'mobile' => '',
            'browser' => 'y'
        ];
        //Session::set('mobileInfo', $arrs);
        //$session = Session::all();
       // print_r($session);
        parent::index();
        if ($data = $this->getData('data')) {
            $obj = App::load('\Component\Board\BannedWords');
            $chkFields = [
                'subject',
                'contents',
                'goodsNm',
                'workedContents',
                'viewContents',
                'listContents',
                'reviewCnt',
            ];
            $obj->load();

            foreach ($data['list'] as $key => $value) {
                foreach ($value as $k => $v) {
                    if (in_array($k, $chkFields)) {
                        $obj->convert($v, "plus_review");

                        $data['list'][$key][$k] = $v;
                    }
                } // endforeach
            } // endforeach
            $this->setData("data", $data);
            //print_r($this);
        }
        
        /**
         * 타임세일 리스트 메인에 출력
         * 조건 : 타임세일중 종료일이 금일보다 클경우 and 가장최근에 입력한 타일세일 1개의 로우
         * 추가예정 : 삭제여부 [ delFl ] , 타임세일이 있는지 없는지 확인용 데이터 
         */
        $nowdate = date('Y-m-d H:i:s');
        $timesale = "SELECT * FROM es_timeSale where endDt > '".$nowdate."'  order by sno desc limit 0,1";
        $timesaleInfo = $this->db->query_fetch($timesale, null);
        
        // 시간 차이값 설정 및 셋팅
        $timeDuration = strtotime($timesaleInfo[0]['endDt']) - time();
        if($timeDuration < 0){
            $timeDuration = 0;
        }
        $this->setData('timeduration', $timeDuration);
        

 /**
		//연습=========================================================
		$arrBind = [];
		$strSQL = "INSERT INTO es_minho SET name = ?, contents = ?";
		$this->db->bind_param_push($arrBind, 's', 'minho');
		$this->db->bind_param_push($arrBind, 's', 'abc');
		$this->db->bind_query($strSQL, $arrBind);
		//print_r($arrBind);
	
		$aaac = [];
		$hoSQL = "SELECT * FROM es_minho";
		$aaac = $this->db->query_fetch($hoSQL, null);

		//for($i = 0; $i < count($aaac); $i++){
			//print_r($aaac);
		//}
		//print_r("이름: " . $aaac['name']. " 내용: " .$aaac['contents']. );
		
		$arrBBind = [];
		$strSQL = "UPDATE es_minho SET name = ? WHERE contents = ?";
		$this->db->bind_param_push($arrBBind, 's', 'nominho');
		$this->db->bind_param_push($arrBBind, 's', 'abc');
		$this->db->bind_query($strSQL, $arrBBind);
		//print_r($arrBBind);
		
		$upSQL = "SELECT * FROM es_minho";
		$aaab = $this->db->query_fetch($upSQL, null);
		
		
		//print_r("이름: ".$aaab[0]." 내용: ".$aaab[1].);
		
		$arrBBBind = [];
		$query = "DELETE FROM es_minho WHERE name = ?";
		$this->db->bind_param_push($arrBBBind, 's', 'nominho');
		$this->db->bind_query($query, $arrBBBind);

		//print_r($aaab);

		//배열연습========-----------------------------
 
		$grades = array('egoing'=>21, 'k8805'=>6, 'sorialgi'=>80);
		foreach($grades as $key => $value){
			print_r("키키: {$key} 값값: {$value}<br />");
		}

*/


//연습2=================================================================
		

		
		$strSQL = "SELECT * FROM es_bd_event";
		$aa = $this->db->query_fetch($strSQL, null);
		//$bb[] = </data/board/upload/event/ed4c9f154ceca4f9>;
        
		$this->setData('aa', $aa);
		
		//$this->setData('bb', $bb);
		//print_r($aa);
		//$this->setData('sno', $sno);
        //$this->setData('writerid', $writerid);
	    //$this->setData('subject', $subject);

//연습3==================================================================

  //리스트출력
		$page = Request::get()->get('page');
		$pageNum = Request::get()->get('pageNum');

		if($page == ''){$page = 1;}
		if($pageNum == ''){$pageNum = 10;}

		$start = ($page-1)*10;	

		$ct = "SELECT COUNT(*) AS cnt FROM es_goods";
		$result = $this->db->query($ct);
		$cnt = $this->db->fetch($result);

        $total = $cnt['cnt'];
		$pcnt = $total-$start;
        
		$ab = ($page-1)%10;
		$startpage = $page-$ab;
		$endpage = $startpage+9;
		$pagecount = $total/10;
		$nowpage = $page;
		
		if($endpage>$pagecount){$endpage = ceil($pagecount);}
		
		$this->setData('startpage', $startpage);
		$this->setData('endpage', $endpage);
		$this->setData('pagecount', ceil($pagecount));
		$this->setData('nowpage', $nowpage);
		$this->setData('pcnt', $pcnt);
		$this->setData('aa', $aa);
		

		$strSQL = "SELECT eg.*, egi.imageName FROM es_goods eg JOIN es_goodsImage egi ON eg.goodsNo = egi.goodsNo  WHERE egi.imageKind = 'main' ORDER BY goodsNo DESC LIMIT {$start},{$pageNum}";
		$result = $this->db->query_fetch($strSQL, null);

		
		$this->setData('page', $page);
		$this->setData('pageNum', $pageNum);
 
 //이 부분은 옵션값 전체를 뽑아야할때 html도 신경써야함
//print_r($result);
		//$result[3]['minho'] = "민호";
		//foreach($result as $key => $val){
			//echo $val['minOption'].'//';
			
		//	$sfd = "SELECT sno FROM es_goodsOption WHERE goodsNo =".$val['goodsNo'];
		//	$resultsssss = $this->db->query_fetch($sfd, null);

			//print_r($resultsssss);

		//	$result[$key]['minoption'] = $resultsssss;
	//	}


	//	foreach($result as $key => $val){
			//echo $val['minoption'].'//';
	//	}
//이 부분은 sno(옵션)값중에 하나만 뽑을때
		foreach($result as $key => $val){
			//echo $val['minOption'].'//';
			
			$sfd = "SELECT sno FROM es_goodsOption WHERE goodsNo =".$val['goodsNo'] ." LIMIT 0,1";
			$resultsssss = $this->db->query_fetch($sfd, null);

			//print_r($resultsssss);
			//$qwer1234=$resultsssss[0]['sno'].'/';
			//print_r($qwer1234);
			$result[$key]['minoption'] = $resultsssss[0]['sno'];
		}

		$this->setData('result', $result);//리스트출력

//페이징 시작
		$testpage = '';
		$testpage .= '<ul>';
		if($startpage != 1){ 
			$testpage .= '<li class="btn_page btn_page_first">';
			$testpage .= '<a aria-label="First" href="/main/index.php?page=1 "><<</a>';
			$testpage .= '</li>';
			$testpage .= '<li class="btn_page btn_page_prev">';
			$testpage .= '<a aria-label="Previous" href="/main/index.php?page='.($nowpage-1).'"><</a>';
			$testpage .= '</li>';
		}

		for($i = $startpage; $i <= $endpage; $i++){
			if($nowpage == $i){$act = "on";}else{$act = "";}	
			$testpage .= '<li class="'.$act.'">';
			if($nowpage != $i){
				$testpage .= '<a href="/main/index.php?page='.$i.'">';
			}
			$testpage .= '<span> '.$i.' </span>';
			$testpage .= '</a>';
			$testpage .= '</li>';
		}

		if($endpage < ceil($pagecount)){ 
			
			$testpage .= '<li class="btn_page btn_page_next">';
			$testpage .= '<a aria-label="Next" href="/main/index.php?page='.($nowpage+1).' ">></a>';
			$testpage .= '</li>';
			$testpage .= '<li class="btn_page btn_page_last">';
			$testpage .= '<a aria-label="Last" href="/main/index.php?page='.ceil($pagecount).'">>></a>';
			$testpage .= '</li>';
		} 
        $testpage .= '</ul>';   
		
		
		$this->setData('testpage', $testpage);
		
		$strSSQL = "SELECT ego.goodsNo, ego.sno FROM es_goodsOption AS ego JOIN es_goods eg ON ego.goodsNo = eg.goodsNo WHERE ego.goodsNo = {$goodsNo}";
		$rresult = $this->db->query($strSSQL);
		$asd = $this->db->fetch($rresult);
		//print_r($asd);
		if($asd['ego.goodsNo']==$asd['eg.goodsNo']){
		$testpage2 = '';
		$testpage2 .= '<form class="fourdata" type="post">';
		$testpage2 .= '<input type="hidden" name="mode" value="cartIn">';
		$testpage2 .= '<input type="hidden" name="scmNo" value="1">';
		$testpage2 .= '<input type="hidden" name="goodsNo[]" value="'.$asd['ego.goodsNo'].'">';
		$testpage2 .= '<input type="hidden" name="optionSno[]" value="">';
		$testpage2 .= '<input type="hidden" name="goodsCnt[]" value="1">';
		$testpage2 .= '<input type="hidden" name="couponApplyNo[]" value="">';	
		$testpage2 .= '<button id="cartBtn" type="button" class="btn_add_cart">장바구니</button>';
		$testpage2 .= '</form>';
		}
		$this->setData('testpage2', $testpage2);

		$goodsNo = Request::get()->get('goodsNo');
		//print_r($goodsNo);
		//SELECT ego.goodsNo, MIN(ego.sno) AS min_sno FROM es_goodsOption AS ego JOIN es_goods eg ON ego.goodsNo = eg.goodsNo WHERE ego.goodsNo = 1000000000
		$this->db->strField .= 'ego.goodsNo, MIN(ego.sno) AS min_sno';
		$this->db->strJoin = 'JOIN es_goods AS eg ON ego.goodsNo = eg.goodsNo';
		$this->db->strWhere = 'ego.goodsNo = ?';

		$this->db->bind_param_push($bindParam, i, $goodsNo);

		$query = $this->db->query_complete();
		$strSQL = 'SELECT ' . array_shift($query) . ' FROM es_goodsOption AS ego ' . implode(' ', $query);

		$tt = $this->db->query_fetch($strSQL, $bindParam, false);
		
		//print_r($tt['min_sno']);
		$this->setData('tt', $tt['min_sno']);
		
//플러스리뷰(후기)========================
	/**$gosearch = Request::get()->get('gosearch');
	
	if($gosearch == null || $gosearch == 1){
		$gosearch="regDt desc";
	}
	if($gosearch == 2){
		$gosearch="regDt asc";
	}
	if($gosearch == 3){
		$gosearch="char_length(contents) desc";
	}
	if($gosearch == 4){
		$gosearch="char_length(contents) asc";
	}*/



	$yyy = "SELECT ep.*, eg.goodsNm, eg.imagePath FROM es_plusReviewArticle AS ep JOIN es_goods AS eg ON ep.goodsNo = eg.goodsNo ORDER BY regDt DESC";
	$uuu = $this->db->query_fetch($yyy,null);
	
	foreach($uuu as $key => $value){
		$st = "SELECT imageName FROM es_goodsImage WHERE imageKind = 'main' AND goodsNo = ".$value['goodsNo'];
		$sst = $this->db->query_fetch($st,null);
		//print_r($sst);
		$uuu[$key]['imageName'] = $sst;
		$str = $uuu[$key]['regDt'];
		$uuu[$key]['regDt'] = substr($str,0,10);
	}

	$this->setData('uuu',$uuu);


//넘버즈인 연습===================================




/**		
		$upSQL = "SELECT * FROM es_minho";
		$aaab = $this->db->query_fetch($upSQL, null);
		
		
		foreach($aaab as $key => $value){
			//print_r("☆{$key}☆ => {$value['name']} <br />"); var_dump();
			echo $value['name'].'<br>';
		}
*/
//        $ch = curl_init(); 
//        curl_setopt($ch, CURLOPT_URL, 'https://www.instagram.com/midas_b'); 
//
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
//
//        curl_setopt($ch, CURLOPT_HEADER, 0); 
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
//
//        $data = curl_exec($ch); 
//        if (curl_error($ch))  
//        { 
//           exit('CURL Error('.curl_errno( $ch ).') '.curl_error($ch)); 
//        } 
//        curl_close($ch); 
//
//        var_dump($data);
        
    }
}