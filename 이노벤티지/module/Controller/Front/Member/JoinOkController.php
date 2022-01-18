<?php

use Session;
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

use Component\Wib\WibSql;
/**
 * Class 회원가입완료
 * @package Bundle\Controller\Front\Member
 * @author  yjwee
 */
class JoinOkController extends \Bundle\Controller\Front\Member\JoinOkController
{
    public function index()
    {
        parent::index();
        
        $session = \App::getInstance('session');
        
        //pc,mobile 공통
        $eventAgreeFl = $session->get('WIB_SESSION_JOIN_INFO');
        $session->del('WIB_SESSION_JOIN_INFO');
        
        if($eventAgreeFl == 'y'){
            
            $memNo = $this->getData('memNo');
            
            $wib = new WibSql();
            
            $wibQuery = [
                'es_member',
                array('eventAgreeFl'=>[$eventAgreeFl,'s']),
                array('memNo'=>[$memNo,'i'])
            ];

            $wib->WibUpdate($wibQuery);
            
        }
            
    }
}