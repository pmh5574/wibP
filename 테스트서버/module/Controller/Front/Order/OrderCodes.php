<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Controller\Front\Order;

use Request;

/**
 * 코드쿠폰 사용여부 처리
 *
 * @author WIB_PUB
 */
class OrderCodesController  {
    
    public function index()
    {
        if (Request::isAjax()) {
            
            $setData['test'] = 'test';
            
            $this->json($setData);
            exit;
        }else{
            throw new AlertCloseException('잘못된 접근입니다.');
        }
    }
    
}
