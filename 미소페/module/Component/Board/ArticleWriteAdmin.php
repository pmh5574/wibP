<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Smart to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link http://www.godo.co.kr
 */
namespace Component\Board;

use Framework\Utility\ArrayUtils;

class ArticleWriteAdmin extends \Bundle\Component\Board\ArticleWriteAdmin
{
	/*
	* 매장 등록 지역 가져오는 함수
	* @author INBET Matthew 2019-03-22
	*/
	public function getAddress(){
		$arrBind = [];
        $this->db->strField = " addrNo,addrDepth,addrParentNo,addrSort,addrName,regDt ";
		
        $query = $this->db->query_complete();
        $strSQL = "SELECT " . array_shift($query) . " FROM " . 'es_addressInfo' . " " . implode(" ", $query)."ORDER BY addrSort asc";
        $data = $this->db->query_fetch($strSQL, $arrBind);
       
		unset($arrBind);
		
        return gd_htmlspecialchars_stripslashes($data);
	}
	
	 

}