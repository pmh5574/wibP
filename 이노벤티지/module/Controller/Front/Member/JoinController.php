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
namespace Controller\Front\Member;

use Session;
use Component\Member\Member;
/**
 * Class 회원가입 정보입력
 * @package Bundle\Controller\Front\Member
 * @author  yjwee
 */
class JoinController extends \Bundle\Controller\Front\Member\JoinController
{
    public function index()
    {
        $request = \App::getInstance('request');
        
        $eventAgreeFl = $request->post()->get('eventAgreeFl');
        
        $result = gd_isset($eventAgreeFl, 'n');

        //세션에 저장함
        Session::set('WIB_SESSION_JOIN_INFO', $result);
        
        parent::index();
        
    }
}