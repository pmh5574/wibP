<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Admin\Goods;

use Component\Wib\WibSql;
use Request;

class LayerGoodsScrollListController extends \Controller\Admin\Controller
{
    public function index()
    {
        $wibSql = new WibSql();
        
        //페이징
        $page = Request::get()->get('page');
        if($page == ''){$page = 1;}
        $start = ($page-1)*10;
        $end = 10;
        
        $query = "SELECT * FROM es_goodsScroll ORDER BY sno DESC LIMIT {$start},{$end}";
        $data = $wibSql->WibAll($query);

        //페이징
        $totalQuery = "SELECT COUNT(sno) cnt FROM es_goodsScroll ORDER BY sno DESC";
        $totalData = $wibSql->WibNobind($totalQuery);

        $total = $totalData['cnt'];
        $pcnt = $total-$start;
        
        $paging = $this->paging($total, $page);
        
        
        
        $this->setData('pcnt', $pcnt);
        $this->setData('total', $total);
        $this->setData('paging', $paging);
        $this->setData('data', $data);
        
        $this->getView()->setDefine('layout', 'layout_layer.php');
        $this->getView()->setPageName('goods/layer_goods_scroll_list.php');
    }
    
    public function paging($total, $page)
    {

        $ab = ($page-1)%10;
        $startpage = $page-$ab;
        $endpage = $startpage+9;
        $pagecount = $total/10;
        $nowpage = $page;

//        $kk ='&key='.$key.'&keyword='.$keyword.'&brandNm='.$brandNm;
        $kk = '';
        if($endpage>ceil($pagecount)){$endpage = ceil($pagecount);}

        $setPage = '';
        $setPage .= '<ul class="pagination pagination-sm">';
        if($startpage != 1){
            
            $setPage .= '<li class="front-page front-page-first">';
            $setPage .= '<a aria-label="First" href="javascript:layer_goods_scroll_list(page=1'.$kk.')"><img src="/admin/gd_share/img/icon_arrow_page_ll.png" class="img-page-arrow">맨앞</a>';
            $setPage .= '</li>';
            $setPage .= '<li class="front-page front-page-prev">';
            $setPage .= '	<a aria-label="Previous" href="javascript:layer_goods_scroll_list(page='.($nowpage-1).$kk.')"><img src="/admin/gd_share/img/icon_arrow_page_l.png" class="img-page-arrow">이전</a>';
            $setPage .= '</li>';
        }


        for($i = $startpage; $i <= $endpage; $i++){
            
            if($nowpage == $i){
                $act = "active";
            }else{
                $act = "";
            }
            
            if($act == 'active'){
                $setPage .= '<li class="'.$act.'">';
                $setPage .= '<span> '.$i.' </span>';
                $setPage .= '</li>';
            }else{
                $setPage .= '<li class="'.$act.'">';
                $setPage .= '<a href="javascript:layer_goods_scroll_list(page='.($i).$kk.')">';
                $setPage .= '<span> '.$i.' </span>';
                $setPage .= '</a>';
                $setPage .= '</li>';
            }
            
        }

        if($endpage < ceil($pagecount)){

            $setPage .= '<li class="front-page front-page-next">';
            $setPage .= '<a aria-label="Next" href="javascript:layer_goods_scroll_list(page='.($nowpage+1).$kk.')"><img src="/admin/gd_share/img/icon_arrow_page_r.png" class="img-page-arrow">다음</a>';
            $setPage .= '</li>';
            $setPage .= '<li class="front-page front-page-last">';
            $setPage .= '<a aria-label="Last" href="javascript:layer_goods_scroll_list(page='.ceil($pagecount).$kk.')"><img src="/admin/gd_share/img/icon_arrow_page_rr.png" class="img-page-arrow">맨뒤</a>';
            $setPage .= '</li>';
        }
        $setPage .= '</ul>';

        return $setPage;
    }
}
