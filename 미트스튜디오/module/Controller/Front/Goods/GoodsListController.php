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
use Component\Wib\WibSql;

class GoodsListController extends \Bundle\Controller\Front\Goods\GoodsListController
{
    public function index()
    {
        parent::index();
        
        $wib = new WibSql();
        
        $cateCd = Request::get()->get('cateCd');
        
        $str = "SELECT cateNm,cateCd FROM es_categoryGoods WHERE cateCd LIKE '{$cateCd}%' AND LENGTH(cateCd) = 9 AND cateDisplayFl = 'y' AND cateDisplayMobileFl = 'y'";
        $result = $wib->WibAll($str);
        
        $strSQL = "SELECT cateNm, cateCd FROM es_categoryGoods WHERE cateCd LIKE '{$cateCd}%' AND LENGTH(cateCd) = 6 AND cateDisplayFl = 'y' AND cateDisplayMobileFl = 'y'";
        $res = $wib->WibAll($strSQL);
        
        
        
        foreach ($res as $key => $value){
            
            $ocateCd = $value['cateCd'];

            foreach ($result as $k => $val){
                
                
                $tcateCd = $val['cateCd'];

                $query = "SELECT COUNT(*) cnt,eg.delFl FROM es_goodsLinkCategory egl JOIN es_goods eg ON eg.goodsNo = egl.goodsNo WHERE egl.cateCd LIKE '{$tcateCd}' AND egl.cateLinkFl = 'y' AND eg.delFl != 'y'";
                $cnt = $wib->WibNobind($query);

                $val['goodsCnt'] = $cnt['cnt'];

                if(strpos($tcateCd,$ocateCd) !== false){

                    $res[$key]['cateCcd'][] = $val;
                    
                }
            }
        }
        
        $this->setData('res',$res);

    }
}