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
namespace Controller\Front\Board;

use Request;

class WriteController extends \Bundle\Controller\Front\Board\WriteController
{
    public function index()
    {
        parent::index();
        $this->db = \App::load('DB');

        $goodsNo = Request::get()->get('goodsNo');
        //print_r($goodsNo);
        $strSQL = "SELECT eg.*, egi.imageName, egi.imageKind FROM es_goods AS eg JOIN es_goodsImage AS egi ON egi.goodsNo = eg.goodsNo WHERE egi.imageKind = 'list' AND eg.goodsNo = ".$goodsNo;
        $result = $this->db->query_fetch($strSQL,null);
        //print_r($result);
        $this->setData("result", $result);


        
    }
}