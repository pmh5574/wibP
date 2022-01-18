<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Goods;

/**
 * Description of Goods
 *
 * @author kwonyong
 */
class Goods extends \Bundle\Component\Goods\Goods
{
    public function setSearchGoodsList($getValue = null, $searchTerms = null)
    {
        parent::setSearchGoodsList($getValue, $searchTerms);
        
        $date = date('Y-m-d H:i:s');
        //echo $date;
        
        $this->arrWhere[] = '( ( g.salesStartYmd <= ? and g.salesEndYmd >= ? ) or ( g.salesStartYmd = ? ) )';
        //$this->arrWhere[] = 'g.salesEndYmd >= ?';
        $this->db->bind_param_push($this->arrBind,'s',$date);
        $this->db->bind_param_push($this->arrBind,'s',$date);
        $this->db->bind_param_push($this->arrBind,'s','0000-00-00 00:00:00');
        
        //$this->arrWhere[] = " g.salesStartYmd < '{$date}' ";
        //$this->arrWhere[] = " ( g.salesStartYmd <= {$date} and g.salesEndYmd >= {$date} ) ";
    }
    
    public function getGoodsList($cateCd, $cateMode = 'category', $pageNum = 10, $displayOrder = 'sort asc', $imageType = 'list', $optionFl = false, $soldOutFl = true, $brandFl = false, $couponPriceFl = false, $imageViewSize = 0, $displayCnt = 10)
    {
        $date = date('Y-m-d H:i:s');
        
        $this->arrWhere[] = '( ( g.salesStartYmd <= ? and g.salesEndYmd >= ? ) or ( g.salesStartYmd = ? ) )';
        $this->db->bind_param_push($this->arrBind,'s',$date);
        $this->db->bind_param_push($this->arrBind,'s',$date);
        $this->db->bind_param_push($this->arrBind,'s','0000-00-00 00:00:00');
        
        $return = parent::getGoodsList($cateCd, $cateMode = 'category', $pageNum, $displayOrder, $imageType, $optionFl, $soldOutFl, $brandFl, $couponPriceFl, $imageViewSize, $displayCnt);
       
        return $return;
    }
}
