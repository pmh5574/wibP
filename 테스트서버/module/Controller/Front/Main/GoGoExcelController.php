<?php
namespace Controller\Front\Main;

use Request;

class GoGoExcelController extends \Controller\Front\Controller
{
    public function index()
    {
        $_excel = Request::files()->toArray();
        print_r($_excel);
        exit;
        //move_uploaded_file();
    }
}