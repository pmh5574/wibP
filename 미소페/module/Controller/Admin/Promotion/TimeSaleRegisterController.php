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

use Component\Wib\WibSql;

class TimeSaleRegisterController extends \Bundle\Controller\Admin\Promotion\TimeSaleRegisterController
{
    public function index()
    {
        parent::index();
        
        $wib = new WibSql();
        

        $data = $this->getData('data');

        $strSQL = "SELECT * FROM es_timeSale WHERE sno = ".$data['sno'];
        $result = $wib->WibNobind($strSQL);

        $eachBenefit = explode('||',$result['eachBenefit']);

        foreach ($data['goodsNo'] as $key => $value) {

            foreach($eachBenefit as $k => $v){

                $data['goodsNo'][$k]['eachBenefit'] = $v;

            }

        }

        $this->setData('data',$data);
    }
}