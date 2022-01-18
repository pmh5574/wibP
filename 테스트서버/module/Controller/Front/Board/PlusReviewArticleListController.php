<?php
namespace Controller\Front\Board;

use App;
use Request;

class PlusReviewArticleListController extends \Bundle\Controller\Front\Board\PlusReviewArticleListController
{
    public function index()
    {
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
            ];
			$this->db = \App::load('DB');
            $obj->load();

            foreach ($data['list'] as $key => $value) {
				
                foreach ($value as $k => $v) {
                    if (in_array($k, $chkFields)) {
                        $obj->convert($v, "plus_review");
						
                        $data['list'][$key][$k] = $v;
                    }
                } // endforeach
            } // endforeach

			$gosearch = Request::post()->get('gosearch');
            $photo = Request::post()->get('photo');
            $orderby = Request::post()->get('orderby');
            //print_r($photo);

            if($orderby == null || $orderby == "bestReview DESC, regDt DESC, uploadFileNm DESC"){
                $orderby = "ep.bestReview DESC, qw DESC, ep.regDt DESC";
                $casewhen = " (CASE WHEN ep.uploadFileNm = '' THEN 1 ELSE 2 END) AS qw, ";
            }else{
                $casewhen = "";
            }
            //if($gosearch == null){
            //    $gosearch="ep.bestReview DESC, ep.regDt DESC, ep.uploadFileNm DESC";
                //print_r($gosearch);
            //}
            //포토사진 있는 것만 출력            
            if($photo == "photo"){
                $photo = "AND ep.uploadFileNm";
            }else{
                $photo = "";
            }
            //일반 후기
            $aa = "SELECT ep.*, ". $casewhen ." eg.goodsNm, eg.imagePath, egi.imageName 
            FROM es_plusReviewArticle AS ep 
            JOIN es_goods AS eg ON ep.goodsNo = eg.goodsNo 
            JOIN es_goodsImage AS egi on ep.goodsNo = egi.goodsNo 
            WHERE egi.imageKind = 'main' ".$photo." ORDER BY ".$orderby;
            //SELECT ep.*, (CASE WHEN ep.uploadFileNm = '' THEN 1 ELSE 2 END) AS qw, eg.goodsNm, eg.imagePath, egi.imageName FROM es_plusReviewArticle AS ep JOIN es_goods AS eg ON ep.goodsNo = eg.goodsNo JOIN es_goodsImage AS egi on ep.goodsNo = egi.goodsNo WHERE egi.imageKind = 'main' ORDER BY ep.bestReview DESC, qw DESC, ep.regDt DESC
            $re = $this->db->query_fetch($aa,null);
            foreach($re as $key => $value){
                $str = $re[$key]['regDt'];
                $re[$key]['regDt'] = substr($str,0,10);
                
                $fileNm = $re[$key]['saveFileNm'];
                $saveFileNm = explode("^|^",$fileNm); 
                $re[$key]['saveFileNm'] = $saveFileNm;
            }
            
            //print_r($aa);
            //foreach($re as $key => $value){
                //SELECT ep.*, eg.goodsNm, eg.imagePath, (SELECT egi.imageName FROM es_goodsImage AS egi WHERE egi.imageKind = 'main' AND egi.goodsNo = ep.goodsNo) FROM es_plusReviewArticle AS ep JOIN es_goods AS eg ON ep.goodsNo = eg.goodsNo ORDER BY regDt DESC중첩쿼리
                //(SELECT egi.imageName FROM es_goodsImage AS egi WHERE egi.imageKind = 'main' AND egi.goodsNo = ep.goodsNo)
                //$bb = "SELECT imageName FROM es_goodsImage WHERE imageKind = 'main' AND goodsNo = ". $value['goodsNo'];
                //$cc = $this->db->query_fetch($bb,null);
                //print_r($cc);
                //$re[$key]['imageName'] = $cc;
            //}

			//베스트후기
			$strSQL = "SELECT * FROM es_plusReviewArticle WHERE bestReview = 'y' ORDER BY regDt DESC LIMIT 3";
            $result = $this->db->query_fetch($strSQL, null);
            foreach($result as $key => $value){
                
                if($result[$key]['bestUploadFileNm']){
                    $fileNm = $result[$key]['bestUploadFileNm'];
                    $bestUploadFileNm = explode("^|^",$fileNm); 
                    $result[$key]['bestUploadFileNm'] = $bestUploadFileNm;
                }
                //print_r($result[$key]['bestUploadFileNm']);
            }
            //print_r($result);
            
			$this->setData("result", $result);
            $data['list'] = $re;
            $this->setData("data", $data);
            
        }
    }
}
