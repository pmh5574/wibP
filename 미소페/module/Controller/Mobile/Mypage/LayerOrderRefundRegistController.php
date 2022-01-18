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
namespace Controller\Mobile\Mypage;

use Request;
use Component\Wib\WibSql;
/**
 * Class MypageQnaController
 *
 * @package Bundle\Controller\Front\Mypage
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 */
class LayerOrderRefundRegistController extends \Bundle\Controller\Mobile\Mypage\LayerOrderRefundRegistController
{
    public function index()
    {
        parent::index();
        
        $wib = new WibSql();
        
        $orderNo = Request::get()->get('orderNo');
        
        
        $wql = "SELECT settleKind FROM es_order WHERE orderNo = {$orderNo}";
        $qData = $wib->WibNobind($wql);
            
        
        
        $this->setData('settleKind',$qData['settleKind']);
    }
}