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
namespace Component\Excel;


/**
 * Class ExcelForm
 * @package Bundle\Component\Excel
 * @author  yjwee <yeongjong.wee@godo.co.kr>
 *          atomyang
 */
class ExcelForm extends \Bundle\Component\Excel\ExcelForm
{
    public function setExcelFormOrder($location)
    {
        $setData = parent::setExcelFormOrder($location);
        
        $wibData = ['goodsRealPrice' =>['name'=>__('상품 실 결제 금액') ,'type'=>'price']];

        $setData = array_merge($setData, $wibData);
//                print_r($setData);
//        exit;
        return $setData;
    }
}