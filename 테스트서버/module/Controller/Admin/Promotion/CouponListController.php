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
namespace Controller\Admin\Promotion;


class CouponListController extends \Bundle\Controller\Admin\Promotion\CouponListController
{
    public function index()
    {
        parent::index();
        
        // 쿠폰 리스트에 비화원 쿠폰종류 유형 추가
        $hookData = $this->getData('data');
        foreach ($hookData as $key => $value) {
            if($value['couponUseType'] == ''){
                $hookData[$key]['couponUseType'] = 'paper';
            }
        }
        // 쿠폰 리스트 데이터 재셋팅
        $this->setData('data', $hookData);
        
        // 쿠폰유형에 비회원쿠폰유형 추가
        $hookConvertArrData = $this->getData('convertArrData');
        foreach ($hookConvertArrData as $key => $value) {
            if(!isset($value['couponUseType'])){
                $hookConvertArrData[$key]['couponUseType'] = '비회원쿠폰';
            }
        }
        // 쿠폰 변형데이터 재셋팅
        $this->setData('convertArrData', $hookConvertArrData);
    }
}