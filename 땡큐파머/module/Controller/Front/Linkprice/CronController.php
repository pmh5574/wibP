<?php

namespace Controller\Front\LinkPrice;

use App;

/**
* 링크프라이스 크론 처리 
*
* @author webnmobile
*/ 
class CronController extends \Controller\Front\Controller
{
	public function index()
	{
		$linkPrice = App::load(\Component\LinkPrice\LinkPrice::class);
		$linkPrice->updateAll();
		exit;
	}
}