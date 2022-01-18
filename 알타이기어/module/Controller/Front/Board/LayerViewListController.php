<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Front\Board;

use Request;
use Component\Wib\WibSql;

class LayerViewListController extends \Controller\Front\Controller
{
    public function index()
    {
        $wibSql = new WibSql();
        
        $sno = Request::post()->get('sno');
        
        if($sno){
            
            $query = "SELECT * FROM es_bd_store WHERE sno = {$sno}";
            $data = $wibSql->WibAll($query);

            foreach ($data as $key => $value){

                $saveFileNm = explode('^|^', $value['saveFileNm']);

                if($saveFileNm[0]){

                    $arr = [];

                    //이미지 여러개 뿌리기
                    foreach ($saveFileNm as $k => $val){
                        $arr[$k]['imgList'] = '/data/board/'.$value['bdUploadPath'].$val;
                    }

                    $data[$key]['saveFileNmList'] = $arr;
                }

            }

            $this->setData('data',$data);
            
        }else{
            $this->js('alert("' . __('잘못된 접근입니다.') . '");'. 'location.href="http://hdmkorea.godomall.com";');
        }
    }
}
