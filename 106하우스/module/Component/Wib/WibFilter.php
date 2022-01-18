<?php
namespace Component\Wib;

use Component\Wib\WibSql;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class WibFilter 
{
    public $wibSql = null;
    public $filter_size = null;
    public $goodsTable = [];

    public function __construct() {  
        
        $this->wibSql = new WibSql();
        $this->goodsTable = [
            'es_goods',
            'es_goodsSearch'
        ];
    }
    
    
    public function setGoodsFilter($goodsNo,$postValue)
    {
        
        //기존 검색필터 데이터 불러오기
        $filterSize = implode('^|^', $postValue['filter_size']);
        
        $lastData['filter_size'] = [$filterSize, 's'];
        
        $this->setUpdateGoodsFilter($goodsNo, $lastData);
        
    }
    
    public function setUpdateGoodsFilter($goodsNo, $lastData)
    {
        
        foreach($this->goodsTable as $value){
            $data = [
                $value,
                $lastData,
                array('goodsNo' => [$goodsNo,'i'])
            ];

            $this->wibSql->WibUpdate($data);
        }
        
    }
    
    //필터 삭제
    public function deleteGoodsFilter($goodsNo)
    {
        $lastData['filter_size'] = ['','s'];
        
        $this->setUpdateGoodsFilter($goodsNo, $lastData);
        
    }
}
