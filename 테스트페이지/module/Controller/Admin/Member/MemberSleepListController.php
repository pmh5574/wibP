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
namespace Controller\Admin\Member;

use Framework\Utility\DateTimeUtils;

/**
 * Class 관리자-회원-휴면회원 관리리스트 조회 컨트롤러
 * @package Bundle\Controller\Admin\Member
 * @author  yjwee
 */
class MemberSleepListController extends \Bundle\Controller\Admin\Member\MemberSleepListController
{
    public function index()
    {
        parent::index();
//        $request = \App::getInstance('request');
//        
//        $request->get()->set('sleepDate', DateTimeUtils::getBetweenDateString('-365days'));
//        print_r($request->get()->all());
    }
}