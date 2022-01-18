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
use Component\Wib\WibGoods;

class GoodsSearchController extends \Bundle\Controller\Mobile\Goods\GoodsSearchController
{
    public function index()
    {
        parent::index();

        $wib = new WibGoods();

        $wibKeyword = Request::server()->get('QUERY_STRING');
        $keyword = Request::get()->toArray();
        $gdlist = $this->getData('goodsList');
            
        $goodsList = $wib->WibFe($gdlist);

        /*필터값 모바일 더보기 떄문에 추가*/
        $filterColor = implode('^|^',$keyword['filterColor']);
        $fpStart = $keyword['fpStart'];
        $fpEnd = $keyword['fpEnd'];
        $filterPattern = implode('^|^',$keyword['filterPattern']);
        $filterSeason = implode('^|^',$keyword['filterSeason']);
        $cateGoods = implode('^|^',$keyword['cateGoods']);

        $this->setData('filterColor',$filterColor);
        $this->setData('fpStart',$fpStart);
        $this->setData('fpEnd',$fpEnd);
        $this->setData('filterPattern',$filterPattern);
        $this->setData('filterSeason',$filterSeason);
        $this->setData('cateGoods',$cateGoods);
        //필터값 모바일 더보기 떄문에 추가

        $this->setData('wibKeyword',$wibKeyword);
        $this->setData("goodsList", $goodsList);
            
        
    }
}