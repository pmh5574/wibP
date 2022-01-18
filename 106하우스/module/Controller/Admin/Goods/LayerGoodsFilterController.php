<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
nameSpace Controller\Admin\Goods;

use Request;
use Component\Wib\WibSql;

class LayerGoodsFilterController extends \Controller\Admin\Controller
{
    public function index()
    {
        $postValue = Request::post()->toArray();
        
        $goodsNo = $postValue['goodsNo'];
        $category = $postValue['category'];
        
        //카테고리 이름
        $categoryName = $this->getCategoryName($category);
        
        $this->getView()->setDefine('layout', 'layout_layer.php');
        
        $this->setData('categoryName',$categoryName);
        $this->setData('goodsNo',$goodsNo);
        $this->setData('category',$category);
    }
    
    public function getCategoryName($category)
    {
        $wibSql = new WibSql();
        
        $query = "SELECT cateNm FROM es_categoryGoods WHERE cateCd = {$category}";
        $data = $wibSql->WibNobind($query);
        
        return $data['cateNm'];
    }
}