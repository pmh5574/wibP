<?php
namespace Component\Wib;

use Component\Wib\WibSql;
use Request;
use Component\Storage\Storage;
/**
 * 고도몰 기본쿼리를 편하게 사용할수 있도록 작업
 * select, update, insert 가능 [ 필요할때마다 기능 추가중 ]
 */
class WibScroll 
{
    
    protected $db = null;
    protected $table = 'es_goodsScroll';


    public function __construct() 
    {  
        
        $this->db = new WibSql();
        
    }
    
    public function saveScroll($postValue)
    {
        $filesValue = Request::files()->toArray();

        $scrollColor = $postValue['scrollColor'];
        $nFile       = $filesValue['scrollImg'];
        $scrollName  = $postValue['scrollName'];
        $msg         = '';
        
        if(!$scrollColor){
            $msg = '컬러값을 입력해주세요.';
            return $msg;
        }
        
        if(!$scrollName){
            $msg = '그룹명을 입력해주세요.';
            return $msg;
        }
        
        if(!$nFile['name']){
            $msg = '파일을 첨부해주세요.';
            return $msg;
        }

        if($nFile['name']){
            if (gd_file_uploadable($nFile, 'image') === true) {  // 이미지 업로드
            
                $_random = mt_rand(1, 10000);
                $micro = substr(microtime(), 2, 8);
                
                $targetImageFile = $_random.'_'.$micro;

                $tmpImageFile = $nFile['tmp_name'];
                
                Storage::disk(Storage::PATH_CODE_GOODS)->upload($tmpImageFile, $targetImageFile);
                $scrollImg = $targetImageFile;
            } else {
                return $msg = (__('이미지파일만 가능합니다.'));
            }
        }
        
        $data = [];
        
        if($scrollColor && $scrollImg && $scrollName){
            
            $data['scrollColor'] = [$scrollColor,'s'];
            $data['scrollName'] = [$scrollName,'s'];
            $data['scrollImg'] = [$scrollImg,'s'];
            
            $query = [
                $this->table,
                $data
            ];

            $sno = $this->db->WibInsert($query);

            if($sno){
                $msg = '저장 되었습니다.';
            }
            
        }
        
        return $msg;
    }
    
    public function delScroll($postValue) 
    {

        $sno = [];
        $img = [];
        
        foreach ($postValue['sno'] as $key => $value){
            
            $sno[] = $key;
            $img[] = $value;
            
        }

        //이미지 파일 삭제
        $this->delImg($img);
        
        $whereSet = ' WHERE';
        foreach ($sno as $value) {
            $whereSet .= " sno = {$value} or";
        }
        
        $whereQuery = substr($whereSet, 0 ,-2);

        $data = "DELETE FROM ".$this->table." {$whereQuery}";
        $this->db->WibNobind($data);
        
        return '삭제 됐습니다.';
    }
    
    public function updateScroll($postValue)
    {
        $filesValue = Request::files()->toArray();

        $scrollColor = $postValue['scrollColor'];
        $scrollName  = $postValue['scrollName'];
        $nFile       = $filesValue['scrollImg'];
        $sno         = $postValue['sno'];
        $msg         = '';
        
        if(!$scrollColor){
            $msg = '컬러값을 입력해주세요.';
            return $msg;
        }
        
        if(!$scrollName){
            $msg = '그룹명을 입력해주세요.';
            return $msg;
        }
        

        if($nFile['name']){
            
            if (gd_file_uploadable($nFile, 'image') === true) {  // 이미지 업로드

                //기존 이미지 삭제
                $this->delImg($postValue['orgImg']);
            
                $_random = mt_rand(1, 10000);
                $micro = substr(microtime(), 2, 8);
                
                $targetImageFile = $_random.'_'.$micro;

                $tmpImageFile = $nFile['tmp_name'];
                
                Storage::disk(Storage::PATH_CODE_GOODS)->upload($tmpImageFile, $targetImageFile);
                $scrollImg = $targetImageFile;
            } else {
                return $msg = (__('이미지파일만 가능합니다.'));
            }
        }
        
        $data = [];
        
        if($scrollColor && $scrollImg && $scrollName){
            
            $data['scrollColor'] = [$scrollColor,'s'];
            $data['scrollName'] = [$scrollName,'s'];
            $data['scrollImg'] = [$scrollImg,'s'];
            
        }else if($scrollColor && !$scrollImg && $scrollName){
            
            $data['scrollColor'] = [$scrollColor,'s'];
            $data['scrollName'] = [$scrollName,'s'];
            
        }
        
        
        $query = [
            $this->table,
            $data,
            array('sno'=>array($sno, 'i'))
        ];
        
        $this->db->WibUpdate($query);
        
        $msg = '수정 되었습니다.';
        
        return $msg;
    }
    
    public function delImg($img) 
    {
        foreach ($img as $value){
            Storage::disk(Storage::PATH_CODE_GOODS)->delete($value);
        }
    }
}
