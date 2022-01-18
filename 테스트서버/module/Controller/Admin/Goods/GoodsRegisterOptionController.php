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
 * @link      http://www.godo.co.kr
 */
namespace Controller\Admin\Goods;

/**
 * 상품 등록 / 수정 페이지
 */
class GoodsRegisterOptionController extends \Bundle\Controller\Admin\Goods\GoodsRegisterOptionController
{
    public function index() 
    {
        parent::index();
        $this->addCss(['../../css/wcolpick.css']);
        $this->addScript(['../../script/wcolpick.js']);
    }
}