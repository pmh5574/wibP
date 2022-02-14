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

use Request;

class GoodsViewController extends \Bundle\Controller\Mobile\Goods\GoodsViewController
{
    public function index()
    {
    parent::index();
        
        if (!is_object($this->db)) {
            $this->db = \App::load('DB');
        }
        
        $goods = \App::load('\\Component\\Goods\\Goods');
        $goodsView = $goods->getGoodsView(Request::get()->get('goodsNo'));
        
        $goodsNo = Request::get()->get('goodsNo');
        $getColor = $goodsView['optionDivision'];
        
        foreach ($getColor as $value){
            $query = "SELECT goodsImage FROM `es_goodsOptionIcon` where goodsNo ={$goodsNo} AND optionValue='{$value}'";
            $goodsView['colorImg'][$value] = $this->db->query_fetch($query,null, false);
        }
        
        foreach ($goodsView['option'] as $k => $goodsOptionInfo) {
            $optionArr[$k] = $goodsOptionInfo['optionValue2'];
            
        }
        
        // Size 옵션 중복 제거, 정렬
        $goodsView['optionSizeDivision'] = array_unique($optionArr); 
        $set_size = array_reverse($goodsView['optionSizeDivision']);

    }
}