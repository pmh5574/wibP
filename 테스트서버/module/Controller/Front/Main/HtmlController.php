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
namespace Controller\Front\Main;

/**
 * Controller 없는 페이지의 Controller
 *
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class HtmlController extends \Bundle\Controller\Front\Main\HtmlController
{
    public function index()
    {
        parent::index();
        
        //print_r('test');
    }
}