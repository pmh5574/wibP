<?php
namespace Controller\Admin\Goods;

use Request;
use Component\Wib\WibSql;

class GoodsSpecificController extends \Controller\Admin\Controller
{
    public function index()
    {
        $wib = new WibSql();
        $wibAjax = Request::post()->toArray();

        if($wibAjax['mode']=='one'){
            
            $goodsSpecific = ($wibAjax['goodsSpecific'] == 'y') ? 'n':'y';

            $data = [
                'es_goods',
                array('goodsSpecific'=>[$goodsSpecific,'s']),
                array('goodsNo'=>[$wibAjax['goodsNo'],'s'])
            ];
            
            $wib->WibUpdate($data); 
            exit;
            
            
        }else if($wibAjax['mode']=='two'){
            
            foreach($wibAjax['goodsNo'] as $key => $val){
                
                $data = [
                    'es_goods',
                    array('goodsSpecific'=>['y','s']),
                    array('goodsNo'=>[$val,'s'])
                ];
                
                $wib->WibUpdate($data);
                
                
            }

            echo "<script>alert('배송정보 미기재 상품이 설정 됐습니다.');parent.location.reload();</script>";exit;
            
        }else if($wibAjax['mode']=='three'){
            
            foreach($wibAjax['goodsNo'] as $key => $val){
                
                $data = [
                    'es_goods',
                    array('goodsSpecific'=>['n','s']),
                    array('goodsNo'=>[$val,'s'])
                ];
                
                $wib->WibUpdate($data);
                
            }
            echo "<script>alert('배송정보 미기재 상품이 해제 됐습니다.');parent.location.reload();</script>";exit;
        }
        
    }
}
