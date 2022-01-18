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
    public static function tableGoods($conf = null)
    {
        $arrField = parent::tableGoods($conf);
        $arrField[] = ['val' => 'wibType', 'typ' => 's', 'def' => null, 'name' => '옵션 구분(일반,색상,사이즈)']; // 옵션 구분(일반,색상)
        $arrField[] = ['val' => 'colorOptions', 'typ' => 's', 'def' => null, 'name' => '옵션 구분(일반,색상,사이즈)']; // 색상명 및 색상코드 리스트
        
        return $arrField;
    }
}