<?php
namespace Controller\Admin\Board;

use Request;

class BestReviewGoController extends \Controller\Admin\Controller
{
    

    public function index()
	{
        $this->db = \App::load('DB');
        $sno = Request::post()->get('sno');
        $uploadFileNm = Request::post()->get('uploadFileNm');
        $saveFileNm = Request::post()->get('saveFileNm');
        $arrBind = [];

        $saveFileNm[0] = str_replace('tmp_','',$saveFileNm[0]);
        
        //print_r($saveFileNm[0]);
        $strSQL = "UPDATE es_plusReviewArticle SET bestUploadFileNm = ?, bestSaveFileNm = ? WHERE sno = ?";
        // print_r($strSQL);
        $this->db->bind_param_push($arrBind, 's', $uploadFileNm[0]);
        $this->db->bind_param_push($arrBind, 's', $saveFileNm[0]);
        $this->db->bind_param_push($arrBind, 'i', $sno);
        //print_r($arrBind);
        $this->db->bind_query($strSQL, $arrBind);
        echo "<script>alert('등록완료');history.go(-2);</script>";exit;
        
    }
}