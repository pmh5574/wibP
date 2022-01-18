<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Admin\Policy;

use Component\Wib\WibSave;
use Request;

class CompanyPsController extends \Controller\Admin\Controller
{
    public function index()
    {   
        $wibCompany = new WibSave();
        
        $postValue = Request::post()->toArray();
        
        foreach($postValue['companyName'] as $key => $value){
            if(empty($value)){
                unset($postValue['companyName'][$key]);
            }
        }
        
        $list = '';
        $list = serialize($postValue['companyName']);
        
        $wibCompany->companyNameUpdate($list);

        echo '<script>alert("저장되었습니다.");location.href="../policy/order_company_list.php"</script>';
        exit;

    }
    
    
}
