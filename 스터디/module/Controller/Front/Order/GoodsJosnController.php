<?php
namespace Controller\Front\Order;

use Request;
use Component\Wib\WibSql;

class GoodsJosnController extends \Controller\Front\Controller{
    public function index()
    {
        $this->db = new WibSql();
        
        $page = Request::get()->get('page');
        $curlpage = ($page)?$page:1;
        
        $url = "https://www.testgut.com/mall/moveGodo/goodsExcel.php?page={$curlpage}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        //echo count($result);
        $data = json_decode($result);
        
        $conn = new WibSql();
        
        foreach ($data as $value) {

            // 자체상품코드
            $goodc_cd = $value->eb_code;
            
            // 판매가 
            $cellprice = $value->goods_sale_price1;
            
            // 바코드
            $barcode = $value->barcode;
            
            // 상세설명
            $contents = stripslashes($value->goods_main);
            
            // 고시정보
            $gosiList = json_decode($value->gosi_list);
            $gosi = array();
            foreach ($gosiList as $key => $val) {
                $gosi[$key]['step0'] = array(
                    "infoTitle" => $val->step0->infoTitle,
                    "infoValue" => $val->step0->infoValue
                );   
            }
            $gosi_data = json_encode($gosi, JSON_UNESCAPED_UNICODE);
            
            // 배송선택입력
            $detailInfoDeliveryFl = 'selection';
            
            // 배송안내 내용
            $detailInfoDelivery = '002001';
            
            // 무게변형
            $kgram = floatval($value->goods_kg) * 1000;
            
            // 품절 체크
            $soldOut = ($value->goods_stock > 0)?'n':'y';
            
            $conn->WibUpdate([
                'es_goods',
                [
                    'goodsPrice' => [ $cellprice, 'i'],
                    'tegut_barcode' => [ $barcode, 's'],
                    'goodsDescription' => [ $contents, 's'],
                    'goodsMustInfo' => [ $gosi_data, 's'],
                    'detailInfoDeliveryFl' => [ $detailInfoDeliveryFl, 's'],
                    'detailInfoDelivery' => [ $detailInfoDelivery, 's'],
                    'goodsWeight' => [ $kgram, 'i'],
                    'soldOutFl' => [ $soldOut, 's'],
                    'stockFl' => [ 'y', 's'],
                    'totalStock' => [ $value->goods_stock, 'i']
                ],
                ['goodsCd' => [$goodc_cd, 'i']]
            ]);
            
            //echo $value->eb_code.'<br>';
        }
        
        $count = count($data);
        $setcount = ($count >= 500)?$count:0;
        //echo $data;
        //print_r($data);
        $this->setData('page', $curlpage);
        $this->setData('count', $setcount);
        $this->setData('realCount', $count);

    }


}
