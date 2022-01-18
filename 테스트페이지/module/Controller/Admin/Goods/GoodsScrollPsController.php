<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Admin\Goods;

use Request;
use Component\Wib\WibScroll;

class GoodsScrollPsController extends \Controller\Admin\Controller
{
    public function index()
    {
        $postValue = Request::post()->toArray();
        $wib = new WibScroll();
        
        switch ($postValue['mode']) {
            case 'save':
                
                $msg = $wib->saveScroll($postValue);
                $this->layer(__($msg));
                
                break;
            
            case 'delete':
                
                $msg = $wib->delScroll($postValue);
                $this->layer(__($msg));
                
                break;
            
            case 'modify':
                
                $msg = $wib->updateScroll($postValue);
                $this->layer(__($msg));
                
                break;
            
            default :
                break;
        }
    }
}
