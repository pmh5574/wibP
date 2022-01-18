<?php
namespace Controller\Front\Frame;

use Controller\Front\Frame\GoodsPsController as goodsps;

class FrameStartController extends \Controller\Front\Controller
{
    public function index()
    {
        $goods = new goodsps();
       
        
        // 컬러 리스트
        $colorList = $goods->getColorList();
        
        // 프레임 리스트
        $frameList = $goods->getGoodsList($colorList[0]['coreColor'], '원목');

        // 미설정시 기본 상품넘버
        $basicGoods = $goods->getBasicGoodsNo($colorList[0]['coreColor']);
        
        // 초기 프레임 이미지 리스트
        $frameImageList = $goods->getGoodsDetailImg($basicGoods);

        // 초기 프레임 정보
        $goodsView = $goods->getGoodsInfo($basicGoods);
        
        // 인건비
        $manPrice = $goods->getManrice();
        
        // 인쇄용지 리스트
        $artList = $goods->getArtList();
        
        // 커버 [부자재] 리스트
        $coverList = $goods->getCoverList();
        
        // 액자 배송 예정일
        $nodate = date('Ymd');
        $deliDate = date('Y년m월d일', strtotime($nodate+' +7 day'));
        
        // 데이터 셋팅
        $this->setData('basicGoodsNo', $basicGoods);
        $this->setData('basicColor', $colorList[0]['coreColor']);
        $this->setData('frameImage', $frameImageList);
        $this->setData('frameList', $frameList);
        $this->setData('colorList', $colorList);
        $this->setData('goodsView', $goodsView);
        $this->setData('goodsViewJson', json_encode($goodsView));
        $this->setData('deliDate', $deliDate);
        $this->setData('manPrice', json_encode($manPrice));
        $this->setData('artList', $artList);
        $this->setData('coverList', $coverList);
        
        $this->getView()->setDefine("tpl", "frame/frame.html");
    }
    
}
