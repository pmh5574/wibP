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
 * Class ExcelDataConvert
 *
 * Excel 저장 및 다운 로드
 * 상품, 회원 Excel 업로드 및 다운로드 관련 Class
 *
 * @package Bundle\Component\Excel
 * @author  artherot
 */
class ExcelDataConvert extends \Bundle\Component\Excel\ExcelDataConvert
{
    public static function excelGoods()
    {
        $arrField = parent::excelGoods();
        $arrField[] = [
            'dbName' => 'goods',
            'dbKey' => 'goodsDeliveryKey',
            'excelKey' => 'goods_delivery_key',
            'text' => '추가',
            'desc' => '추가'
        ];
        
        return $arrField;
    }
    
    
}