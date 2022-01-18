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
        
        $goodsNo = $arrData['goodsNo'];

        
        

        $goodsDeliveryBundle = $arrData['goodsDeliveryBundle'];//라디오값
        $deliveryBundleSno = $arrData['deliveryBundleSno'];

        //까까나라 추가작업 연습
        $query = "UPDATE es_goods SET goodsDeliveryBundle = ?, goodsDeliveryKey = ? WHERE goodsNo = ?";
        $this->db->bind_param_push($arrBind, 's', $goodsDeliveryBundle);
        $this->db->bind_param_push($arrBind, 'i', $deliveryBundleSno);
        $this->db->bind_param_push($arrBind, 'i', $goodsNo);
        $this->db->bind_query($query, $arrBind);
        unset($arrBind);

        return $applyFl;
    }
}