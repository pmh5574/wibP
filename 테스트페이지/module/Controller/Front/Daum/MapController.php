<?php
namespace Controller\Front\Daum;

use Cookie;
use Exception;

class MapController extends \Controller\Front\Controller

{
    public function index(){

        Cookie::set('name', 'value');   // 세션 쿠키 생성
        gd_debug($gd);
    }
}