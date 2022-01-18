<?php
namespace Controller\Admin\Goods;

use Request;
use Component\Wib\WibSql;

class GoodsDiscountPerController extends \Controller\Admin\Controller
{
    public function index()
    {
        $wib = new WibSql();

        $getValue = Request::post()->toArray();

        $goodsNo  = $getValue['goodsNo'];

        //상품할인율 on/off
        if($getValue['saleon']){

            $goodsDiscountPer = ($getValue['saleon'] == 'on') ? 'y' : 'n';

            $data = [
                'es_goods',
                array('goodsDiscountPer' => [$goodsDiscountPer, 's']),
                array('goodsNo'          => [$goodsNo, 'i'])
            ];

            $wib->WibUpdate($data);
        }
        
        //네이버 on/off
        if($getValue['naveron']){

            $naverFl = ($getValue['naveron'] == 'on') ? 'y' : 'n';

            $data = [
                'es_goods',
                array('naverFl' => [$naverFl, 's']),
                array('goodsNo' => [$goodsNo, 'i'])
            ];

            $wib->WibUpdate($data);
        }
        
        if($getValue['wanton']){
            
            $sno = $getValue['sno'];
            
            $wantChecks = ($getValue['wanton'] == 'on') ? 'y' : 'n';
            
            $data = [
                'es_displayTheme',
                array('wantChecks' => [$wantChecks, 's']),
                array('sno' => [$sno, 'i'])
            ];
            
            $wib->WibUpdate($data);
        }

        exit;
    }
}
