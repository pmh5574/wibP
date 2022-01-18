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
use Component\Wib\WibGoods;

class GoodsSearchController extends \Bundle\Controller\Front\Goods\GoodsSearchController
{
    public function index()
    {
        parent::index();

        $wib = new WibGoods();

        
        $gdlist = $this->getData('goodsList');
            
        $goodsList = $wib->WibFe($gdlist);

        $this->setData("goodsList", $goodsList);
            
        
    }
}
