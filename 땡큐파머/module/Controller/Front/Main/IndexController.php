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
namespace Controller\Front\Main;
use Request;
/**
 * 메인 페이지
 *
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class IndexController extends \Bundle\Controller\Front\Main\IndexController
{
    public function index()
    {
        parent::index();
        $server = Request::server()->toArray();
        if($server['REMOTE_ADDR']=='112.145.36.156'){
            $this->setData('wm','wm');
        }
    }
}