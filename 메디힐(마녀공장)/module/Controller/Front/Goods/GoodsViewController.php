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
namespace Controller\Front\Goods;

use Request;
use Globals;

class GoodsViewController extends \Bundle\Controller\Front\Goods\GoodsViewController
{
	public function index()
	{
        parent::index();
        $this->db = \App::load('DB');
        
        $relationGoods = $this->getData('widgetGoodsList');

        //goodsView에서 위젯사용시 goodsVolume 값 소수점 제거
        foreach($relationGoods as $key =>$value){
			foreach($value as $key2 => $val2){
				$strSQL = "SELECT goodsVolume FROM es_goods WHERE goodsNo = ".$val2['goodsNo'];
                $result = $this->db->query_fetch($strSQL, null);
                // 상품 용량 소수점 0 제거 (ex. 4.00 => 4, 4.40 => 4.4)
                if ($result[0]['goodsVolume'] - floor($result[0]['goodsVolume']) == 0) {
                    $result[0]['goodsVolume'] = number_format($result[0]['goodsVolume']);
                } else if ($result[0]['goodsVolume'] - (floor($result[0]['goodsVolume'] * 10) / 10) == 0) {
                    $result[0]['goodsVolume'] = number_format($result[0]['goodsVolume'], 1);
                }
				$relationGoods[$key][$key2]['goodsVolume'] = $result[0]['goodsVolume'];
			}
		}
        $this->setData('widgetGoodsList', gd_isset($relationGoods));
        
	}
}