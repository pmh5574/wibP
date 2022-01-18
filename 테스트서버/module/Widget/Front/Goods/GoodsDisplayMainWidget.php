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
namespace Widget\Front\Goods;


use Request;

class GoodsDisplayMainWidget extends \Bundle\Widget\Front\Goods\GoodsDisplayMainWidget
{
	 public function index()
	 {
         parent::index();
        
//	 	$this->db = \App::load('DB');
          $goods = \App::load('\\Component\\Goods\\Goods');
//          $goodsData = $goods->getGoodsSearchList($displayCnt, $mainOrder, $imageType, $optionFl, $soldOutFl, $brandFl, $couponPriceFl ,$displayCnt,false,$getData['moreBottomFl'] == 'y' ? true : false );
//          print_r($goodsData);
//          $getdata2 = $goods->getDisplayThemeInfo();
//          print_r($getdata2);
//          $goodsData2 = $this->getData('goodsData');
//          print_r($goodsData2);
//         $goods = \App::load('\\Component\\Goods\\Goods');
//         $getData = $goods->getDisplayThemeInfo($this->getData('sno'));
//         $q = "SELECT goodsVolume FROM es_goods WHERE goodsNo = ".$getData['goodsNo'];        
//         $aab = $this->db->query_fetch($q, null);
//         print_r($aab);
//         $this->setData('aab', $aab);
         $data = $this->getData('goodsList');
         
         
        // echo 's<br>';print_r($data);echo 'e<br>';
	 }
}