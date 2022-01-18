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
 * @link      http://www.godo.co.kr
 */
namespace Controller\Front\Order;

use Component\Wib\WibSave;
/**
 * 주문서 작성
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class OrderController extends \Bundle\Controller\Front\Order\OrderController
{
    public function index()
    {
        parent::index();
        
        $wibCompany = new WibSave();
        
        $cartInfo = $this->getData('cartInfo');
        
        $specificChecks='n';
        
        if(gd_is_login() === false){
            foreach($cartInfo as $key=>$value){
                foreach($value as $akey=>$aval){
                    foreach($aval as $k=>$val){
                        
                        //특정 상품인지 체크
                        $mResult = $wibCompany->getGoodsSpecific($val['goodsNo']);
                        
                        $specificChecks = $mResult['goodsSpecific'];
                        if($specificChecks == 'y'){
                            $specificChecks = 'y';
                        }
                        
                    }
                }
            }
            
            if($specificChecks == 'y'){
                
                //등록된 회사명 데이터
                $companyName = $wibCompany->getCompanyName('front');

            }
            
            
            $this->setData('companyNameList', $companyName);
            $this->setData('specificChecks',$specificChecks);

        }
    }
}