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
 * 주문 완료 페이지
 *
 * @package Bundle\Controller\Front\Order
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 */
class OrderEndController extends \Bundle\Controller\Front\Order\OrderEndController
{
    public function index()
    {
        parent::index();
        
        $wib = new WibSql();
        
        $orderInfo = $this->getData('orderInfo');
        $orderNo = $orderInfo['orderNo'];
        
        $session = \App::getInstance('session');
        $companyNm = $session->get('WIB_SESSION_JOIN_INFO');
        $session->del('WIB_SESSION_JOIN_INFO');
        $nmCompanyNm = $companyNm['nmCompanyNm'];
        
        
        if($nmCompanyNm){
            
            $data = [
                'es_order',
                array('nmCompanyNm'=>[$nmCompanyNm,'s']),
                array('orderNo'=>[$orderNo,'s'])
            ];
            
            $wib->WibUpdate($data);
            
            $this->setData('nmCompanyNm',$nmCompanyNm);
            
        }else{
            //새로고침시
            $query = "SELECT * FROM es_order WHERE orderNo = {$orderNo}";
            $wibData = $wib->WibNobind($query);
            
            $nmCompanyNm = $wibData['nmCompanyNm'];
            
            $this->setData('nmCompanyNm',$nmCompanyNm);
        }

        
        
    }
}