<?php
namespace Controller\Front\Test;

use Globals;
use Session;
use Response;
use Request;

class TestController extends \Controller\Front\Controller
{
    /**
     * {@inheritdoc}
     */
    public function index()
    {
        $setData = 'Hello World !!!';
        print_r($setData);
        exit;
        $this->setData('setData', $setData);
    }
}