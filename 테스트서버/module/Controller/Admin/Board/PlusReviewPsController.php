<?php
namespace Controller\Admin\Board;

use Request;
use Component\Storage\Storage;
use Component\Board\Board;

class PlusReviewPsController extends \Bundle\Controller\Admin\Board\PlusReviewPsController
{
    protected $storage;   

	public function index()
	{    
        $this->db = \App::load('DB');
        
        $allpost = Request::post()->toArray();
        
        if(strpos($allpost['queryString'],'bestadd') !== false){

            $beforefile = Request::files()->get('beforeimages');
            $afterfile = Request::files()->get('afterimages');
            $sno = Request::post()->get('sno');
            //print_r($allpost['goodsNo']);
            $arrBind = [];
            //print_r($allpost['queryString']);exit;
            //비포사진 서버에 업로드
            $file = $beforefile['name'];
            $tmp_file = $beforefile['tmp_name'];
            //$upload_directory = './data/plus_review/'.$allpost['goodsNo'].'/'.$file;//이건 원래 php방식
            $upload_directory = $allpost['goodsNo'].'/'.$file;//이건 고도몰 방식
            
            //에프터사진 서버에 업로드
            $file2 = $afterfile['name'];
            $tmp_file2 = $afterfile['tmp_name'];
            //$upload_directory2 = './data/plus_review/'.$allpost['goodsNo'].'/'.$file2;//이건 원래 php방식
            $upload_directory2 = $allpost['goodsNo'].'/'.$file2;//이건 고도몰 방식
            
            
            //storage값이 뭐가 들어가는지 Board.php와 Storage.php를 통해 확인
            $this->storage = Storage::disk(28,'local');
            //print_r($this->storage);
            
            // print_r("들어는 온거지12?");exit;
            
            //before나 after값이 하나 없을때
            if($beforefile['name'] && $afterfile['name'] == null ){
                echo "<script>alert('After 사진을 넣어주세요');</script>";exit;
            }else if($beforefile['name'] == null && $afterfile['name']){
                echo "<script>alert('Before 사진을 넣어주세요');</script>";exit;
            }
            if($beforefile['name'] && $afterfile['name']){
                $beforeafter = $beforefile['name']."^|^".$afterfile['name'];
            }else{
                $beforeafter = "";
            }
            // if($beforefile['name'] == null && $afterfile['name'] == null){

            //     echo "<script> 
            //         var re = confirm('비포에프터 사진을 삭제하시겠습니까?'); 
            //         if(re == true) alert('삭제되었습니다.'); 
            //         if(re == false) alert('취소되었습니다.');
            //         </script>";exit;
            // }else{

            $strSQL = "UPDATE es_plusReviewArticle SET bestUploadFileNm = ? WHERE sno = ?";
            $this->db->bind_param_push($arrBind, 's', $beforeafter);
            $this->db->bind_param_push($arrBind, 'i', $sno);           
            $this->db->bind_query($strSQL, $arrBind);

            if($beforefile['name'] == null && $afterfile['name'] == null){
                echo "<script>alert('삭제완료');</script>";exit;
            }else{
                echo "<script>alert('저장완료');</script>";
            }

            /** 원래 php 방식 사진 업로드 */
            //$a = move_uploaded_file($tmp_file, $upload_directory);
            //$b = move_uploaded_file($tmp_file2, $upload_directory2);
            
            /** 고도몰 방식 사진 업로드 */
            $q = $this->storage->upload($tmp_file, $upload_directory);
            $w = $this->storage->upload($tmp_file2, $upload_directory2);
            exit;
            //}
        }
        parent::index();
        
    }
}