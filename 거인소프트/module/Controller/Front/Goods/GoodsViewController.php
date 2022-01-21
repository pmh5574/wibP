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


class GoodsViewController extends \Bundle\Controller\Front\Goods\GoodsViewController
{   
    protected $db = null;

    public function index()
    {
        parent::index();

        $asd = Request::get()->all();
        $goods = \App::load('\\Component\\Goods\\Goods');
        $goodsNo = Request::get()->get('goodsNo');
        $goodsView = $goods->getGoodsView(Request::get()->get('goodsNo'));
        $asd = $goodsView['optionDivision'];


        foreach ($goodsView['option'] as $k => $goodsOptionInfo) {
            $optionArr[$k] = $goodsOptionInfo['optionValue2'];
            
        }
        $goodsView['optionSizeDivision'] = array_unique($optionArr);
        $goodsView['optionSizeDivision'] = array_reverse($goodsView['optionSizeDivision']);

        echo "<!--";
        print_r($goodsView);
        echo "-->";
        

        $this->setData('goodsView', $goodsView);

    }
}