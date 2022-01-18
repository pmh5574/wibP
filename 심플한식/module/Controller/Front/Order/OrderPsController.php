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
namespace Controller\Front\Order;

use Request;
use Session;
/**
 * 주문완료 처리
 *
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class OrderPsController extends \Bundle\Controller\Front\Order\OrderPsController
{
    public function index()
    {
        $postValue = Request::post()->toArray();
        
        if($postValue['nmCompanyNm']){
            Session::set('WIB_SESSION_JOIN_INFO', $postValue);
        }
        
        parent::index();
    }
}