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
use App;

class GoodsListController extends \Bundle\Controller\Front\Goods\GoodsListController
{
    public function index()
    {
        parent::index();
		$cateCd = Request::get()->get("cateCd");
			if($cateCd=="001001"){$this->setData('cate_on', 'on');}
         //echo "<pre>";
         //print_r($cateCd);
		 //exit;
	
	
    }


}