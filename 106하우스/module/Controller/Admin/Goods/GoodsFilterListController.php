<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Controller\Admin\Goods;


use Exception;
use Globals;
use Request;
/**
 * 상품 필터 리스트 페이지
 */
class GoodsFilterListController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('goods', 'goods', 'filterList');
        
        //uri주소를 list로 바꿈
        Request::server()->set('PHP_SELF','/goods/goods_list.php');
        
        // 모듈호출
        $cate = \App::load('\\Component\\Category\\CategoryAdmin');
        $brand = \App::load('\\Component\\Category\\BrandAdmin');
        $goods = \App::load('\\Component\\Goods\\GoodsAdmin');

        // --- 상품 리스트 데이터
        try {

            /* 운영자별 검색 설정값 */
            $searchConf = \App::load('\\Component\\Member\\ManagerSearchConfig');
            $searchConf->setGetData();

            //검색 - 배송비관련
            $mode['fix'] = [
                'free'   => __('배송비무료'),
                'price'  => __('금액별배송'),
                'count'  => __('수량별배송'),
                'weight' => __('무게별배송'),
                'fixed'  => __('고정배송비'),
            ];
            //검색 - 아이콘 관련
            $getIcon = $goods->getManageGoodsIconInfo();

            $getData = $goods->getAdminListGoods();
            $page = \App::load('\\Component\\Page\\Page'); // 페이지 재설정

            $this->setData('stateCount', $getData['stateCount']); // 상품 품절, 노출 개수
            //상품 그리드 설정
            $goodsAdminGrid = \App::load('\\Component\\Goods\\GoodsAdminGrid');
            $goodsAdminGridMode = $goodsAdminGrid->getGoodsAdminGridMode();
            
            foreach($getData['goodsGridConfigList'] as $key => $value){
                if(!($key == 'check' || $key == 'no' || $key == 'goodsNo' || $key == 'goodsImage' || $key == 'display' || $key == 'goodsNm')){
                    unset($getData['goodsGridConfigList'][$key]);
                }
                $getData['goodsGridConfigList']['goodsFilter'] = '상품 필터';
            }
            
            $this->setData('goodsAdminGridMode', $goodsAdminGridMode);
            $this->setData('goodsGridConfigList', $getData['goodsGridConfigList']); // 상품 그리드 항목

            
            if(!gd_is_provider()) {
                $goodsBenefit = \App::load('\\Component\\Goods\\GoodsBenefit');
                $goodsBenefitSelect = $goodsBenefit->goodsBenefitSelect($getData['search']);
            }

            // --- 관리자 디자인 템플릿
            $this->getView()->setDefine('goodsSearchFrm',  Request::getDirectoryUri() . '/goods_filter_list_search.php');

            $this->addScript([
                'jquery/jquery.multi_select_box.js',
            ]);
            
            $cateGoods = Request::get()->get('cateGoods');
            $secondCategoryChecks = 'n';

            if($cateGoods[1]){
                $secondCategoryChecks = 'y';
            }
            
            $thirdCategory = $cateGoods[2] ? $cateGoods[2]:$cateGoods[1];

            $this->setData('thirdCategory', $thirdCategory);
            $this->setData('secondCategoryChecks',$secondCategoryChecks);

            
            $this->setData('goods', $goods);
            $this->setData('cate', $cate);
            $this->setData('brand', $brand);
            $this->setData('data', $getData['data']);
            $this->setData('search', $getData['search']);
            $this->setData('sort', $getData['sort']);
            $this->setData('checked', $getData['checked']);
            $this->setData('selected', $getData['selected']);
            $this->setData('page', $page);
            $this->setData('getIcon', $getIcon);
            $this->setData('mode', $mode);
            $this->setData('_delivery', Globals::get('gDelivery'));
            $this->setData('goodsBenefitSelect', $goodsBenefitSelect);

            if(Request::get()->get('delFl') =='y')  {
                $this->getView()->setPageName('goods/goods_list_delete');
                if(gd_is_provider()) $this->setData('searchConfigButton', 'hide');
            } else {
                $this->getView()->setPageName('goods/goods_filter_list.php');
            }

            // 그리드 항목에 따른 페이지 include  - (인기, 메인, 카테고리 포함일 경우)
            if($getData['goodsGridConfigListDisplayFl'] === true ) { // 추가그리드항목 영역
                $this->getView()->setDefine('goodsListGridAddDisplay', 'goods/layer_goods_list_grid_add.php');// 리스트폼
            }

        } catch (Exception $e) {
            throw $e;
        }
    }
}