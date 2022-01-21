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
	public static function tableOrderGoods()
    {
		$arrField = parent::tableOrderGoods();
		
		$arrField[] = ["val" => "lpinfo", "typ" => "s", "def" => ""]; // 링크프라이스 쿠키 값
		
		return $arrField;
	}
	
	/* 웹앤모바일 Table 추가 */
	public static function tableWmLpinfo()
	{
		$arrField = [
			['val' => 'order_id', 'typ' => 's', 'def' => ''], // 주문번호	
			['val' => 'product_id', 'typ' => 's', 'def' => ''], // 상품 ID
			['val' => 'order_id', 'typ' => 's', 'def' => ''],
			['val' => 'lpinfo', 'typ' => 's', 'def' => ''],
			['val' => 'user_agent', 'typ' => 's', 'def' => ''],
			['val' => 'ip', 'typ' => 's', 'def' => ''],
			['val' => 'device_type', 'typ' => 's', 'def' => ''],
			['val' => 'isSent', 'typ' => 'i', 'def' => 0], 
		];
		return $arrField;
	}
	
	/* 웹앤모바일 Table 추가 END */
}