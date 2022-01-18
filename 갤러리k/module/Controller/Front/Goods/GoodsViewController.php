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
    public function index()
    {
        parent::index();
        
        $getview = $this->getData('goodsView');
        $goodsno = Request::get()->get('goodsNo');

        $db = \App::load('DB');

        $goods_sql = "select goodsDescriptionAdd from es_goods where goodsNo = {$goodsno} ";
        $result = $db->query_fetch($goods_sql);

        $getview['detailAdd'] = gd_htmlspecialchars_stripslashes($result[0]['goodsDescriptionAdd']);
        //$getview['detailAdd'] = str_replace('\"',"", ($result[0]['goodsDescriptionAdd']));
        //$getview['detailAdd'] = str_replace("\r","<br>", ($getview['detailAdd']));
        $this->setData('goodsView', $getview);

    }
}