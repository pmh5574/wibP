<?php
namespace Controller\Front\Order;

use Request;
use Component\Wib\WibSql;

class ImageGoodsController extends \Controller\Front\Controller{
    public function index()
    {
        $conn = new WibSql();
        
        $goodsImgSql = "SELECT goods_file1, goods_file2, goods_file3, goods_file4, goods_file5, eb_code FROM morning_goods_table where goods_file1 LIKE '%goods2%'";
        $goodsImgList = $conn->WibAll($goodsImgSql);
        
        foreach ($goodsImgList as $value) {
            
            // goodsNo
            $goodsNo = $conn->WibAll("select goodsNo from es_goods where goodsCd = {$value['eb_code']}");
            
            $file1 = explode("/", $value['goods_file1']);
            $fileval1 = $file1[count($file1)-1];
            
            $file2 = explode("/", $value['goods_file2']);
            $fileval2 = $file2[count($file2)-1];
            
            $file3 = explode("/", $value['goods_file3']); 
            $fileval3 = $file3[count($file3)-1];
            
            $file4 = explode("/", $value['goods_file4']);
            $fileval4 = $file4[count($file4)-1];
            
            $file5 = explode("/", $value['goods_file5']);
            $fileval5 = $file5[count($file5)-1];
            
            $file1Update = [
                'es_goodsImage',
                ['imageName' => [$fileval2,'s']],
                ['goodsNo' => [$goodsNo[0]['goodsNo'],'i'], 'imageKind' => ['list', 's']]
            ];
            if($fileval1){
                $conn->WibUpdate($file1Update);
            }
            
            $file2UpdateA = [
                'es_goodsImage',
                ['imageName' => [$fileval1,'s']],
                ['goodsNo' => [$goodsNo[0]['goodsNo'],'i'], 'imageKind' => ['add1', 's']]
            ];
            $file2UpdateB = [
                'es_goodsImage',
                ['imageName' => [$fileval1,'s']],
                ['goodsNo' => [$goodsNo[0]['goodsNo'],'i'], 'imageKind' => ['add2', 's']]
            ];
            if($fileval2){
                $conn->WibUpdate($file2UpdateA);
                $conn->WibUpdate($file2UpdateB);
            }
            
            $file3UpdateA = [
                'es_goodsImage',
                ['imageName' => [$fileval3,'s']],
                ['goodsNo' => [$goodsNo[0]['goodsNo'],'i'], 'imageKind' => ['magnify', 's']]
            ];
            $file3UpdateB = [
                'es_goodsImage',
                ['imageName' => [$fileval2,'s']],
                ['goodsNo' => [$goodsNo[0]['goodsNo'],'i'], 'imageKind' => ['main', 's']]
            ];
            $file3UpdateC = [
                'es_goodsImage',
                ['imageName' => [$fileval3,'s']],
                ['goodsNo' => [$goodsNo[0]['goodsNo'],'i'], 'imageKind' => ['detail', 's'], 'imageNo' => ['0', 'i']]
            ];
            
            if($fileval3){
                $conn->WibUpdate($file3UpdateA);
                $conn->WibUpdate($file3UpdateB);
                $conn->WibUpdate($file3UpdateC);
            }
            
            
            if($fileval4){
                $file4Update = [
                    'es_goodsImage',
                    ['imageName' => [$fileval4,'s']],
                    ['goodsNo' => [$goodsNo[0]['goodsNo'],'i'], 'imageKind' => ['detail', 's'], 'imageNo' => ['1', 'i']]
                ];
                $conn->WibUpdate($file4Update);
            }
            
            if($fileval5){
                $file5Update = [
                    'es_goodsImage',
                    ['imageName' => [$fileval5,'s']],
                    ['goodsNo' => [$goodsNo[0]['goodsNo'],'i'], 'imageKind' => ['detail', 's'], 'imageNo' => ['2', 'i']]
                ];
                $conn->WibUpdate($file5Update);
            }

            
        }

        exit;

    }


}
