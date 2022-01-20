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
namespace Controller\Admin\Share;

use Session;

class PopupAddGoodsController extends \Bundle\Controller\Admin\Share\PopupAddGoodsController
{
    public function post()
    {
//        gd_debug(Session::get('manager.isProvider'));
        print_r(Session::get('manager.isProvider'));
        print_R('qweqweqwewqe');
    }
}