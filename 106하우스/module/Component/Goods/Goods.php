<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link http://www.godo.co.kr
 */
namespace Component\Goods;

use Request;
/**
 * 상품 class
 */
class Goods extends \Bundle\Component\Goods\Goods
{
    // 리스트 출력데이터
    public function getGoodsList($cateCd, $cateMode = 'category', $pageNum = 10, $displayOrder = 'sort asc', $imageType = 'list', $optionFl = false, $soldOutFl = true, $brandFl = false, $couponPriceFl = false, $imageViewSize = 0, $displayCnt = 10)
    {
        
        $req = Request::post()->toArray();
       
        // 사이즈
        if($req['filterSize'] && count($req['filterSize']) > 0){
            
            $filterWhere = '(';
            foreach ($req['filterSize'] as $value) {

                $this->db->bind_param_push($this->arrBind,'s','%'.$value.'%');
                $filterWhere .= " g.filter_size LIKE ? or";
                
            }
            
            $filterWhere = substr($filterWhere, 0, -2);
            $filterWhere .= ")";
            $this->arrWhere[] = $filterWhere;
            
        }
        
        if($req['pageNum']){
            $pageNum = $req['pageNum'];
        }
        
        if($req['page'] && count($req['page']) > 0){
            $page = \App::load('\\Component\\Page\\Page', $req['page']);
            $page->page['list'] = $pageNum; // 페이지당 리스트 수
            $page->block['cnt'] = !Request::isMobile() ? 5 : 10; // 블록당 리스트 개수
            $page->setPage();
            $page->setUrl(\Request::getQueryString());
        }

         // 가격
        if($req['filterPrice'] && count($req['filterPrice']) > 0){
            
            $filterWhere = '(';
            foreach ($req['filterPrice'] as $value){

                $filterPrice = 0;
                
                if(strpos($value, '이상') !== false){
                    
                    $filterPrice = $this->setFilterHighPrice($value);
                    
                    $filterWhere .= "  g.goodsPrice >= ?  or";
                    $this->db->bind_param_push($this->arrBind, 'i', $filterPrice);

                }else{
                    
                    $filterPrice = $this->setFilterPrice($value);
                    $filterWhere .= "  g.goodsPrice <= ?  or";
                    $this->db->bind_param_push($this->arrBind, 'i', $filterPrice);
                }
                
                
            }
            $filterWhere = substr($filterWhere, 0, -2);
            $filterWhere .= ')';
            $this->arrWhere[] = $filterWhere;
            
        }
        
        if($req['sort']){
            Request::get()->set('sort', $req['sort']);
        }
        
        $goodsList = parent::getGoodsList($cateCd, $cateMode, $pageNum, $displayOrder, $imageType, $optionFl, $soldOutFl, $brandFl, $couponPriceFl, $imageViewSize, $displayCnt);

        return $goodsList;
    }
    
    public function setFilterHighPrice($filterPrice) 
    {
        
        $replacePrice = str_replace("이상", "", $filterPrice);
        $realPrice = str_replace("원", "", $replacePrice);
        
        return $realPrice;
    }
    
    public function setFilterPrice($filterPrice) 
    {
        
        $realPrice = str_replace("원", "", $filterPrice);

        return $realPrice;
    }
}