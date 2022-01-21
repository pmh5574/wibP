<?php
namespace Controller\Admin\Member;

class MemberInfoController extends \Bundle\Controller\Admin\Member\MemberListController
{
    public function index()
    {
        // 부모 클래스 상속
        parent::index();

        $this->callMenu('member', 'member', 'memberinfo');



    }
}