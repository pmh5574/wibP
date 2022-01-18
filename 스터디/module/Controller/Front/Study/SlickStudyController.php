<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Front\Study;

use Request;
use Component\Wib\WibSql;

class SlickStudyController extends \Controller\Front\Controller
{
    public function index() 
    {
        $wibSql = new WibSql();
        
        $postValue = Request::post()->toArray();
        
        if($postValue['mode'] == 'ajax'){
            $field = 'sno, bannerName, bannerImg, bannerPath, bannerPrice';
        
            $strSQL = "SELECT {$field} FROM es_studyBanner";
            $query = $wibSql->WibAll($strSQL);

            print_r(json_encode($query));
        }else if(!$postValue){
            print_r('get X');
        }else{
            print_r('No Data');
        }
        
        
        exit;
    }
}
