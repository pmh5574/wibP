<?php

namespace Controller\Front\LinkPrice;

use App;

/**
* 링크프라이스 주문목록 페이지
*
* @author webnmobile
*/ 
class OrderListController extends \Controller\Front\Controller 
{
	public function index()
	{
		
		$linkPrice = App::load(\Component\LinkPrice\LinkPrice::class);			
		$list = $linkPrice->getOrderList();
		header("Content-type: application/json; charset=utf-8");
		echo json_encode($list);
		exit;
	}
}