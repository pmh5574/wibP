<?php
namespace Controller\Front\Test;

//use Globals;
//use Session;
//use Response;
//use Request;

/**
 * 테스트용
 */
class Test2Controller extends \Controller\Front\Controller
{
    /**
     * {@inheritdoc}
     */
    public function index()
    {
        $this->download(UserFilePath::frontSkin('moment', 'img/banner', 'bottom-logo.png'), 'test.png');
    }
}