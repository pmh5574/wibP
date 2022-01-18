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

class GoodsViewPsController extends \Controller\Front\Controller
{
    public function index()
    {
        if (!Request::isAjax()) {
            $this->js("alert('ajax 전용 페이지 입니다.');");
            exit;
        }
        $db = \App::load('DB');
        $goodsNm = Request::post()->get('textVal');
        $sql = "SELECT goodsNm FROM es_goods WHERE goodsNm LIKE '%{$goodsNm}%'";
        $data = $db->query_fetch($sql);
        
        echo json_encode($data);
        exit;
    }
}