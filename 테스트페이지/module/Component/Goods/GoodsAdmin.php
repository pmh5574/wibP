<?php

/**
 * 상품 class
 *
 * 상품 관련 관리자 Class
 * @author artherot
 * @version 1.0
 * @since 1.0
 * @copyright Copyright (c), Godosoft
 */
namespace Component\Goods;

use Component\Member\Group\Util as GroupUtil;
use Component\Member\Manager;
use Component\Page\Page;
use Component\Storage\Storage;
use Component\Database\DBTableField;
use Component\Validator\Validator;
use Framework\Debug\Exception\HttpException;
use Framework\Debug\Exception\AlertBackException;
use Framework\File\FileHandler;
use Framework\Utility\ImageUtils;
use Framework\Utility\StringUtils;
use Framework\Utility\ArrayUtils;
use Encryptor;
use Globals;
use LogHandler;
use UserFilePath;
use Request;
use Exception;
use Session;
use App;

class GoodsAdmin extends \Bundle\Component\Goods\GoodsAdmin
{
    public function saveInfoGoods($arrData) 
    {

        // 옵션구분 (일반, 색상, 사이즈) 저장
        $optionGubun = is_array($arrData['optionY']['optionGubun']);

        if($optionGubun) {
            $arrData['wbOptionGubun'] = implode(STR_DIVISION, $arrData['optionY']['optionGubun']);
            $arrBind = $this->db->get_binding(DBTableField::getBindField('tableGoods', 'wbOptionGubun'), $arrData, 'update', 'wbOptionGubun', null);
            $this->db->bind_param_push($arrBind['bind'], 'i', $arrData['goodsNo']);
            $this->db->set_update_db(DB_GOODS, $arrBind['param'], 'goodsNo = ?', $arrBind['bind']);
            unset($arrBind);
        }


        //202-01-01 썸네일 외부영상 튜닝시작
        //이부분은 원래 saveInfoGoods 실행하는 부분
        $applyFl = parent::saveInfoGoods($arrData);
        // $goodsNo = $this->goodsNo;//관리자 메모2추가 연습
        $goodsNo = $arrData['goodsNo'];
		//위랑 아래랑 같음
		//$goodsNo =  $arrData['goodsNo'];//관리자 메모2추가 연습
        
		$strSQL = "UPDATE es_goods SET memo2 = ? WHERE goodsNo = ?";
		$this->db->bind_param_push($arrBind, 's', $arrData['memo2']);
		$this->db->bind_param_push($arrBind, 'i', $goodsNo);
        $this->db->bind_query($strSQL, $arrBind);
        unset($arrBind);

        $goodsDeliveryBundle = $arrData['goodsDeliveryBundle'];//라디오값
        $deliveryBundleOriSno = $arrData['deliveryBundleOriSno'];
        $deliveryBundleSno = $arrData['deliveryBundleSno'];

        $scrollName = implode('^|^', $arrData['scrollName']);
        $scrollSno = implode('^|^', $arrData['scrollSno']);
        //까까나라 추가작업 연습
        $query = "UPDATE es_goods SET goodsDeliveryBundle = ?, goodsDeliveryKey = ?, scrollName = '{$scrollName}', scrollSno = '{$scrollSno}' WHERE goodsNo = ?";
        $this->db->bind_param_push($arrBind, 's', $goodsDeliveryBundle);
        $this->db->bind_param_push($arrBind, 'i', $deliveryBundleSno);
		$this->db->bind_param_push($arrBind, 'i', $goodsNo);
        $this->db->bind_query($query, $arrBind);
        unset($arrBind);

        // /**같은 값 있나 체크*/
        // if($goodsDeliveryBundle){
        //     $strSQL = "SELECT * FROM es_scmDeliveryBundle WHERE sno = {$deliveryBundleSno}";
        //     $bb = $this->db->query($strSQL);
        //     $cc = $this->db->fetch($bb);
    
        //     $ff = '';
        //     $gg = [];
           
        //     if($goodsDeliveryBundle == 'y'){
                
        //         $ff .= $cc['allGoodsNo'].$goodsNo.'^|^';
                
        //     }else if($goodsDeliveryBundle == 'n'){

        //         $ff .= str_replace($goodsNo.'^|^','',$cc['allGoodsNo']);
                
        //     }
                       

        //     $query = "UPDATE es_scmDeliveryBundle SET allGoodsNo = ? WHERE sno = ?";
        //     $this->db->bind_param_push($arrBind, 's', $ff);
        //     $this->db->bind_param_push($arrBind, 'i', $deliveryBundleSno);
        //     $this->db->bind_query($query, $arrBind);
        //     unset($arrBind);
            
        //     //같은 값 있나 체크

            
            
        // }else{
            
        // }
        
        // /**같은 값 있나 체크*/
        // if($goodsDeliveryBundle == 'y'){
        //     if($deliveryBundleSno){

        //         $strSQL = "SELECT * FROM es_scmDeliveryBundle WHERE sno = {$deliveryBundleSno}";
        //         $bb = $this->db->query($strSQL);
        //         $cc = $this->db->fetch($bb);

        //         $ff = '';
        //         $gg = [];

        //         if($cc['allGoodsNo']){
        //             $ff .= str_replace($goodsNo.'^|^','',$cc['allGoodsNo']);
        //         }else{
        //             $ff .= $goodsNo.'^|^';
        //         }
                
        //         $query = "UPDATE es_scmDeliveryBundle SET allGoodsNo = ? WHERE sno = ?";
        //         $this->db->bind_param_push($arrBind, 's', $ff);
        //         $this->db->bind_param_push($arrBind, 'i', $deliveryBundleSno);
        //         $this->db->bind_query($query, $arrBind);
        //         unset($arrBind);

        //     }else{

        //     }
            



           
    
            
           
                       

           
            
        //     //같은 값 있나 체크

            
            
        // }else if($goodsDeliveryBundle == 'n'){
        //     if($deliveryBundleSno){

                
        //     }else{

        //     }
        // }
       

        //추가코드 (먼저 추가코드 실행하고 후에 원래코드 실행하거나 반대로 해도 됨.)
        $strUpdateSQL = "UPDATE " . DB_GOODS . " SET externalVimeoUrl = '" . $arrData['externalVimeoUrl'] . "' , externalVimeoFl = '" . $arrData['externalVimeoFl'] . "' WHERE goodsNo = '";
        $strUpdateSQL .= $this->goodsNo . "' ";
        $this->db->query($strUpdateSQL);
        //error_log(print_r($strUpdateSQL,true),3,'/www/baiwan09151_godomall_com/tmp/res.txt');
        if($arrData[''])
        //202-01-01 썸네일 외부영상 튜닝끝
		
		
		
		


        $applyFl = parent::saveInfoGoods($arrData);

        return $applyFl;
    }

    public function getDataGoodsOption($goodsNo = null) 
    {
        $getData = parent::getDataGoodsOption($goodsNo);

        $getData['data']['wbOptionGubun'] = explode(STR_DIVISION, $getData['data']['wbOptionGubun']);

        return $getData;
    }

    // public function setSearchGoods($getValue = null, $list_type = null)
    // {
    //     if (is_null($getValue)) $getValue = Request::get()->toArray();

    //     // 통합 검색
    //     /* @formatter:off */
    //     $this->search['combineSearch'] = [
    //         'goodsNm' => __('상품명'),
    //         'goodsNo' => __('상품코드'),
    //         'goodsCd' => __('자체상품코드'),
    //         'goodsSearchWord' => __('검색 키워드'),
    //         '__disable1' => '==========',
    //         'makerNm' => __('제조사'),
    //         'originNm' => __('원산지'),
    //         'goodsModelNo' => __('모델번호'),
    //         'hscode' => __('HS코드'),
    //         'addInfo' => __('추가항목'),
    //         '__disable2' => '==========',
    //         'memo' => '관리자 메모',
    //     ];
    //     /* @formatter:on */

    //     if(gd_is_provider() === false) {
    //         $this->search['combineSearch']['companyNm'] = __('공급사명');
    //         if(gd_is_plus_shop(PLUSSHOP_CODE_PURCHASE) === true) $this->search['combineSearch']['purchaseNm'] = __('매입처명');
    //     }

    //     // 검색을 위한 bind 정보
    //     $fieldTypeGoods = DBTableField::getFieldTypes('tableGoods');
    //     $fieldTypeOption = DBTableField::getFieldTypes('tableGoodsOption');
    //     $fieldTypeLink = DBTableField::getFieldTypes('tableGoodsLinkCategory');
    //     $fieldTypeIcon = DBTableField::getFieldTypes('tableGoodsIcon');

    //     //검색설정
    //     $this->search['sortList'] = array(
    //         'g.goodsNo desc' => __('등록일 ↓'),
    //         'g.goodsNo asc' => __('등록일 ↑'),
    //         'goodsNm asc' => __('상품명 ↓'),
    //         'goodsNm desc' => __('상품명 ↑'),
    //         'goodsPrice asc' => __('판매가 ↓'),
    //         'goodsPrice desc' => __('판매가 ↑'),
    //         'companyNm asc' => __('공급사 ↓'),
    //         'companyNm desc' => __('공급사 ↑'),
    //         'makerNm asc' => __('제조사 ↓'),
    //         'makerNm desc' => __('제조사 ↑'),
    //         'orderGoodsCnt desc' => __('결제 ↑'),
    //         'hitCnt desc' => __('조회 ↑'),
    //         'orderRate desc' => __('구매율 ↑'),
    //         'cartCnt desc' => __('담기 ↑'),
    //         'wishCnt desc' => __('관심 ↑'),
    //         'reviewCnt desc' => __('후기 ↑')
    //     );

    //     //삭제일추가
    //     $deleteDaySort = array (
    //         'delDt desc, g.goodsNo desc' => __('삭제일 ↓'),
    //         'delDt asc' => __('삭제일 ↑'),
    //     );

    //     if ($getValue['delFl'] == 'y') {
    //         $this->search['sortList'] = array_merge($deleteDaySort , $this->search['sortList']);
    //     }

    //     // --- 검색 설정
    //     $this->search['sort'] = gd_isset($getValue['sort'], 'g.goodsNo desc');
    //     $this->search['detailSearch'] = gd_isset($getValue['detailSearch']);
    //     $this->search['key'] = gd_isset($getValue['key']);
    //     $this->search['keyword'] = gd_isset($getValue['keyword']);
    //     $this->search['cateGoods'] = ArrayUtils::last(gd_isset($getValue['cateGoods']));
    //     $this->search['displayTheme'] = gd_isset($getValue['displayTheme']);
    //     $this->search['brand'] = ArrayUtils::last(gd_isset($getValue['brand']));
    //     $this->search['brandCd'] = gd_isset($getValue['brandCd']);
    //     $this->search['brandCdNm'] = gd_isset($getValue['brandCdNm']);
    //     $this->search['purchaseNo'] = gd_isset($getValue['purchaseNo']);
    //     $this->search['purchaseNoNm'] = gd_isset($getValue['purchaseNoNm']);
    //     $this->search['goodsPrice'][] = gd_isset($getValue['goodsPrice'][0]);
    //     $this->search['goodsPrice'][] = gd_isset($getValue['goodsPrice'][1]);
    //     $this->search['mileage'][] = gd_isset($getValue['mileage'][0]);
    //     $this->search['mileage'][] = gd_isset($getValue['mileage'][1]);
    //     $this->search['optionFl'] = gd_isset($getValue['optionFl']);
    //     $this->search['mileageFl'] = gd_isset($getValue['mileageFl']);
    //     $this->search['optionTextFl'] = gd_isset($getValue['optionTextFl']);
    //     $this->search['goodsDisplayFl'] = gd_isset($getValue['goodsDisplayFl']);
    //     $this->search['goodsDisplayMobileFl'] = gd_isset($getValue['goodsDisplayMobileFl']);
    //     $this->search['goodsSellFl'] = gd_isset($getValue['goodsSellFl']);
    //     $this->search['goodsSellMobileFl'] = gd_isset($getValue['goodsSellMobileFl']);
    //     $this->search['stockFl'] = gd_isset($getValue['stockFl']);
    //     $this->search['stock'] = gd_isset($getValue['stock']);
    //     $this->search['stockStateFl'] = gd_isset($getValue['stockStateFl'], 'all');
    //     $this->search['soldOut'] = gd_isset($getValue['soldOut']);
    //     $this->search['sellStopFl'] = gd_isset($getValue['sellStopFl']);
    //     $this->search['confirmRequestFl'] = gd_isset($getValue['confirmRequestFl']);
    //     $this->search['goodsIconCdPeriod'] = gd_isset($getValue['goodsIconCdPeriod']);
    //     $this->search['goodsIconCd'] = gd_isset($getValue['goodsIconCd']);
    //     $this->search['goodsColor'] = gd_isset($getValue['goodsColor']);
    //     $this->search['deliveryFl'] = gd_isset($getValue['deliveryFl']);
    //     $this->search['deliveryFree'] = gd_isset($getValue['deliveryFree']);

    //     $this->search['goodsDisplayMobileFl'] = gd_isset($getValue['goodsDisplayMobileFl']);
    //     $this->search['goodsSellMobileFl'] = gd_isset($getValue['goodsSellMobileFl']);
    //     $this->search['mobileDescriptionFl'] = gd_isset($getValue['mobileDescriptionFl']);
    //     $this->search['delFl'] = gd_isset($getValue['delFl'], 'n');

    //     $this->search['addGoodsFl'] = gd_isset($getValue['addGoodsFl']);
    //     $this->search['categoryNoneFl'] = gd_isset($getValue['categoryNoneFl']);
    //     $this->search['brandNoneFl'] = gd_isset($getValue['brandNoneFl']);
    //     $this->search['purchaseNoneFl'] = gd_isset($getValue['purchaseNoneFl']);

    //     $this->search['goodsDeliveryFl'] = gd_isset($getValue['goodsDeliveryFl']);
    //     $this->search['goodsDeliveryFixFl'] = gd_isset($getValue['goodsDeliveryFixFl'], array('all'));

    //     $this->search['scmFl'] = gd_isset($getValue['scmFl'], Session::get('manager.isProvider') ? 'y' : 'all');
    //     if ($this->search['scmFl'] == 'y' && !isset($getValue['scmNo']) && !Session::get('manager.isProvider')) $this->search['scmFl'] = "all";
    //     $this->search['scmNo'] = gd_isset($getValue['scmNo'], (string)Session::get('manager.scmNo'));
    //     $this->search['scmNoNm'] = gd_isset($getValue['scmNoNm'],(string) Session::get('manager.companyNm'));
    //     $this->search['searchPeriod'] = gd_isset($getValue['searchPeriod'], '-1');
    //     if ($getValue['delFl'] == 'y') {
    //         $this->search['searchDateFl'] = gd_isset($getValue['searchDateFl'], 'delDt');
    //     }
    //     $this->search['searchDateFl'] = gd_isset($getValue['searchDateFl'], 'regDt');

    //     if ($this->search['searchPeriod'] < 0) {
    //         $this->search['searchDate'][] = gd_isset($getValue['searchDate'][0]);
    //         $this->search['searchDate'][] = gd_isset($getValue['searchDate'][1]);
    //     } else {
    //         $this->search['searchDate'][] = gd_isset($getValue['searchDate'][0], date('Y-m-d', strtotime('-7 day')));
    //         $this->search['searchDate'][] = gd_isset($getValue['searchDate'][1], date('Y-m-d'));
    //     }


    //     $this->search['applyType'] = gd_isset($getValue['applyType'], 'all');
    //     $this->search['applyFl'] = gd_isset($getValue['applyFl'], 'all');
    //     $this->search['naverFl'] = gd_isset($getValue['naverFl']);
    //     $this->search['paycoFl'] = gd_isset($getValue['paycoFl']);
    //     $this->search['daumFl'] = gd_isset($getValue['daumFl']);
    //     $this->search['eventThemeSno'] = gd_isset($getValue['eventThemeSno']);
    //     $this->search['event_text'] = gd_isset($getValue['event_text']);
    //     $this->search['eventGroup'] = gd_isset($getValue['eventGroup']);
    //     $this->search['eventGroupSelectList'] = gd_isset($getValue['eventGroupSelectList']);
    //     $this->search['restockFl'] = gd_isset($getValue['restockFl'], 'all');
    //     // 상품혜택관리 검색 정보
    //     $this->search['goodsBenefitSno'] = gd_isset($getValue['goodsBenefitSno']);
    //     $this->search['goodsBenefitNm'] = gd_isset($getValue['goodsBenefitNm']);
    //     $this->search['goodsBenefitDiscount'] = gd_isset($getValue['goodsBenefitDiscount']);
    //     $this->search['goodsBenefitDiscountGroup'] = gd_isset($getValue['goodsBenefitDiscountGroup']);
    //     $this->search['goodsBenefitPeriod'] = gd_isset($getValue['goodsBenefitPeriod']);
    //     $this->search['goodsBenefitNoneFl'] = gd_isset($getValue['goodsBenefitNoneFl']);

    //     $this->search['optionSellFl'] = gd_isset($getValue['optionSellFl']);
    //     $this->search['optionDeliveryFl'] = gd_isset($getValue['optionDeliveryFl']);

    //     $this->search['stockType'] = gd_isset($getValue['stockType']);
    //     $this->checked['daumFl'][$this->search['daumFl']] = $this->checked['naverFl'][$this->search['naverFl']] = $this->checked['paycoFl'][$this->search['paycoFl']] = $this->checked['purchaseNoneFl'][$this->search['purchaseNoneFl']] = $this->checked['stockStateFl'][$this->search['stockStateFl']] = $this->checked['addGoodsFl'][$this->search['addGoodsFl']] = $this->checked['applyType'][$this->search['applyType']] = $this->checked['applyFl'][$this->search['applyFl']] = $this->checked['goodsDeliveryFl'][$this->search['goodsDeliveryFl']] = $this->checked['categoryNoneFl'][$this->search['categoryNoneFl']] = $this->checked['brandNoneFl'][$this->search['brandNoneFl']] = $this->checked['scmFl'][$this->search['scmFl']] = $this->checked['optionFl'][$this->search['optionFl']] = $this->checked['mileageFl'][$this->search['mileageFl']] = $this->checked['optionTextFl'][$this->search['optionTextFl']] = $this->checked['goodsDisplayFl'][$this->search['goodsDisplayFl']] = $this->checked['goodsSellFl'][$this->search['goodsSellFl']] = $this->checked['goodsDisplayMobileFl'][$this->search['goodsDisplayMobileFl']] = $this->checked['goodsSellMobileFl'][$this->search['goodsSellMobileFl']] = $this->checked['stockFl'][$this->search['stockFl']] = $this->checked['soldOut'][$this->search['soldOut']] = $this->checked['goodsIconCdPeriod'][$this->search['goodsIconCdPeriod']] = $this->checked['goodsIconCd'][$this->search['goodsIconCd']] = $this->checked['deliveryFl'][$this->search['deliveryFl']] = $this->checked['deliveryFree'][$this->search['deliveryFree']] = $this->checked['goodsDisplayMobileFl'][$this->search['goodsDisplayMobileFl']] = $this->checked['goodsSellMobileFl'][$this->search['goodsSellMobileFl']] = $this->checked['mobileDescriptionFl'][$this->search['mobileDescriptionFl']] = $this->checked['restockFl'][$this->search['restockFl']] = $this->checked['goodsBenefitNoneFl'][$this->search['goodsBenefitNoneFl']] = $this->checked['sellStopFl'][$this->search['sellStopFl']] = $this->checked['confirmRequestFl'][$this->search['confirmRequestFl']] = $this->checked['optionSellFl'][$this->search['optionSellFl']] = $this->checked['optionDeliveryFl'][$this->search['optionDeliveryFl']] = 'checked="checked"';

    //     foreach ($this->search['goodsDeliveryFixFl'] as $k => $v) {
    //         $this->checked['goodsDeliveryFixFl'][$v] = 'checked="checked"';
    //     }

    //     $this->checked['searchPeriod'][$this->search['searchPeriod']] = "active";

    //     $this->selected['searchDateFl'][$this->search['searchDateFl']] = $this->selected['sort'][$this->search['sort']] = $this->selected['eventGroup'][$this->search['eventGroup']] = $this->selected['stockType'][$this->search['stockType']] = "selected='selected'";

    //     //삭제상품여부
    //     if ($this->search['delFl']) {
    //         $this->arrWhere[] = 'g.delFl = ?';
    //         $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['delFl'], $this->search['delFl']);
    //     }

    //     // 처리일자 검색
    //     if ($this->search['searchDateFl'] && $this->search['searchDate'][0] && $this->search['searchDate'][1] && $mode != 'layer') {
    //         if ($this->goodsDivisionFl === true && $this->search['searchDateFl'] == 'delDt') {
    //             $this->arrWhere[] = $this->search['searchDateFl'] . ' BETWEEN ? AND ?';
    //             $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['delDt'], $this->search['searchDate'][0] . ' 00:00:00');
    //             $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['delDt'], $this->search['searchDate'][1] . ' 23:59:59');
    //         } else {
    //             $this->arrWhere[] = 'g.' . $this->search['searchDateFl'] . ' BETWEEN ? AND ?';
    //             $this->db->bind_param_push($this->arrBind, 's', $this->search['searchDate'][0] . ' 00:00:00');
    //             $this->db->bind_param_push($this->arrBind, 's', $this->search['searchDate'][1] . ' 23:59:59');
    //         }
    //     }

    //     // 키워드 검색
        
    //     if ($this->search['key'] && $this->search['keyword']) {
    //         if ($this->search['key'] == 'all') {
    //             $tmpWhere = array('goodsNm', 'goodsNo', 'goodsCd', 'goodsSearchWord');
    //             $arrWhereAll = array();
    //             foreach ($tmpWhere as $keyNm) {
    //                 $arrWhereAll[] = '(g.' . $keyNm . ' LIKE concat(\'%\',?,\'%\'))';
    //                 $this->db->bind_param_push($this->arrBind, $fieldTypeGoods[$keyNm], $this->search['keyword']);
    //             }

    //             $this->arrWhere[] = '(' . implode(' OR ', $arrWhereAll) . ')';
    //             unset($tmpWhere);
    //         } else {

    //             if ($this->search['key'] == 'companyNm') {
    //                 $this->arrWhere[] = 's.' . $this->search['key'] . ' LIKE concat(\'%\',?,\'%\')';
    //                 $this->db->bind_param_push($this->arrBind, 's', $this->search['keyword']);
    //             } else if ($this->search['key'] == 'purchaseNm') {
    //                 $this->arrWhere[] = 'p.' . $this->search['key'] . ' LIKE concat(\'%\',?,\'%\')';
    //                 $this->db->bind_param_push($this->arrBind, 's', $this->search['keyword']);
    //             }
    //             else if($this->search['key'] == 'addInfo') {
    //             }
    //             else if($this->search['key'] == 'hscode'){
    //                 if($this->search['keyword']){
    //                     $this->arrWhere[] = " hscode != '' AND json_extract(hscode,'$.*') LIKE concat('%',?,'%')";
    //                     $this->db->bind_param_push($this->arrBind, 's', $this->search['keyword']);
    //                 }
    //             }
    //             else {
    //                 //추가
    //                 if(strpos(nl2br($this->search['keyword']),'<br />') !== false){
    //                     $addSearch = '';
    //                     $addSearch = explode("\n", $this->search['keyword']);
                        
    //                     $addWhere = '';
    //                     foreach($addSearch as $key => $value){
    //                         $value = trim($value);
    //                         if($key === 0){
    //                             $addWhere = ' AND g.' . $this->search['key'] . ' LIKE concat(\'%\',?,\'%\')';
    //                         }else{
    //                             $addWhere .= ' OR g.' . $this->search['key'] . ' LIKE concat(\'%\',?,\'%\')';
    //                         }
    //                         $this->db->bind_param_push($this->arrBind, $fieldTypeGoods[$this->search['key']], $value);
    //                     }
    //                     $this->arrWhere[0] .= $addWhere;
    //                 }else{
    //                     $this->arrWhere[] = 'g.' . $this->search['key'] . ' LIKE concat(\'%\',?,\'%\')';
    //                     $this->db->bind_param_push($this->arrBind, $fieldTypeGoods[$this->search['key']], $this->search['keyword']);
    //                 }
                    
    //             }

    //         }
    //     }

    //     // 카테고리 검색
    //     if ($this->search['cateGoods']) {
    //         $this->arrWhere[] = 'gl.cateCd = ?';
    //         $this->db->bind_param_push($this->arrBind, $fieldTypeLink['cateCd'], $this->search['cateGoods']);
    //         $this->arrWhere[] = 'gl.cateLinkFl = "y"';
    //     }

    //     //카테고리 미지정
    //     if ($this->search['categoryNoneFl']) {
    //         $this->arrWhere[] = 'g.cateCd  = ""';
    //     }


    //     // 브랜드 검색
    //     if (($this->search['brandCd'] && $this->search['brandCdNm']) || $this->search['brand']) {
    //         if (!$this->search['brandCd'] && $this->search['brand'])
    //             $this->search['brandCd'] = $this->search['brand'];
    //         if($this->search['brandNoneFl']) { // 선택값 있고 미지정일 때
    //             $this->arrWhere[] = '(g.brandCd != ? OR (g.brandCd  = "" or g.brandCd IS NULL))';
    //         } else {
    //             $this->arrWhere[] = 'g.brandCd = ?';
    //         }
    //         $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['brandCd'], $this->search['brandCd']);
    //     } else if (((!$this->search['brandCd'] && !$this->search['brandCdNm']) || !$this->search['brand']) && $this->search['brandNoneFl']) { //브랜드 미지정
    //         $this->arrWhere[] = '(g.brandCd  = "" or g.brandCd IS NULL)';
    //     } else {
    //         $this->search['brandCd'] = '';
    //     }


    //     // 메인상품진열 상품 검색 Sno
    //     if ($this->search['displayTheme']) {
    //         // 메인분류 1,2차에 따른 조건 생성 함수
    //         $strDisplayThemeWhere = $this->searchGoodsListDisplayThemeData($this->search['displayTheme'][0], $this->search['displayTheme'][1]);
    //         if($strDisplayThemeWhere) {
    //             $this->arrWhere[] = implode(' AND ', $strDisplayThemeWhere);
    //         }
    //     }

    //     // 매입처 검색
    //     if (($this->search['purchaseNo'] && $this->search['purchaseNoNm'])) {
    //         if (is_array($this->search['purchaseNo'])) {
    //             foreach ($this->search['purchaseNo'] as $val) {
    //                 if($this->search['purchaseNoneFl']) { // 선택값 있고 미지정일 때
    //                     $tmpWhere[] = 'g.purchaseNo != ?';
    //                 } else {
    //                     $tmpWhere[] = 'g.purchaseNo = ?';
    //                 }
    //                 $this->db->bind_param_push($this->arrBind, 's', $val);
    //             }
    //             if($this->search['purchaseNoneFl']) { // 선택값 있고 미지정일 때
    //                 $this->arrWhere[] = '((' . implode(' AND ', $tmpWhere) . ') OR (g.purchaseNo IS NULL OR g.purchaseNo  = "" OR g.purchaseNo  <= 0 OR p.delFl = "y"))';
    //             } else {
    //                 $this->arrWhere[] = '(' . implode(' OR ', $tmpWhere) . ')';
    //             }
    //             unset($tmpWhere);
    //         }
    //     }
    //     // 매입처 미지정 (선택 값 없고 미적용상품일 때)
    //     else if ((!$this->search['purchaseNo'] && !$this->search['purchaseNoNm']) && $this->search['purchaseNoneFl']) {
    //         $this->arrWhere[] = '(g.purchaseNo IS NULL OR g.purchaseNo  = "" OR g.purchaseNo  <= 0 OR p.delFl = "y")';
    //     }

    //     //추가상품 사용
    //     if ($this->search['addGoodsFl']) {
    //         $this->arrWhere[] = 'g.addGoodsFl = ?';
    //         $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['addGoodsFl'], $this->search['addGoodsFl']);
    //     }

    //     // 상품가격 검색
    //     if ($this->search['goodsPrice'][0] || $this->search['goodsPrice'][1]) {

    //         if($this->search['goodsPrice'][0]) {
    //             $this->arrWhere[] = 'g.goodsPrice >= ? ';
    //             $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['goodsPrice'], $this->search['goodsPrice'][0]);
    //         }

    //         if($this->search['goodsPrice'][1]) {
    //             $this->arrWhere[] = 'g.goodsPrice <= ? ';
    //             $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['goodsPrice'], $this->search['goodsPrice'][1]);
    //         }
    //     }

    //     // 마일리지 검색
    //     if ($this->search['mileage'][0] || $this->search['mileage'][1]) {

    //         $mileage = gd_policy('member.mileageGive')['goods'];

    //         if($this->search['mileage'][0]) {
    //             $this->arrWhere[] = 'if( g.mileageFl ="c", '.$mileage.',  g.mileageGoods ) >= ? ';
    //             $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['mileageGoods'], $this->search['mileage'][0]);
    //         }

    //         if($this->search['mileage'][1]) {
    //             $this->arrWhere[] = 'if( g.mileageFl ="c", '.$mileage.',  g.mileageGoods ) <= ? ';
    //             $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['mileageGoods'], $this->search['mileage'][1]);
    //         }
    //     }

    //     // 재고검색
    //     if ($this->search['stock'][0] || $this->search['stock'][1]) {

    //         $tmpStockType = $this->search['stockType'] == 'option' ? 'go.stockCnt' : 'g.totalStock';
    //         if($this->search['stock'][0]) {
    //             $this->arrWhere[] = $tmpStockType . ' >= ? ';
    //             $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['totalStock'], $this->search['stock'][0]);
    //         }

    //         if($this->search['stock'][1]) {
    //             $this->arrWhere[] = $tmpStockType . ' <= ? ';
    //             $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['totalStock'], $this->search['stock'][1]);
    //         }
    //     }

    //     // 옵션 사용 여부 검색
    //     if ($this->search['optionFl']) {
    //         $this->arrWhere[] = 'g.optionFl = ?';
    //         $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['optionFl'], $this->search['optionFl']);
    //     }
    //     // 마일리지 정책 검색
    //     if ($this->search['mileageFl']) {
    //         $this->arrWhere[] = 'g.mileageFl = ?';
    //         $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['mileageFl'], $this->search['mileageFl']);
    //     }
    //     // 텍스트옵션 사용 여부 검색
    //     if ($this->search['optionTextFl']) {
    //         $this->arrWhere[] = 'g.optionTextFl = ?';
    //         $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['optionTextFl'], $this->search['optionTextFl']);
    //     }
    //     // 상품 출력 여부 검색
    //     if ($this->search['goodsDisplayFl']) {
    //         $this->arrWhere[] = 'g.goodsDisplayFl = ?';
    //         $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['goodsDisplayFl'], $this->search['goodsDisplayFl']);
    //     }
    //     // 상품 판매 여부 검색
    //     if ($this->search['goodsSellFl']) {
    //         $this->arrWhere[] = 'g.goodsSellFl = ?';
    //         $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['goodsSellFl'], $this->search['goodsSellFl']);
    //     }
    //     // 무한정 판매 여부 검색
    //     if ($this->search['stockFl']) {
    //         $this->arrWhere[] = 'g.stockFl = ?';
    //         $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['stockFl'], $this->search['stockFl']);
    //     }
    //     if ($this->search['stockStateFl'] != 'all') {
    //         switch ($this->search['stockStateFl']) {
    //             case 'n': {
    //                 $this->arrWhere[] = 'g.stockFl = ?';
    //                 $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['stockFl'], 'n');
    //                 break;
    //             }
    //             case 'u' : {
    //                 $this->arrWhere[] = '(g.stockFl = ? and g.totalStock > 0)';
    //                 $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['stockFl'], 'y');
    //                 break;
    //             }
    //             case 'z' : {
    //                 $this->arrWhere[] = '(g.stockFl = ? and g.totalStock = 0)';
    //                 $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['stockFl'], 'y');
    //                 break;
    //             }

    //         }
    //     }

    //     // 품절상품 여부 검색
    //     if ($this->search['soldOut']) {
    //         if ($this->search['soldOut'] == 'y') {
    //             $this->arrWhere[] = '( g.soldOutFl = \'y\' OR (g.stockFl = \'y\' AND g.totalStock <= 0 ))';
    //         }
    //         if ($this->search['soldOut'] == 'n') {
    //             $this->arrWhere[] = '( g.soldOutFl = \'n\' AND (g.stockFl = \'n\' OR (g.stockFl = \'y\' AND g.totalStock > 0)) )';
    //         }
    //     }

    //     // 판매중지수량 여부 검색
    //     if ($this->search['sellStopFl']) {
    //         if ($this->search['sellStopFl'] == 'y') {
    //             $this->arrWhere[] = 'go.sellStopFl = \'y\'';
    //         }
    //         if ($this->search['sellStopFl'] == 'n') {
    //             $this->arrWhere[] = 'go.sellStopFl = \'n\'';
    //         }
    //     }

    //     // 확인요청수량 여부 검색
    //     if ($this->search['confirmRequestFl']) {
    //         if ($this->search['confirmRequestFl'] == 'y') {
    //             $this->arrWhere[] = 'go.confirmRequestFl = \'y\'';
    //         }
    //         if ($this->search['confirmRequestFl'] == 'n') {
    //             $this->arrWhere[] = 'go.confirmRequestFl = \'n\'';
    //         }
    //     }

    //     // 옵션품절상태 여부 검색
    //     if ($this->search['optionSellFl']) {
    //         if ($this->search['optionSellFl'] == 'y' || $this->search['optionSellFl'] == 'n') {
    //             $this->arrWhere[] = 'go.optionSellFl = \''.$this->search['optionSellFl'].'\'';
    //         }
    //         else  if ($this->search['optionSellFl'] != '') {
    //             $this->arrWhere[] = 'go.optionSellFl = \'t\'';
    //             $this->arrWhere[] = 'go.optionSellCode = \''.$this->search['optionSellFl'].'\'';
    //         }
    //     }

    //     // 옵션품절상태 여부 검색
    //     if ($this->search['optionDeliveryFl']) {
    //         if ($this->search['optionDeliveryFl'] == 'normal') {
    //             $this->arrWhere[] = 'go.optionDeliveryFl = \''.$this->search['optionDeliveryFl'].'\'';
    //         }
    //         else  if ($this->search['optionDeliveryFl'] != '') {
    //             $this->arrWhere[] = 'go.optionDeliveryFl = \'t\'';
    //             $this->arrWhere[] = 'go.optionDeliveryCode = \''.$this->search['optionDeliveryFl'].'\'';
    //         }
    //     }

    //     //상품 헤택 모듈
    //     $goodsBenefit = \App::load('\\Component\\Goods\\GoodsBenefit');
    //     $goodsBenefitUse = $goodsBenefit->getConfig();

    //     if ($list_type == 'excel') {
    //         $tmpIcon = [];
    //         //기간제한 아이콘
    //         if ($this->search['goodsIconCdPeriod']) {
    //             $tmpIcon[] = '(gi.goodsIconCd = ? AND gi.iconKind = \'pe\')';
    //             $this->db->bind_param_push($this->arrBind, 's', $this->search['goodsIconCdPeriod']);
    //         }

    //         //무제한 아이콘
    //         if ($this->search['goodsIconCd']) {
    //             if($goodsBenefitUse == 'y'){
    //                 $tmpIcon[] = '(gi.goodsIconCd = ? AND gi.iconKind = \'un\' OR gi.goodsIconCd = ? AND gi.iconKind = \'pr\')';
    //                 $this->db->bind_param_push($this->arrBind, 's', $this->search['goodsIconCd']);
    //                 $this->db->bind_param_push($this->arrBind, 's', $this->search['goodsIconCd']);
    //             }else{
    //                 $tmpIcon[] = '(gi.goodsIconCd = ? AND gi.iconKind = \'un\' )';
    //                 $this->db->bind_param_push($this->arrBind, 's', $this->search['goodsIconCd']);
    //             }
    //         }

    //         if (count($tmpIcon) > 0) {
    //             $this->arrWhere[] = '(' . implode(' OR ', $tmpIcon) . ')';
    //             unset($tmpIcon);
    //         }
    //     }

    //     // 아이콘(무제한) 여부 검색
    //     if ($this->search['goodsColor']) {
    //         $tmp = [];
    //         foreach ($this->search['goodsColor'] as $k => $v) {
    //             $tmp[] = 'g.goodsColor LIKE concat(\'%\',?,\'%\')';
    //             $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['goodsColor'], $v);
    //         }
    //         $this->arrWhere[] = '(' . implode(" OR ", $tmp) . ')';
    //     }

    //     // 모바일 상품 출력 여부 검색
    //     if ($this->search['goodsDisplayMobileFl']) {
    //         $this->arrWhere[] = 'g.goodsDisplayMobileFl = ?';
    //         $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['goodsDisplayMobileFl'], $this->search['goodsDisplayMobileFl']);
    //     }

    //     // 모바일 상품 판매 여부 검색
    //     if ($this->search['goodsSellMobileFl']) {
    //         $this->arrWhere[] = 'g.goodsSellMobileFl = ?';
    //         $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['goodsSellMobileFl'], $this->search['goodsSellMobileFl']);
    //     }


    //     // 모바일 상세 설명 여부 검색
    //     if ($this->search['mobileDescriptionFl'] == 'y') {
    //         $this->arrWhere[] = 'g.goodsDescriptionMobile != \'\' AND g.goodsDescriptionMobile IS NOT NULL';
    //     } else if ($this->search['mobileDescriptionFl'] == 'n') {
    //         $this->arrWhere[] = '(g.goodsDescriptionMobile = \'\' OR g.goodsDescriptionMobile IS NULL)';
    //     }
    //     //공급사
    //     if ($this->search['scmFl'] != 'all') {
    //         if (is_array($this->search['scmNo'])) {
    //             foreach ($this->search['scmNo'] as $val) {
    //                 $tmpWhere[] = 'g.scmNo = ?';
    //                 $this->db->bind_param_push($this->arrBind, 's', $val);
    //             }
    //             $this->arrWhere[] = '(' . implode(' OR ', $tmpWhere) . ')';
    //             unset($tmpWhere);
    //         } else {
    //             $this->arrWhere[] = 'g.scmNo = ?';
    //             $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['scmNo'], $this->search['scmNo']);

    //             $this->search['scmNo'] = array($this->search['scmNo']);
    //             $this->search['scmNoNm'] = array($this->search['scmNoNm']);

    //         }
    //     }

    //     //승인구분
    //     if ($this->search['applyType'] != 'all') {
    //         $this->arrWhere[] = 'g.applyType = ?';
    //         $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['applyType'], $this->search['applyType']);
    //     }

    //     //승인상태
    //     if ($this->search['applyFl'] != 'all') {
    //         $this->arrWhere[] = 'g.applyFl = ?';
    //         $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['applyFl'], $this->search['applyFl']);
    //     }

    //     //배송관련
    //     $tmpFixFl = array_flip($this->search['goodsDeliveryFixFl']);
    //     unset($tmpFixFl['all']);
    //     if (count($tmpFixFl) || $this->search['goodsDeliveryFl']) {
    //         $delivery = \App::load('\\Component\\Delivery\\Delivery');
    //         $deliveryData = $delivery->getDeliveryGoods($this->search);

    //         if (is_array($deliveryData)) {
    //             foreach ($deliveryData as $val) {
    //                 $tmpWhere[] = 'g.deliverySno = ?';
    //                 $this->db->bind_param_push($this->arrBind, 's', $val['sno']);
    //             }
    //             $this->arrWhere[] = '(' . implode(' OR ', $tmpWhere) . ')';
    //             unset($tmpWhere);
    //         }
    //     }

    //     // 네이버쇼핑상품 출력 여부 검색
    //     if ($this->search['naverFl']) {
    //         $this->arrWhere[] = 'g.naverFl = ?';
    //         $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['naverFl'], $this->search['naverFl']);
    //     }

    //     // 페이코쇼핑상품 출력 여부 검색
    //     if ($this->search['paycoFl']) {
    //         $this->arrWhere[] = 'g.paycoFl = ?';
    //         $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['paycoFl'], $this->search['paycoFl']);
    //     }

    //     // 다음쇼핑하우상품 출력 여부 검색
    //     if ($this->search['daumFl']) {
    //         $this->arrWhere[] = 'g.daumFl = ?';
    //         $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['daumFl'], $this->search['daumFl']);
    //     }

    //     //기획전 검색
    //     if ($this->search['eventThemeSno']) {
    //         $eventGoodsNoArray = array();
    //         $eventThemeData = $this->getDisplayThemeInfo($this->search['eventThemeSno']);
    //         if($eventThemeData['displayCategory'] === 'g'){
    //             //그룹형인경우

    //             $eventGroupTheme = \App::load('\\Component\\Promotion\\EventGroupTheme');
    //             $eventGroupData = $eventGroupTheme->getSimpleData($this->search['eventThemeSno']);
    //             $this->search['eventGroupSelectList'] = $eventGroupData;
    //             foreach($eventGroupData as $key => $eventGroupArr){
    //                 if((int)$eventGroupArr['sno'] === (int)$this->search['eventGroup']){
    //                     $eventGoodsNoArray = @explode(STR_DIVISION, $eventGroupArr['groupGoodsNo']);
    //                     break;
    //                 }
    //             }
    //         }
    //         else {
    //             //일반형인경우
    //             $eventGoodsNoArray = @explode(INT_DIVISION, $eventThemeData['goodsNo']);
    //         }

    //         if(count($eventGoodsNoArray) > 0){
    //             $this->arrWhere[] = "(g.goodsNo IN ('" . implode("',' ", $eventGoodsNoArray) . "'))";
    //         }
    //         unset($eventGoodsNoArray);
    //     }

    //     // 재입고 알림 사용 여부
    //     if ($this->search['restockFl'] != 'all') {
    //         $this->arrWhere[] = 'g.restockFl = ?';
    //         $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['restockFl'], $this->search['restockFl']);
    //     }

    //     // 상품 혜택 검색
    //     if ($this->search['goodsBenefitSno']) {
    //         if (is_array($this->search['goodsBenefitSno'])) { // 배열일 경우
    //             foreach ($this->search['goodsBenefitSno'] as $val) {
    //                 if ($this->search['goodsBenefitNoneFl'] == 'y') { // 선택한 상품 미지정
    //                     $tmpWhere[] = 'gbl.benefitSno != ?';
    //                 } else {
    //                     $tmpWhere[] = 'gbl.benefitSno = ?';
    //                 }
    //                 $this->db->bind_param_push($this->arrBind, 's', $val);
    //             }
    //             if ($this->search['goodsBenefitNoneFl'] == 'y') {
    //                 if($getValue['applyPath'] == '/goods/goods_batch_mileage.php' || $getValue['applyPath'] == '/goods/goods_batch_link.php' || $getValue['applyPath'] == '/goods/goods_batch_stock.php') {
    //                     $this->arrWhere[] = '((' . implode(' AND ', $tmpWhere) . ') OR ( gbl.benefitSno = \'\' OR gbl.benefitSno IS NULL)) ';
    //                 } else {
    //                     $this->arrWhere[] = '((' . implode(' AND ', $tmpWhere) . ') OR ( gbl.benefitSno = \'\' OR gbl.benefitSno IS NULL)) GROUP BY g.goodsNo ';
    //                 }
    //             } else {
    //                 $this->arrWhere[] = '(' . implode(' OR ', $tmpWhere) . ')';
    //             }
    //             unset($tmpWhere);
    //         } else { // 기존 단일선택 레거시 보장
    //             $this->arrWhere[] = 'gbl.benefitSno = ?';
    //             $this->db->bind_param_push($this->arrBind, 'i', $this->search['goodsBenefitSno']);
    //         }
    //     } else if(!$this->search['goodsBenefitSno'] && $this->search['goodsBenefitNoneFl'] == 'y') { // 혜택 선택 값 없고 미적용상품일 때
    //         $this->arrWhere[] = '(gbl.benefitSno  = "" or gbl.benefitSno IS NULL)';
    //     }

    //     if (empty($this->arrBind)) {
    //         $this->arrBind = null;
    //     }
    // }
}