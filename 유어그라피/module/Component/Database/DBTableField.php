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
namespace Component\Database;

/**
 * DB Table 기본 Field 클래스 - DB 테이블의 기본 필드를 설정한 클래스 이며, prepare query 생성시 필요한 기본 필드 정보임
 * @package Component\Database
 * @static  tableConfig
 */
class DBTableField extends \Bundle\Component\Database\DBTableField
{
    // 상품테이블 컬럼정보 추가
    public static function tableGoods($conf = null)
    {
        
        $arrField = parent::tableGoods($conf);
        $arrField[] = ['val' => 'coreColor', 'typ' => 's', 'def' => null,]; // 색상 코어
        $arrField[] = ['val' => 'coreType', 'typ' => 's', 'def' => null,]; // 색상 재질
        $arrField[] = ['val' => 'oriLength', 'typ' => 'i', 'def' => null,]; // 원봉 길이
        $arrField[] = ['val' => 'oriPrice', 'typ' => 'i', 'def' => null,]; // 원봉 단가
        
        $arrField[] = ['val' => 'frontSize', 'typ' => 'i', 'def' => null,]; // 원봉 단가
        $arrField[] = ['val' => 'sideSize', 'typ' => 'i', 'def' => null,]; // 원봉 단가
        $arrField[] = ['val' => 'innerSize', 'typ' => 'i', 'def' => null,]; // 원봉 단가
        
        $arrField[] = ['val' => 'ppA', 'typ' => 'i', 'def' => null,]; // P17-1
        $arrField[] = ['val' => 'ppB', 'typ' => 'i', 'def' => null,]; // P17-2
        $arrField[] = ['val' => 'ppC', 'typ' => 'i', 'def' => null,]; // P17-3
        $arrField[] = ['val' => 'ppD', 'typ' => 'i', 'def' => null,]; // P17-4
        $arrField[] = ['val' => 'ppE', 'typ' => 'i', 'def' => null,]; // P17-5
        $arrField[] = ['val' => 'ppF', 'typ' => 'i', 'def' => null,]; // P17-6
        $arrField[] = ['val' => 'ppG', 'typ' => 'i', 'def' => null,]; // P17-7
        
        return $arrField;
        
    }

}