<?php

namespace Controller\Front\Linkprice;

/**
* 링크프라이스 게이트웨이 페이지 
* lpinfo 쿠키 생성페이지 
*
* @author webnmobile
*/ 
class GatewayController extends \Controller\Front\Controller
{
	public function index()
	{
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<!-- Google Tag Manager -->
			<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
			new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
			j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
			'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
			})(window,document,'script','dataLayer','GTM-T3JQD6G');</script>
			<!-- End Google Tag Manager -->
		</head>
		<body></body>
		</html>
		<?php 
		exit;
	}
}