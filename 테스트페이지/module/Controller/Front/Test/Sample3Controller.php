<?php
namespace Controller\Front\Test;

use Request;
use Session;
use Cookie;

class SampleController extends \Controller\Front\Controller
{
    public function index()
    {
        echo 'Hello World !!!<br>';
        echo '<pre>';
        print_r(Request::get()->all());
        print_r(Request::post()->all());
        print_r(Session::all());
        print_r(Cookie::all());
        echo '</pre>';
        exit();
    }
}