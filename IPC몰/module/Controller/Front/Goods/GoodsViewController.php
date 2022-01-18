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


class GoodsViewController extends \Bundle\Controller\Front\Goods\GoodsViewController
{
    public function index()
    {
        parent::index();
        
        $data = $this->getData('goodsView');
        
        $optionType = explode("^|^", $data['wibType']);
        $colorOptionsEx = explode("^|^", $data['colorOptions']);
        
        $colorOptions = [];
        foreach ($colorOptionsEx as $value) {
            $exVal = explode("||", $value);
            $colorOptions[htmlspecialchars($exVal[0])] = $exVal[1];
        }
        
        $data['optionDisplayFl'] = 'd';
        
        //$this->setData('goodsView', $data);
        $this->setData('wibType', $optionType);
        $this->setData('colorOptions', $colorOptions);
        //print_r($colorOptions);
        
    }
}