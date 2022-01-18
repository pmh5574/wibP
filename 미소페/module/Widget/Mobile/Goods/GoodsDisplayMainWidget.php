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
namespace Widget\Mobile\Goods;

use Request;
use Component\Wib\WibGoods;

class GoodsDisplayMainWidget extends \Bundle\Widget\Mobile\Goods\GoodsDisplayMainWidget
{
    public function index()
    {
        /** 원래 GoodsDisplayMainWidget에 있던 내용인데 $themeInfo['displayType'] 가져오기 위해서 씀 */
        if (is_null($this->getData('soldoutDisplay'))) {
            $this->setData('soldoutDisplay', gd_policy('soldout.pc'));
        }

        Request::get()->set('isMain',true);

        $goods = \App::load('\\Component\\Goods\\Goods');
        $getData = $goods->getDisplayThemeInfo($this->getData('sno'));
        $mainLinkData = [
            'mainThemeSno' => $getData['sno'],
            'mainThemeNm' => $getData['themeNm'],
            'mainThemeDevice' => $getData['mobileFl'],
        ];
        Request::get()->set('mainLinkData',$mainLinkData);
        //기획전 그룹형 그룹정보 로드
        if((int)$this->getData('groupSno') > 0) {
            $eventGroup = \App::load('\\Component\\Promotion\\EventGroupTheme');
            $getData = $eventGroup->replaceEventData($this->getData('groupSno'), $getData, 'pc');
        }

        //다른기획전 보기
        $getData['otherEventData'] = $goods->getDisplayOtherEventList();

        if($getData['kind'] === 'event'){
            //하단 더보기노출 미사용일시 전체노출
            if($getData['moreBottomFl'] === 'y'){
                $this->setData('viewType', '');
            }
        }

        if ($this->getData('isMobile') == 'y') {
            $themeCd = $getData['mobileThemeCd'];
        }
        else {
            $themeCd = $getData['themeCd'];
        }

        $displayConfig = \App::load('\\Component\\Display\\DisplayConfig');
        if (empty($themeCd) === true) {
            $themeInfo = $displayConfig->getInfoThemeConfigCate('B')[0];
        } else {
            $themeInfo = $displayConfig->getInfoThemeConfig($themeCd);
        }
        
        
        parent::index();
        
        $wib = new WibGoods();


        $gdlist = $this->getData('goodsList');
 
        
        if($themeInfo['displayType'] == '01' || $themeInfo['displayType'] == '02' || $themeInfo['displayType'] == '11'){

            $goodsList = $wib->WibFe($gdlist);

            $this->setData("goodsList", $goodsList);
        }

            
        
    }
}