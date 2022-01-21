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

        $selectdb = $expage->selectDBfunc($countdb);

        $printAtag = $expage->printAtagFunc();

        $this->setData("first_page",$expage->first_page); // 맨앞으로
        $this->setData("print_pre",$expage->print_pre); // 이전 페이지
        $this->setData("print_next",$expage->print_next); // 다음 페이지
        $this->setData("end_page",$expage->end_page);   // 맨 뒤
        
        $this->setData("printAtag",$printAtag);
        $this->setData("selectdb",$selectdb);
    }  
}