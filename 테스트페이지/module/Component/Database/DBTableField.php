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
        $arrField[] = ['val' => 'wbRelationGroupNo', 'typ' => 's', 'def' => null, 'name' => '연관상품 그룹번호']; // 연관상품 그룹번호
        $arrField[] = ['val' => 'wbOptionGubun', 'typ' => 's', 'def' => null, 'name' => '옵션 구분(일반,색상,사이즈)']; // 옵션 구분(일반,색상,사이즈)
        $arrField[] = ['val' => 'goodsDeliveryKey', 'typ' => 'i', 'def' => null, 'name' => '묶음배송 키']; // 묶음배송 키
        $arrField[] = ['val' => 'scrollName', 'typ' => 's', 'def' => null, 'name' => '스크롤 그룹명']; // 스크롤 그룹명
        $arrField[] = ['val' => 'scrollSno', 'typ' => 's', 'def' => null, 'name' => '스크롤 고유번호']; // 스크롤 고유번호
//        $arrField[] = ['val' => 'clientGoodsSort', 'typ' => 'i', 'def' => null, 'name' => '상품 정렬 순서']; // 상품 정렬 순서

        return $arrField;
    }
    public static function tableGoodsSearch()
    {
        $arrField = parent::tableGoodsSearch();
        $arrField[] = ['val' => 'goodsDeliveryKey', 'typ' => 'i', 'def' => null, 'name' => '묶음배송 키']; // 묶음배송 키
//        $arrField[] = ['val' => 'clientGoodsSort', 'typ' => 'i', 'def' => null, 'name' => '상품 정렬 순서']; // 상품 정렬 순서
        return $arrField;
    }
    
    public static function tableOrderGoods()
    {
        $arrField = parent::tableOrderGoods();
        
        $arrField[] = ['val' => 'goodsRealPrice', 'typ' => 's', 'def' => null, 'name' => '상품 실 결제 금액'];
        
        return $arrField;
    }

}