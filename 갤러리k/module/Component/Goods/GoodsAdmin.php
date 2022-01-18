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

class GoodsAdmin extends \Bundle\Component\Goods\GoodsAdmin
{
    public function saveInfoGoods($arrData)
    {
        $applyFl = parent::saveInfoGoods($arrData);
        
        $detailAdd = $arrData['goodsDescriptionAdd'];
        
        $db = \App::load('DB');
        
        $arrBind = [];
        $strSQL = "UPDATE ".DB_GOODS." SET goodsDescriptionAdd = ? WHERE goodsNo = ?";
        $db->bind_param_push($arrBind, 's', $detailAdd);
        $db->bind_param_push($arrBind, 'i', $this->goodsNo);
        $db->bind_query($strSQL, $arrBind);
        
        return $applyFl;
    }
}