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
namespace Controller\Mobile\Board;

use App;
use Request;

class PlusReviewArticleListController extends \Bundle\Controller\Mobile\Board\PlusReviewArticleListController
{
	public function index()
    {
        parent::index();

        $this->db = \App::load('DB');

        $selectwhere = Request::post()->get('selectwhere');
        $page = Request::get()->get('page');
        if($page == null){
            $page = Request::post()->get('page');
        }
        if($selectwhere == null || $selectwhere == "all"){
            $selectwhere = "";
            $countwhere = "";
        }
        if($selectwhere == "photo"){
            $selectwhere = " AND ep.saveFileNm != '' ";
            $countwhere = " WHERE saveFileNm != '' ";
        }
        if($selectwhere == "text"){
            $selectwhere = " AND ep.saveFileNm = '' ";
            $countwhere = " WHERE saveFileNm = '' ";
        }
        if($orderbyselect == null){
            $orderbyselect = "regDt desc";
        }
        /** 페이징(더보기버튼) */
        $start = ($page-1)*10;
    
        $ct = "SELECT COUNT(*) AS cnt FROM es_plusReviewArticle ".$countwhere;
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
        //페이징

        $strSQL = "SELECT ep.*, eg.goodsNm, eg.imagePath, egi.imageName 
        FROM es_plusReviewArticle AS ep 
        JOIN es_goods AS eg ON ep.goodsNo = eg.goodsNo 
        JOIN es_goodsImage AS egi on ep.goodsNo = egi.goodsNo 
        WHERE egi.imageKind = 'main' ".$selectwhere." ORDER BY ".$orderbyselect."
        LIMIT 0, ".$page2;
        $result = $this->db->query_fetch($strSQL, null);
        
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
        }

        //베스트후기
        $bestrSQL = "SELECT ep.*, eg.goodsNm
        FROM es_plusReviewArticle AS ep
        JOIN es_goods AS eg ON ep.goodsNo = eg.goodsNo 
        WHERE ep.bestReview = 'y' ORDER BY regDt DESC LIMIT 6";
        $beresult = $this->db->query_fetch($bestrSQL, null);
    
        foreach($beresult as $key => $value){

            $str = $beresult[$key]['regDt'];
            $beresult[$key]['regDt'] = substr($str,0,10);

            $str2 = $beresult[$key]['writerId'];
            $len = strlen($str2);
            $beresult[$key]['writerId'] = substr($str2,0,2) .str_repeat('*',$len-2);

            $fileNm = $beresult[$key]['saveFileNm'];
            $saveFileNm = explode("^|^",$fileNm);               
            
            
            $is_file_exist = file("https://www.medihealshop.com/data/plus_review/{$value['goodsNo']}/t/square_{$saveFileNm[0]}");

            if($is_file_exist){

                $beresult[$key]['saveFileNm'] = "/data/plus_review/{$value['goodsNo']}/t/square_".$saveFileNm[0];

            }else{

                $beresult[$key]['saveFileNm'] = "/data/plus_review/{$value['goodsNo']}/".$saveFileNm[0];

            }

            $beresult[$key]['contents'] = str_replace('\r\n','<br>',$beresult[$key]['contents']); 
            $beresult[$key]['contents'] = str_replace('\n','',$beresult[$key]['contents']);
            $beresult[$key]['contents'] = str_replace('\r','',$beresult[$key]['contents']);
            $beresult[$key]['contents'] = stripslashes($beresult[$key]['contents']);   
        }

        //포토리뷰
        $strSQL2 = "SELECT * FROM es_plusReviewArticle WHERE saveFileNm != '' ORDER BY regDt DESC LIMIT 36";
        $result2 = $this->db->query_fetch($strSQL2, null);

        //리뷰하나에 saveFileNm 여러개있으면 나누기
        foreach($result2 as $key => $value){
            $fileNm = $result2[$key]['saveFileNm'];
            $saveFileNm = explode("^|^",$fileNm);               
            $result2[$key]['saveFileNm'] = $saveFileNm[0];
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
        
        
        $data['list'] = $result;
        $this->setData('pt',$pat);
        $this->setData('imglist',$result2);
        $this->setData("beresult", $beresult);
        $this->setData("data",$data);
    }
}