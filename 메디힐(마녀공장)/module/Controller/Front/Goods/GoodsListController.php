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


class GoodsListController extends \Bundle\Controller\Front\Goods\GoodsListController
{
	public function index()
	{
        //특정 값 뿌려주기(goodsVolume(용량))
		parent::index();
		$this->db = \App::load('DB');
		$gdlist = $this->getData('goodsList');
        
        foreach($gdlist as $key => $value){
            foreach($value as $key2 => $val){
                $strSQL = "SELECT goodsVolume FROM es_goods WHERE goodsNo = ".$val['goodsNo'];
                $result = $this->db->query_fetch($strSQL, null);
                $result[0]['goodsVolume'] = floor($result[0]['goodsVolume']);
                $gdlist[$key][$key2]['goodsVolume'] = $result[0]['goodsVolume'];
            }
        }
        $this->setData("goodsList", $gdlist);
		
	}

}