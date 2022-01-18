<?php

/**
 * 상품노출형태 관리
 * @author atomyang
 * @version 1.0
 * @since 1.0
 * @copyright ⓒ 2016, NHN godo: Corp.
 */

namespace Component\Promotion;

use Component\Wib\WibSql;

class TimeSaleAdmin extends \Bundle\Component\Promotion\TimeSaleAdmin {

    public function saveInfoTimeSale($arrData) 
    {
        $wib = new WibSql();


        $oriarrData = parent::saveInfoTimeSale($arrData);

        foreach ($arrData['eachBenefit'] as $key => $value) {

            if (trim($value) == false) {
                $arrData['eachBenefit'][$key] = $arrData['benefit'];
            }
        }

        if (!$oriarrData) {

            $query = "SELECT sno FROM es_timeSale ORDER BY sno DESC LIMIT 1";
            $sno = $wib->WibNobind($query);


            $oriarrData = $sno['sno'];
        }

        $arrData['eachBenefit'] = implode(INT_DIVISION, $arrData['eachBenefit']);


        $strSQL = [
            'es_timeSale',
            array('eachBenefit' => [$arrData['eachBenefit'],'s']),
            array('sno' => [$oriarrData,'i'])
        ];
        $wib->WibUpdate($strSQL);

        return $oriarrData;
    }

}
