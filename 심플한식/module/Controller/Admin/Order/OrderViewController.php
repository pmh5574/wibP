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
namespace Controller\Admin\Order;

use Component\Wib\WibSql;
/**
 * 주문 상세 페이지
 * [관리자 모드] 주문 상세 페이지
 *
 * @package Bundle\Controller\Admin\Order
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 */
class OrderViewController extends \Bundle\Controller\Admin\Order\OrderViewController
{
    public function index()
    {
        parent::index();
        
        $wib = new WibSql();
        
        $data = $this->getData('data');
        
        $query = "SELECT * FROM es_order WHERE orderNo = {$data['orderNo']}";
        $result = $wib->WibNobind($query);
        
        foreach ($data as $val){
            $data['nmCompanyNm'] = $result['nmCompanyNm'];
        }
        
        $this->setData('data',$data);
    }
}