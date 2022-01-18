<?php
namespace Controller\Admin\Goods;

use Session;
use Request;
use Component\Wib\WibSql;

class LayerGoodsDeliveryBundleController extends \Controller\Admin\Controller 
{
	/**
     * @inheritdoc
     *
     * @throws Exception
     */
    public function index()
    {
        $wib = new WibSql();
        $db = \App::load('DB');

        $strSQL = "SELECT * FROM es_scmDeliveryBundle";
        $result = $wib->WibAll($strSQL);
        
        foreach($result as $key => $value){
            
            if($value['deliveryType'] == 'HIGH'){

                $result[$key]['deliveryType'] = '최대부과';

            }else if($value['deliveryType'] == 'ROW'){

                $result[$key]['deliveryType'] = '최소부과';

            }

            if($value['allGoodsNo']){

                $result[$key]['allGoodsNo'] = '등록된 상품이 있습니다.';

            }else{

                $result[$key]['allGoodsNo'] = '등록된 상품이 없습니다.';

            }
        }

         //--- 관리자 디자인 템플릿
         $this->getView()->setDefine('layout', 'layout_layer.php');

         // 공급사 페이지 설정
         $this->getView()->setPageName('goods/layer_goods_delivery_bundle.php');
        
        
        $this->setData('result',$result);
    }

    
}