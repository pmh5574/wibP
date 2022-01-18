<?php
namespace Controller\Admin\Wbtest;

use Session;

class IndexController extends \Controller\Admin\Controller 
{
	public function index() 
	{

        $this->callMenu('wbtest', 'wbtest', 'wbtest');

        $this->addCss([
            '../css/wcolpick.css',
            ]);

        $this->addScript([
            '../script/wcolpick.js',
            ]);

	}
}