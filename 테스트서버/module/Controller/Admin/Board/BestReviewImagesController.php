<?php
namespace Controller\Admin\Board;

use Request;

class BestReviewImagesController extends \Controller\Admin\Controller
{
    

    public function index()
	{
        $this->db = \App::load('DB');
        $sno = Request::get()->get('sno');
        // $uploadFileNm = Request::post()->get('uploadFileNm');
        // $saveFileNm = Request::post()->get('saveFileNm');
        // $arrBind = [];

        if($sno == null || $sno == ""){
            $sno = Request::post()->get('sno');
        }
        // if($uploadFileNm[1]){
        //     $afteruploadFileNm = " AND ".$uploadFileNm[1];
        // } 
        // //print_r($uploadFileNm);

        // $strSQL = "UPDATE es_plusReviewArticle SET bestUploadFileNm = ? AND bestSaveFileNm = ? WHERE sno = ?";
        // print_r($strSQL);
        // $this->db->bind_param_push($arrBind, 's', $uploadFileNm[0]);
        // $this->db->bind_param_push($arrBind, 's', $saveFileNm[0]);
        // $this->db->bind_param_push($arrBind, 'i', $sno);
        // print_r($arrBind);
        // $this->db->bind_query($strSQL, $arrBind);
        
        $this->setData('sno',$sno);
    }
}