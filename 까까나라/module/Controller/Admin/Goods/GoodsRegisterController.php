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
namespace Controller\Admin\Goods;

use Component\Wib\WibSql;
/**
 * 상품 등록 / 수정 페이지
 */
class GoodsRegisterController extends \Bundle\Controller\Admin\Goods\GoodsRegisterController
{
    public function index()
    {
        parent::index();
        $wib = new WibSql();
        
        $data = $this->getData('data');
        
        
        $strSQL = "SELECT * FROM es_scmDeliveryBundle WHERE sno = {$data['goodsDeliveryKey']}";
        $result = $wib->WibNobind($strSQL);
        
        $data['scmBundleMethod'] = $result['method'];

        $this->setData('data',$data);
    }
}