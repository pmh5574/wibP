<?php
namespace Controller\Admin\Goods;

use Request;
use Component\Storage\Storage;

class TestAthletePsController extends \Controller\Admin\Controller
{
    protected $storage;  

    public function index()    
    {
        $this->db = \App::load('DB');

        $_Athlete = Request::post()->toArray();
        $athleteUploadFileNm = Request::files()->get('athleteUploadFileNm');
        //  print_r($_Athlete);
        //  print_r($athleteUploadFileNm);
        // exit;
        $brandNm = "";

        if($_Athlete['mode'] == 'save'){
            $file = $athleteUploadFileNm['name'];
            $tmp_file = $athleteUploadFileNm['tmp_name'];
            $upload_directory = '1000000009/'.$file;

            $this->storage = Storage::disk(28,'local');

            $q = $this->storage->upload($tmp_file, $upload_directory);
            
            if($_Athlete['brandCd'] == "001"){
                $brandNm = "NIKE";
            }
            if($_Athlete['brandCd'] == "003"){
                $brandNm = "ADIDAS";
            }

            $strSQL = "INSERT INTO es_athlete SET brandCd = ?, athlete = ?, athleteUploadFileNm = ?, brandNm = ?";
            $this->db->bind_param_push($arrBind, 's', $_Athlete['brandCd']);
            $this->db->bind_param_push($arrBind, 's', $_Athlete['athlete']);
            $this->db->bind_param_push($arrBind, 's', $athleteUploadFileNm['name']);
            $this->db->bind_param_push($arrBind, 's', $brandNm);       
            $this->db->bind_query($strSQL, $arrBind);

            echo "<script>alert('저장완료'); location.href='../goods/test_athlete.php'; </script>";exit;
        }
        if($_Athlete['mode'] == 'modify'){
            if($athleteUploadFileNm['name']){
                $file = $athleteUploadFileNm['name'];
                $tmp_file = $athleteUploadFileNm['tmp_name'];
                $upload_directory = '1000000009/'.$file;
    
                $this->storage = Storage::disk(28,'local');
                
                $q = $this->storage->upload($tmp_file, $upload_directory);
            }else{
                $str = "SELECT athleteUploadFileNm FROM es_athlete WHERE sno = ".$_Athlete['sno'];
                $result = $this->db->query_fetch($str, null);
                $athleteUploadFileNm['name'] = $result[0][athleteUploadFileNm];
            }
            if($_Athlete['brandCd'] == "001"){
                $brandNm = "NIKE";
            }
            if($_Athlete['brandCd'] == "003"){
                $brandNm = "ADIDAS";
            }

            $strSQL = "UPDATE es_athlete SET brandCd = ?, athlete = ?, athleteUploadFileNm = ?, brandNm = ? WHERE sno = ?";
            $this->db->bind_param_push($arrBind, 's', $_Athlete['brandCd']);
            $this->db->bind_param_push($arrBind, 's', $_Athlete['athlete']);
            $this->db->bind_param_push($arrBind, 's', $athleteUploadFileNm['name']);   
            $this->db->bind_param_push($arrBind, 's', $brandNm);    
            $this->db->bind_param_push($arrBind, 'i', $_Athlete['sno']);   
            $this->db->bind_query($strSQL, $arrBind);

            echo "<script>alert('수정완료'); location.href='../goods/test_athlete.php'; </script>";exit;
        }
    }
}