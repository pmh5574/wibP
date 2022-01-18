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
namespace Controller\Mobile\Goods;

use Request;
use Component\Wib\WibSql;

class GoodsViewController extends \Bundle\Controller\Mobile\Goods\GoodsViewController
{
    public function index()
    {
        parent::index();
        
        $wib = new WibSql();
        
        $goodsNo = Request::get()->get('goodsNo');

        $numCount = "";
        $numCount .= "(SELECT count(goodsPt) FROM es_bd_goodsreview WHERE goodsPt = 5 AND goodsNo = {$goodsNo}) goodsPtFive, ";
        $numCount .= "(SELECT count(goodsPt) FROM es_bd_goodsreview WHERE goodsPt = 4 AND goodsNo = {$goodsNo}) goodsPtFour, ";
        $numCount .= "(SELECT count(goodsPt) FROM es_bd_goodsreview WHERE goodsPt = 3 AND goodsNo = {$goodsNo}) goodsPtThree, ";
        $numCount .= "(SELECT count(goodsPt) FROM es_bd_goodsreview WHERE goodsPt = 2 AND goodsNo = {$goodsNo}) goodsPtTwo, ";
        $numCount .= "(SELECT count(goodsPt) FROM es_bd_goodsreview WHERE goodsPt = 1 AND goodsNo = {$goodsNo}) goodsPtOne ";

        $strSQL = "SELECT avg(goodsPt) goodsPtAvg, {$numCount} FROM `es_bd_goodsreview` WHERE goodsNo = {$goodsNo}";
        $getData = $wib->WibNobind($strSQL);

        $this->setData('goodsPtFive',$getData['goodsPtFive']);
        $this->setData('goodsPtFour',$getData['goodsPtFour']);
        $this->setData('goodsPtThree',$getData['goodsPtThree']);
        $this->setData('goodsPtTwo',$getData['goodsPtTwo']);
        $this->setData('goodsPtOne',$getData['goodsPtOne']);
        $this->setData('goodsPtAvg',$getData['goodsPtAvg']);
            
        
    }
}