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
        
        if($location == 'order_list_all'){
            $setData['nmCompanyNm'] = array('name'=>__('회사명'));
        }
        
        return $setData;
    }
    public function getInfoExcelForm($sno = null, $goodsField = null, $arrBind = null, $dataArray = false)
    {
        
        $getData = parent::getInfoExcelForm($sno, $goodsField, $arrBind, $dataArray);
        
        $getData['excelField'] .= '^|^nmCompanyNm';
        
        return $getData;
    }
}