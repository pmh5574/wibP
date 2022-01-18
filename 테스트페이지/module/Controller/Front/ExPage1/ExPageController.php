<?php
namespace Controller\Front\ExPage1;

use Component\ExPage\ExPage;
use Request;

class ExPageController extends \Controller\Front\Controller
{
    public function index()
    {   
        $expage = new ExPage();

        echo "<h1>index 부분</h1>";

        $countdb = $expage->countDBfunc();

        $this->setData("first_page",$first_page); // 맨앞으로
        $this->setData("print_pre",$print_pre); // 이전 페이지
        $this->setData("print_next",$print_next); // 다음 페이지
        $this->setData("end_page",$end_page);   // 맨 뒤
        
        $this->setData("print_page",$print_page);
        $this->setData("result",$result);
    }  
}