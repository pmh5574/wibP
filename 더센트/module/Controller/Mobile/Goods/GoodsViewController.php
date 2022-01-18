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
namespace Controller\Mobile\Goods;

use Component\Wib\WibSql;

class GoodsViewController extends \Bundle\Controller\Mobile\Goods\GoodsViewController
{
    public function index()
    {
        parent::index();
        
        $wib = new WibSql();
        
        $goodsView = $this->getData('goodsView');
        
        foreach ($goodsView as $value){
            
            $query = "SELECT * FROM es_goods WHERE goodsNo = {$goodsView['goodsNo']}";
            $result = $wib->WibNobind($query);
            
            $goodsView['goodsReplaceLink'] = $result['goodsReplaceLink'];
            
        }
        
        $this->setData('goodsView',$goodsView);
    }
}