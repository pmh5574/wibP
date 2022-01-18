<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
nameSpace Controller\Admin\Goods;

use Request;
use Component\Wib\WibFilter;


class GoodsFilterPsController extends \Controller\Admin\Controller
{
    public function index()
    {
        $wibFilter = new WibFilter();
        
        $postValue = Request::post()->toArray();

        switch ($postValue['mode']) {
            case 'save':
                foreach($postValue['goodsNo'] as $goodsNo){
                
                    $wibFilter->setGoodsFilter($goodsNo,$postValue);
                    
                }
                
                $this->layer(__('검색 필터 설정값이 저장 되었습니다.'));
                break;
            case 'delete':
                foreach($postValue['goodsNo'] as $goodsNo){
                
                    $wibFilter->deleteGoodsFilter($goodsNo);
                    
                }
                
                $this->layer(__('검색 필터 설정값이 삭제 되었습니다.'));
                break;
                
            default :
                break;
        }
        exit;
    }
}
