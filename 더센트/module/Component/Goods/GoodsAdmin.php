<?php

/**
 * 상품 class
 *
 * 상품 관련 관리자 Class
 * @author artherot
 * @version 1.0
 * @since 1.0
 * @copyright Copyright (c), Godosoft
 */
namespace Component\Goods;

use Component\Wib\WibSql;

class GoodsAdmin extends \Bundle\Component\Goods\GoodsAdmin
{
    public function saveInfoGoods($arrData) 
    {
        $applyFl = parent::saveInfoGoods($arrData);
        
        $wib = new WibSql();
        
        $goodsNo = $this->goodsNo;
        $goodsReplaceLink = $arrData['goodsReplaceLink'];
   
        $data = [
            'es_goods',
            array('goodsReplaceLink'=>[$goodsReplaceLink,'s']),
            array('goodsNo'=>[$goodsNo,'s'])
        ];
        
        $wib->WibUpdate($data);
        
        return $applyFl;
    }
}