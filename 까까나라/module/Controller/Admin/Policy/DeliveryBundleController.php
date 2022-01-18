<?php
namespace Controller\Admin\Policy;

use Session;
use Request;
use Component\Wib\WibSql;

class DeliveryBundleController extends \Controller\Admin\Controller 
{
	/**
     * @inheritdoc
     *
     * @throws Exception
     */
    public function index()
    {
        $wib = new WibSql();
        // --- 메뉴 설정
        $this->callMenu('policy', 'delivery', 'deliveryBundle');

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
        

        $this->setData('result',$result);
    }

    
}