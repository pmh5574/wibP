<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Admin\Policy;

use Component\Wib\WibSave;

class OrderCompanyListController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('policy', 'order', 'companyName');
        

        $wibCompany = new WibSave();
        
        $list = $wibCompany->getCompanyName('admin');

        $this->setData('data', $list);
    }
}
