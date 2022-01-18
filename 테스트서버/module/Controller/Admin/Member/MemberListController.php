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

/**
 * Class 회원리스트
 * @package Bundle\Controller\Admin\Member
 * @author  yjwee
 */
class MemberListController extends \Bundle\Controller\Admin\Member\MemberListController
{
    protected $db = null;
    
    public function __construct() {
        
        parent::__construct();
        
        if (!is_object($this->db)) {
            $this->db = \App::load('DB');
        }
    }
    
    public function index()
    {
        parent::index();
        
        $addData = $this->getData('data');
        
        foreach ($addData as $key => $value) {
            $recommId_SQL = "SELECT recommId FROM es_member  WHERE memNo = '" . $value['memNo'] . "'";
            $result = $this->db->query($recommId_SQL);
            $data = $this->db->fetch($result);
            $addData[$key]['recommId'] = $data['recommId'];
        }
        
        $this->setData('data', $addData);
    }
}