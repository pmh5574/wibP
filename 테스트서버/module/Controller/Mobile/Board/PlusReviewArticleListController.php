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

        $photo = Request::post()->get('photo');
        $orderby = Request::post()->get('orderby');
        //print_r($photo);
        //print_r($orderby);
        if($orderby == null || $orderby == "bestReview DESC, regDt DESC, uploadFileNm DESC"){
            $orderby = "bestReview DESC, qw DESC, regDt DESC";
            $casewhen = " (CASE WHEN ep.uploadFileNm = '' THEN 1 ELSE 2 END) AS qw, ";
        }else{
            $casewhen = "";
        }
        if($photo == "photo"){
            $photo = "AND ep.uploadFileNm";
        }else{
            $photo = "";
        }

        $strSQL = "SELECT ep.*, ". $casewhen ." eg.goodsNm, eg.imagePath, egi.imageName 
        FROM es_plusReviewArticle AS ep 
        JOIN es_goods AS eg ON ep.goodsNo = eg.goodsNo 
        JOIN es_goodsImage AS egi on ep.goodsNo = egi.goodsNo 
        WHERE egi.imageKind = 'main' ".$photo." ORDER BY ".$orderby;
        $result = $this->db->query_fetch($strSQL, null);
        //print_r($strSQL);
        foreach($result as $key => $value){
            $str = $result[$key]['regDt'];
            $result[$key]['regDt'] = substr($str,0,10);
        }

        //베스트후기
        $bestrSQL = "SELECT * FROM es_plusReviewArticle WHERE bestReview = 'y' ORDER BY regDt DESC LIMIT 3";
        $beresult = $this->db->query_fetch($bestrSQL, null);
        //print_r($result);
        
        $this->setData("beresult", $beresult);

        $this->setData("result",$result);
    }
}