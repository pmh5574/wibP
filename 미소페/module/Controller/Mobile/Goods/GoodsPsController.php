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
/**
 * 상품 상세 페이지 처리
 *
 * @author artherot
 * @version 1.0
 * @since 1.0
 * @copyright Copyright (c), Godosoft
 */

class GoodsPsController extends \Bundle\Controller\Mobile\Goods\GoodsPsController
{
    public function index()
    {
        parent::index();
        
        $wib = new WibGoods();
            
        $postValue = Request::request()->toArray();

        if($postValue['mode'] == 'get_main'){

            $gdList = $this->getData('goodsList');

            $goodsList = $wib->WibFe($gdList);

            $this->setData("goodsList", $goodsList);
        }
        
    }
}