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
	 /**
     * [게시판] store 게시판 기본값
     *
     * @author INBET matthew 2019-03-22
     * @return array board 테이블 필드 정보
     */
    public static function tableBdStore()
    {
        // @formatter:off
        $arrField = [
           
			['val' => 'storeTitle', 'typ' => 's', 'def' => null],
			['val' => 'storeSiDo', 'typ' => 's', 'def' => null],
			['val' => 'storeType', 'typ' => 's', 'def' => null],
			['val' => 'storePhoneNo', 'typ' => 's', 'def' => null],
			['val' => 'address', 'typ' => 's', 'def' => null],
			['val' => 'storeDisplayFl', 'typ' => 's', 'def' => 'n']
		];
        // @formatter:on

        return $arrField;
    }
    
    
    public static function tableGoods($conf = null)
    {
        $arrField = parent::tableGoods($conf);
        $arrField[] = ['val' => 'season', 'typ' => 's', 'def' => null,]; // 상품시즌

        return $arrField;
    }
    
    public static function tableGoodsSearch()
    {
        $arrField = parent::tableGoodsSearch();
        $arrField[] = ['val' => 'season', 'typ' => 's', 'def' => null,]; // 상품시즌

        return $arrField;
    }
    public static function tableDisplayTheme() 
    {
        $arrField = parent::tableDisplayTheme();
        $arrField[] = ['val' => 'event_thumbnail', 'typ' => 's', 'def' => null,]; //썸네일용
                
        return $arrField;
    }
}