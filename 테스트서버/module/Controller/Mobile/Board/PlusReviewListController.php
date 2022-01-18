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
            $obj = App::load('\Component\Board\BannedWords');
            $chkFields = [
                'subject',
                'contents',
                'goodsNm',
                'workedContents',
                'viewContents',
                'listContents',
            ];
            $obj->load();
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
            $orderby = Request::get()->get('sort');
            $photo = Request::get()->get('photo');

            //최신순
            if($orderby == "regDt"){
                $orderby = " ORDER BY regDt DESC";
                $casewhen = "";
            }else{//유용한 순
                $orderby = " ORDER BY bestReview DESC, qw DESC, regDt DESC";
                $casewhen = ", (CASE WHEN uploadFileNm = '' THEN 1 ELSE 2 END) AS qw ";
            }
            //포토후기만
            if($photo == "photo"){
                $photo = " AND uploadFileNm ";
            }else{
                $photo = " ";
            }
            
            $strSQL = "SELECT * " .$casewhen. " FROM es_plusReviewArticle WHERE goodsNo = ".$goodsNo.$photo.$orderby;
            $result = $this->db->query_fetch($strSQL,null);
            //print_r($strSQL);
            $data['list'] = $result;

            $this->setData("data", $data);
        } // endif
    }
}
