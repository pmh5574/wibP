<?php
namespace Controller\Mobile\Board;

use App;
use Request;

class PlusReviewListController extends \Bundle\Controller\Mobile\Board\PlusReviewListController
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
            

            $goodsNo = Request::get()->get('goodsNo');
            $whereselect = Request::post()->get('whereselect');
            $orderbyselect = Request::post()->get('orderbyselect');
            $page = Request::get()->get('page');

            if($page == null){
                $page = Request::post()->get('page');
            }
            if($goodsNo == null){
                $goodsNo = Request::post()->get('goodsNo2');
            }
            if($whereselect == null || $whereselect == "all"){
                $whereselect = "";
                $countwhere = "";
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

            /** 페이징(더보기버튼) */
            $start = ($page-1)*10;
        
            $ct = "SELECT COUNT(*) AS cnt FROM es_plusReviewArticle WHERE goodsNo = " .$goodsNo.$whereselect ;
            $rt = $this->db->query($ct);
            $cnt = $this->db->fetch($rt);

            $total = $cnt['cnt'];
            $pcnt = $total-$start;

            $page2 = ($page*10);
            
            $ab = ($page-1)%10;
            $startpage = $page-$ab;
            $endpage = $startpage+9;
            $pagecount = $total/10;
            $nowpage = $page;
            $kk ='&key='.$key.'&keyword='.$keyword;

            if($endpage>$pagecount){$endpage = ceil($pagecount);}
            
            $this->setData('pagecount',$endpage);

            $strSQL = "SELECT *  FROM es_plusReviewArticle 
            WHERE goodsNo = ".$goodsNo.$whereselect
            .$orderbyselect." LIMIT 0, ".$page2;
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

            //리뷰3개올려주기
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
            $strSQL2 = "SELECT * FROM es_plusReviewArticle WHERE goodsNo = " .$goodsNo. " AND saveFileNm != '' ORDER BY regDt DESC LIMIT 27";
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
