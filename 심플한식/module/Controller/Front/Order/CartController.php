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
namespace Controller\Front\Order;

use Component\Wib\WibSql;
/**
 * 장바구니
 *
 * @author Ahn Jong-tae <qnibus@godo.co.kr>
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class CartController extends \Bundle\Controller\Front\Order\CartController
{
    public function index()
    {
        parent::index();
        
        $wib = new WibSql();
        $cartInfo = $this->getData('cartInfo');
        
        $specificChecks='n';
        $wibChecks = '';
        if(gd_is_login() === false){
            foreach($cartInfo as $key=>$value){
                foreach($value as $akey=>$aval){
                    foreach($aval as $k=>$val){

                        $strSQL = "SELECT * FROM es_goods WHERE goodsNo = {$val['goodsNo']}";
                        $mResult = $wib->WibNobind($strSQL);
                        
                        $specificChecks = $mResult['goodsSpecific'];
                        if($specificChecks == 'y'){
                            $wibChecks .= 'y^|^';
                        }else{
                            $wibChecks .= 'n^|^';
                        }
                        $cartInfo[$key][$akey][$k]['goodsSpecific'] = $specificChecks;
                        
                    }
                }
            }

            if(strpos($wibChecks, 'y') !== false) {
                $this->setData('wibChecks','y');
            }

            $this->setData('cartInfo',$cartInfo);

        }
    }
}