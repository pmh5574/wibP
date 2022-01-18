<?php
namespace Controller\Front\Frame;

use Component\Storage\WibStorage;
use Request;


class UploadController extends \Controller\Front\Controller
{
    public function index()
    {
        $filesValue = Request::files()->toArray();
        $nFile = $filesValue['file'];
        $exImg = explode(".", $nFile['name']);
        
        $randomNum = rand(0, 10000);
        
        $imageExt = strrchr($nFile['name'], '.');
        $targetImageFile = 'F'.$randomNum.'_'.$exImg[0]."_".date('YmdHis'). $imageExt; // 이미지명 공백 제거
        $tmpImageFile = $nFile['tmp_name'];
        
        $storage = new WibStorage(1);
        $storage->upload($tmpImageFile, $targetImageFile);
        
        
        $this->json(['img' => '/data/wibUpload/'.$targetImageFile]);
        
        exit;
    }
}
