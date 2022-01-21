<?php

namespace Controller\Front;

use App;
use Request;

class CommonController
{
	public function index($controller)
	{
		/* 웹앤모바일 튜닝 - 2019-11-05, 링크프라이스 연동 */
		$server = Request::server()->toArray();
		if ($server['PHP_SELF'] == '/' || $server['PHP_SELF'] == '/main/index.php' || $server['PHP_SELF'] == '/order/order_end.php') {
			$linkPrice = App::load(\Component\LinkPrice\LinkPrice::class);
			$linkPrice->updateAll();
		}
		/* 튜닝 END */
		
	}
}