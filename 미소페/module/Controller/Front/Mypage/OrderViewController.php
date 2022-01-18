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

use Request;
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
        
        $wib= new WibSql();
        
        $orderNo = Request::get()->get('orderNo');
            
        $strSQL = "SELECT * FROM es_orderUserHandle WHERE orderNo = {$orderNo}";
        $result = $wib->WibNobind($strSQL);

        switch($result['deliveryRefundType']){

            case 'r':
                $result['deliveryRefundType'] = '환불금차감';

                break;

            case 'b':
                $result['deliveryRefundType'] = '상자동봉';

                break;
            case 'd':
                $result['deliveryRefundType'] = '입금';

                break;
            default:
                $result['deliveryRefundType'] = '';

                break;

        }

        $deliveryRefundType = $result['deliveryRefundType'];

        $this->setData('deliveryRefundType',$deliveryRefundType);
        
    }
}