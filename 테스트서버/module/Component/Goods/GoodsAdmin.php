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

use Component\Database\DBTableField;

class GoodsAdmin extends \Bundle\Component\Goods\GoodsAdmin
{
    public function saveInfoGoods($arrData) 
    {

        // 옵션구분 (일반, 색상, 사이즈) 저장
        $optionGubun = is_array($arrData['optionY']['optionGubun']);

        if($optionGubun) {
            $arrData['wbOptionGubun'] = implode(STR_DIVISION, $arrData['optionY']['optionGubun']);
            $arrBind = $this->db->get_binding(DBTableField::getBindField('tableGoods', 'wbOptionGubun'), $arrData, 'update', 'wbOptionGubun', null);
            $this->db->bind_param_push($arrBind['bind'], 'i', $arrData['goodsNo']);
            $this->db->set_update_db(DB_GOODS, $arrBind['param'], 'goodsNo = ?', $arrBind['bind']);
            unset($arrBind);
        }


        //202-01-01 썸네일 외부영상 튜닝시작
        //이부분은 원래 saveInfoGoods 실행하는 부분
        $applyFl = parent::saveInfoGoods($arrData);
		$aa = $this->goodsNo;//관리자 메모2추가 연습
		//위랑 아래랑 같음
		//$aa =  $arrData['goodsNo'];//관리자 메모2추가 연습
	
		$strSQL = "UPDATE es_goods SET memo2 = ? WHERE goodsNo = ?";
		$this->db->bind_param_push($arrBind, 's', $arrData['memo2']);
		$this->db->bind_param_push($arrBind, 'i', $aa);
		$this->db->bind_query($strSQL, $arrBind);

        //추가코드 (먼저 추가코드 실행하고 후에 원래코드 실행하거나 반대로 해도 됨.)
        $strUpdateSQL = "UPDATE " . DB_GOODS . " SET externalVimeoUrl = '" . $arrData['externalVimeoUrl'] . "' , externalVimeoFl = '" . $arrData['externalVimeoFl'] . "' WHERE goodsNo = '";
        $strUpdateSQL .= $this->goodsNo . "' ";
        $this->db->query($strUpdateSQL);
        //error_log(print_r($strUpdateSQL,true),3,'/www/baiwan09151_godomall_com/tmp/res.txt');
        if($arrData[''])
        //202-01-01 썸네일 외부영상 튜닝끝
		
		
		
		


        $applyFl = parent::saveInfoGoods($arrData);

        return $applyFl;
    }

    public function getDataGoodsOption($goodsNo = null) 
    {
        $getData = parent::getDataGoodsOption($goodsNo);

        $getData['data']['wbOptionGubun'] = explode(STR_DIVISION, $getData['data']['wbOptionGubun']);

        return $getData;
    }
}