<?php
namespace Component\Wib;

use Component\Wib\WibSql;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class WibSave
{
    private $companyNm;
    private $wibSql;

    public function __construct() 
    {
        $this->wibSql = new WibSql();
    }
    
    public function setCompanyNm($data)
    {
        $this->companyNm = $data;
    }
    
    public function getCompanyNm()
    {
        return $this->companyNm;
    }
    
    // 등록된 회사 정보
    public function getCompanyName($mode) 
    {
        $query = "SELECT companyName FROM es_companyName WHERE sno = 1";
        
        $data = [];
        $list = [];
        
        if($mode == 'admin'){
            
            $data = $this->wibSql->WibNobind($query);
            $list = unserialize(stripslashes($data['companyName']));
            
        }else if($mode == 'front'){
            
            $data = $this->wibSql->WibAll($query);
            $list = unserialize(stripslashes($data[0]['companyName']));
            
        }
        
        return $list;
    }
    
    //회사 정보 업데이트
    public function companyNameUpdate($list)
    {
        $data = [];
        
        $data['companyName'] = array($list,'s');
        $data['regDt'] = array(date('Y-m-d H:i:s'),'s');

        $query = array(
            'es_companyName',
            $data,
            array('sno'=>array('1','i'))
        );
        
        $this->wibSql->WibUpdate($query);
    }
    
    // 특정상품이 있는지 체크
    public function getGoodsSpecific($goodsNo)
    {
        $strSQL = "SELECT goodsSpecific FROM es_goods WHERE goodsNo = {$goodsNo}";
        $mResult = $this->wibSql->WibNobind($strSQL);
        
        return $mResult;
    }
}
