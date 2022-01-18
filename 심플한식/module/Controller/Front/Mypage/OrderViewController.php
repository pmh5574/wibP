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
namespace Controller\Front\Mypage;

use Component\Wib\WibSql;
/**
 * 주문 상세 보기 페이지
 *
 * @author artherot
 * @version 1.0
 * @since 1.0
 * @copyright Copyright (c), Godosoft
 */
class OrderViewController extends \Bundle\Controller\Front\Mypage\OrderViewController
{
    public function index()
    {
        parent::index();
        
        $wib = new WibSql();
        
        $orderInfo = $this->getData('orderInfo');
        
        $query = "SELECT nmCompanyNm FROM es_order WHERE orderNo = {$orderInfo['orderNo']}";
        $data = $wib->WibNobind($query);
        
        if($data['nmCompanyNm']) {
            $this->setData('nmCompanyNm',$data['nmCompanyNm']);
        }
        
    }
}