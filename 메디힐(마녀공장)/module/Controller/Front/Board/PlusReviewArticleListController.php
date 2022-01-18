<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Enamoo S5 to newer
 * versions in the future.
 *
 * @copyright Copyright (c) 2015 GodoSoft.
 * @link http://www.godo.co.kr
 */
namespace Controller\Front\Board;

use App;
use Request;

class PlusReviewArticleListController extends \Bundle\Controller\Front\Board\PlusReviewArticleListController
{
	public function index()
    {
        parent::index();
        if ($data = $this->getData('data')) {
          
			$this->db = \App::load('DB');
            

            foreach ($data['list'] as $key => $value) {
				
                foreach ($value as $k => $v) {
                    if (in_array($k, $chkFields)) {
                        $obj->convert($v, "plus_review");
						
                        $data['list'][$key][$k] = $v;
                    }
                } // endforeach
            } // endforeach
            // orderbyselect 
            // whereselect

            /*페이징 시작*/
            $page = Request::post()->get('page');
            $page = Request::get()->get('page');  
            
            $whereselect = Request::post()->get('whereselect');
            if(!$whereselect){
                $whereselect = Request::get()->get('whereselect');
            }
            $orderbyselect = Request::post()->get('orderbyselect');
            
            if($page == ''){$page = 1;}
            if($whereselect == null || $whereselect == "all"){
                $countwhere = "";
            }    
            if($whereselect == "photo"){
                $countwhere = " WHERE saveFileNm != ''";
            }
            if($whereselect == "text"){
                $countwhere = " WHERE saveFileNm = '' ";
            }
            $start = ($page-1)*20;
    
            $ct = "SELECT COUNT(*) AS cnt FROM es_plusReviewArticle ".$countwhere;
            $rt = $this->db->query($ct);
            $cnt = $this->db->fetch($rt);
    
            $total = $cnt['cnt'];
            $pcnt = $total-$start;
            
            $ab = ($page-1)%10;
            $startpage = $page-$ab;
            $endpage = $startpage+9;
            $pagecount = $total/20;
            $nowpage = $page;
            $kk ='&key='.$key.'&keyword='.$keyword;
            
            if($endpage>$pagecount){$endpage = ceil($pagecount);}
            
            $this->setData('startpage', $startpage);
            $this->setData('endpage', $endpage);
            $this->setData('pagecount', ceil($pagecount));
            $this->setData('nowpage', $nowpage);
            $this->setData('pcnt', $pcnt);
            $this->setData('aa', $aa);

            
            $testpage = '';
            $testpage .= '<div class="pagination">';
            $testpage .= '<ul>';
            if($startpage != 1){ 
                $goAjaxPage = "'page=";
                $goAjaxPageNm = $nowpage;
                $testpage .= '<li class="btn_page btn_page_first">';
                $testpage .= '<a aria-label="First" href="javascript:goAjaxPage2('.$goAjaxPage.'1\')"></a>';
                $testpage .= '</li>';
                $testpage .= '<li class="btn_page btn_page_prev">';
                $testpage .= '<a aria-label="Previous" href="javascript:goAjaxPage2('.$goAjaxPage.($startpage-10).'\')"></a>';
                $testpage .= '</li>';
            }

            for($i = $startpage; $i <= $endpage; $i++){
                if($nowpage == $i){$act = "class = 'on'";}else{$act = "";}
                $goAjaxPage = "'page=".$i."'";
                $testpage .= '<li '.$act.'>';
                if($nowpage != $i){
                    $testpage .= '<a href="javascript:goAjaxPage2('.$goAjaxPage.')">';
                   
                    $testpage .= $i;
                    $testpage .= '</a>';
                }else{
                    $testpage .= '<span> '.$i.' </span>';
                }
               
                $testpage .= '</li>';
            }

            if($endpage < ceil($pagecount)){           
                $goAjaxPage = "'page=";
                $goAjaxPageNm = $nowpage;
                $testpage .= '<li class="btn_page btn_page_next">';
                $testpage .= '<a aria-label="Next" href="javascript:goAjaxPage2('.$goAjaxPage.($startpage+10).'\')"></a>';
                $testpage .= '</li>';
                $testpage .= '<li class="btn_page btn_page_last">';
                $testpage .= '<a aria-label="Last" href="javascript:goAjaxPage2('.$goAjaxPage.ceil($pagecount).'\')"></a>';
                $testpage .= '</li>';
            } 
            $testpage .= '</ul>';   
            $testpage .= '</div>';
            //{=data.pagination}원래 html에 있던 기본 페이징
            $this->setData('testpage', $testpage);
            //페이징끝
            
            

            if($whereselect == null || $whereselect == "all"){
                $whereselect = "";
            }    
            if($whereselect == "photo"){
                $whereselect = " AND ep.saveFileNm != '' ";
            }
            if($whereselect == "text"){
                $whereselect = " AND ep.saveFileNm = '' ";
            }
            if($orderbyselect == null){
                $orderbyselect = " regDt DESC";
            }
            
            //전체보기 포토보기 텍스트보기
            $aa = "SELECT ep.*, CASE WHEN ep.saveFileNm = '' THEN 1 ELSE 2 END AS sf, eg.goodsNm, eg.imagePath, egi.imageName 
            FROM es_plusReviewArticle AS ep 
            JOIN es_goods AS eg ON eg.goodsNo = ep.goodsNo 
            JOIN es_goodsImage AS egi ON egi.goodsNo = ep.goodsNo 
            WHERE egi.imageKind = 'main' ".$whereselect." ORDER BY ".$orderbyselect." 
            LIMIT ".$start.", 20";
            $re = $this->db->query_fetch($aa,null);
            
            foreach($re as $key => $value){
                $str = $re[$key]['regDt'];
                $re[$key]['regDt'] = substr($str,0,10);

                $str2 = $re[$key]['writerId'];
                $len = strlen($str2);
                $re[$key]['writerId'] = substr($str2,0,2) .str_repeat('*',$len-2);

                $fileNm = $re[$key]['saveFileNm'];
                $saveFileNm = explode("^|^",$fileNm);               

                
                if($saveFileNm[0]){
                    $re[$key]['saveFileNm'] = array();
                    foreach ($saveFileNm as $k => $val){
                        if($value['saveFileNm']){
                            
                            $is_file_exist = file("https://www.medihealshop.com/data/plus_review/{$value['goodsNo']}/t/square_{$val}");
                            
                            if($is_file_exist){
                                
                                $re[$key]['saveFileNm'][$k]['thumSrc'] = "/data/plus_review/{$value['goodsNo']}/t/square_".$val;
                                $re[$key]['saveFileNm'][$k]['Src'] = "/data/plus_review/{$value['goodsNo']}/".$val;
                                
                            }else{
                                
                                $re[$key]['saveFileNm'][$k]['thumSrc'] = "/data/plus_review/{$value['goodsNo']}/".$val;
                                $re[$key]['saveFileNm'][$k]['Src'] = "/data/plus_review/{$value['goodsNo']}/".$val;
                                
                            }
                        }
                    }
                }
                
                $re[$key]['contents'] = str_replace('\r\n','<br>',$re[$key]['contents']); 
                $re[$key]['contents'] = str_replace('\n','',$re[$key]['contents']);
                $re[$key]['contents'] = str_replace('\r','',$re[$key]['contents']);     
                $re[$key]['contents'] = stripslashes($re[$key]['contents']);  
            }
            
            //베스트리뷰
            $strSQL = "SELECT ep.*, eg.goodsNm
            FROM es_plusReviewArticle AS ep
            JOIN es_goods AS eg ON ep.goodsNo = eg.goodsNo 
            WHERE ep.bestReview = 'y' ORDER BY regDt DESC LIMIT 6";
            $result = $this->db->query_fetch($strSQL, null);
            
            foreach($result as $key => $value){

                $str = $result[$key]['regDt'];
                $result[$key]['regDt'] = substr($str,0,10);

                $str2 = $result[$key]['writerId'];
                $len = strlen($str2);
                $result[$key]['writerId'] = substr($str2,0,2) .str_repeat('*',$len-2);

                $fileNm = $result[$key]['saveFileNm'];
                $saveFileNm = explode("^|^",$fileNm);                      
                
                    
                $is_file_exist = file("https://www.medihealshop.com/data/plus_review/{$value['goodsNo']}/t/square_{$saveFileNm[0]}");

                if($is_file_exist){

                    $result[$key]['saveFileNm'] = "/data/plus_review/{$value['goodsNo']}/t/square_".$saveFileNm[0];

                }else{

                    $result[$key]['saveFileNm'] = "/data/plus_review/{$value['goodsNo']}/".$saveFileNm[0];

                }
                

                $result[$key]['contents'] = str_replace('\r\n','<br>',$result[$key]['contents']);
                $result[$key]['contents'] = str_replace('\n','',$result[$key]['contents']);   
                $result[$key]['contents'] = str_replace('\r','',$result[$key]['contents']); 
                $result[$key]['contents'] = stripslashes($result[$key]['contents']);
            }
		      
            //포토리뷰
            $strSQL2 = "SELECT * FROM es_plusReviewArticle WHERE saveFileNm != '' ORDER BY regDt DESC LIMIT 36";
            $result2 = $this->db->query_fetch($strSQL2, null);

            //리뷰하나에 saveFileNm 여러개있으면 나누기
            foreach($result2 as $key => $value){
                $fileNm = $result2[$key]['saveFileNm'];
                $saveFileNm = explode("^|^",$fileNm);               
                
                
                if($value['saveFileNm']){
                    
                    $is_file_exist = file("https://www.medihealshop.com/data/plus_review/{$value['goodsNo']}/t/square_{$saveFileNm[0]}");
                    
                    if($is_file_exist){
                        
                        $result2[$key]['saveFileNm'] = "/data/plus_review/{$value['goodsNo']}/t/square_".$saveFileNm[0];
                        
                    }else{
                        
                        $result2[$key]['saveFileNm'] = "/data/plus_review/{$value['goodsNo']}/".$saveFileNm[0];
                        
                    }
                }
            }
            //평점 구하기
            $pt = "SELECT AVG(goodsPt) AS avg, COUNT(*) AS ptTotal FROM es_plusReviewArticle";
            $pat = $this->db->query_fetch($pt,null);
           
            $pat[0]['avg']=number_format($pat[0]['avg'], 1);
            $this->setData('pt',$pat);
            $this->setData('imglist',$result2);
            $this->setData("result", $result);
            $data['list'] = $re;
            $this->setData("data", $data);
            
        }
    }
}