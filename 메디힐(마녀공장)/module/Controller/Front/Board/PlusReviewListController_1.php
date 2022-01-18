<?php
namespace Controller\Front\Board;

use App;
use Request;

class PlusReviewListController extends \Bundle\Controller\Front\Board\PlusReviewListController
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

            /*페이징 시작*/
            $page = Request::get()->get('page');  
            $goodsNo = Request::get()->get('goodsNo');
           
            $whereselect = Request::post()->get('whereselect');
            $orderbyselect = Request::post()->get('orderbyselect');
            //print_r($data['pagination']);
            //print_r($page);
            if($goodsNo == null){
                $goodsNo = Request::post()->get('goodsNo2');
            }
            if($page == ''){$page = 1;}
            if($whereselect == null || $whereselect == "all"){
                $countwhere = "";
            }    
            if($whereselect == "photo"){
                $countwhere = " AND saveFileNm != ''";
            }
            if($whereselect == "text"){
                $countwhere = " AND saveFileNm = '' ";
            }
            $start = ($page-1)*10;
    
            $ct = "SELECT COUNT(*) AS cnt FROM es_plusReviewArticle WHERE goodsNo = ".$goodsNo.$countwhere;
            $rt = $this->db->query($ct);
            $cnt = $this->db->fetch($rt);
    
            $total = $cnt['cnt'];
            $pcnt = $total-$start;
            
            $ab = ($page-1)%10;
            $startpage = $page-$ab;
            $endpage = $startpage+9;
            $pagecount = $total/10;
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
                $goAjaxPage = "'page='";
                $goAjaxPageNm = $nowpage;
                $testpage .= '<li class="btn_page btn_page_first">';
                $testpage .= '<a aria-label="First" href="javascript:goAjaxPage2('.$goAjaxPage.$nowpage.')"><<</a>';
                $testpage .= '</li>';
                $testpage .= '<li class="btn_page btn_page_prev">';
                $testpage .= '<a aria-label="Previous" href="javascript:goAjaxPage2('.$goAjaxPage.($nowpage-10).')"><</a>';
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
                $goAjaxPage = "'page='";
                $goAjaxPageNm = $nowpage;
                $testpage .= '<li class="btn_page btn_page_next">';
                $testpage .= '<a aria-label="Next" href="javascript:goAjaxPage2('.$goAjaxPage.($nowpage+10).')">></a>';
                $testpage .= '</li>';
                $testpage .= '<li class="btn_page btn_page_last">';
                $testpage .= '<a aria-label="Last" href="javascript:goAjaxPage2('.$goAjaxPage.ceil($pagecount).')">>></a>';
                $testpage .= '</li>';
            } 
            $testpage .= '</ul>';   
            $testpage .= '</div>';
            //{=data.pagination}원래 html에 있던 기본 페이징
            $this->setData('testpage', $testpage);
            //페이징끝

            if($page == null){
                $page = Request::get()->get('page');
            }
            
            if($whereselect == null || $whereselect == "all"){
                $whereselect = "";
            }
            if($whereselect == "photo"){
                $whereselect = " AND saveFileNm != '' ";
            }           
            if($whereselect == "text"){
                $whereselect = " AND saveFileNm = '' ";
            }
            if($orderbyselect == null){
                $orderbyselect = " ORDER BY regDt DESC";
            }

            $strSQL = "SELECT *  FROM es_plusReviewArticle 
            WHERE goodsNo = ".$goodsNo.$whereselect
            .$orderbyselect." LIMIT ".$start.",10";
            $result = $this->db->query_fetch($strSQL,null);
            
            foreach($result as $key => $value){
                $str = $result[$key]['regDt'];
                $result[$key]['regDt'] = substr($str,0,10);

                $str2 = $result[$key]['writerId'];
                $len = strlen($str2);
                $result[$key]['writerId'] = substr($str2,0,2) .str_repeat('*',$len-2);

                $fileNm = $result[$key]['saveFileNm'];
                $saveFileNm = explode("^|^",$fileNm);               
                
                
                if($saveFileNm[0]){
                    $result[$key]['saveFileNm'] = array();
                    foreach ($saveFileNm as $k => $val){
                        if($value['saveFileNm']){
                            
                            $is_file_exist = file("https://www.medihealshop.com/data/plus_review/{$value['goodsNo']}/t/square_{$val}");
                            
                            if($is_file_exist){
                                
                                $result[$key]['saveFileNm'][$k]['thumSrc'] = "/data/plus_review/{$value['goodsNo']}/t/square_".$val;
                                $result[$key]['saveFileNm'][$k]['Src'] = "/data/plus_review/{$value['goodsNo']}/".$val;
                                
                            }else{
                                
                                $result[$key]['saveFileNm'][$k]['thumSrc'] = "/data/plus_review/{$value['goodsNo']}/".$val;
                                $result[$key]['saveFileNm'][$k]['Src'] = "/data/plus_review/{$value['goodsNo']}/".$val;
                                
                            }
                        }
                    }
                }
                

                $result[$key]['contents'] = str_replace('\r\n','<br>',$result[$key]['contents']);
                $result[$key]['contents'] = str_replace('\n','',$result[$key]['contents']);
                $result[$key]['contents'] = str_replace('\r','',$result[$key]['contents']);         
                $result[$key]['contents'] = stripslashes($result[$key]['contents']);
                
                $result[$key]['auth'] = $data['list'][$key]['auth'];
            }

            //리뷰 3개 올려주기
			$str = "SELECT ep.*, eg.goodsNm
            FROM es_plusReviewArticle AS ep
            JOIN es_goods AS eg ON ep.goodsNo = eg.goodsNo 
            WHERE ep.goodsNo = ".$goodsNo." AND ep.saveFileNm != '' ORDER BY regDt DESC LIMIT 3";
            $res = $this->db->query_fetch($str, null);
           
            foreach($res as $key => $value){

                $str = $res[$key]['regDt'];
                $res[$key]['regDt'] = substr($str,0,10);

                $str2 = $res[$key]['writerId'];
                $len = strlen($str2);
                $res[$key]['writerId'] = substr($str2,0,2) .str_repeat('*',$len-2);

                $fileNm = $res[$key]['saveFileNm'];
                $saveFileNm = explode("^|^",$fileNm);

                $is_file_exist = file("https://www.medihealshop.com/data/plus_review/{$value['goodsNo']}/t/square_{$saveFileNm[0]}");
                
                if($is_file_exist){
                    
                    $res[$key]['saveFileNm'] = "/data/plus_review/{$value['goodsNo']}/t/square_".$saveFileNm[0];
                    
                }else{
                    
                    $res[$key]['saveFileNm'] = "/data/plus_review/{$value['goodsNo']}/".$saveFileNm[0];
                    
                }
                
                
                
                $res[$key]['contents'] = str_replace('\r\n','<br>',$res[$key]['contents']);
                $res[$key]['contents'] = str_replace('\n','',$res[$key]['contents']);
                $res[$key]['contents'] = str_replace('\r','',$res[$key]['contents']);
                $res[$key]['contents'] = stripslashes($res[$key]['contents']); 
            }
            
            //이미지 뿌려주기
            $strSQL2 = "SELECT * FROM es_plusReviewArticle WHERE goodsNo = " .$goodsNo. " ORDER BY regDt DESC LIMIT 27";
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
            
            $data['list'] = $result; 
            $this->setData('result',$res);
            $this->setData('imglist',$result2);
            $this->setData("data", $data);
        } // endif
    }
}
