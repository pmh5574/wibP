<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link http://www.godo.co.kr
 */
namespace Controller\Admin\Goods;

use Component\Wib\WibSql;
/**
 * 상품 리스트 페이지
 */
class GoodsListController extends \Bundle\Controller\Admin\Goods\GoodsListController
{
    public function index()
    {
        parent::index();
        $wib = new WibSql();
        
        $goodsGridConfigList = $this->getData('goodsGridConfigList');
        $data = $this->getData('data');
        
        foreach($goodsGridConfigList as $key => $value){
            $goodsGridConfigList['goodsSpecific'] = '배송정보 미기재 상품';
        }
        
        foreach($data as $key => $value){
            $strSQL = "SELECT goodsSpecific FROM es_goods WHERE goodsNo = ".$value['goodsNo'];
            $result = $wib->WibNobind($strSQL);
            
            $data[$key]['goodsSpecific'] = $result['goodsSpecific'];
        }
        
        $this->setData('goodsGridConfigList',$goodsGridConfigList);
        $this->setData('data',$data);
    }
}