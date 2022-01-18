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
 * @link http://www.godo.co.kr
 */
namespace Controller\Front\Board;

use Component\Wib\WibSql;
use Request;

class ListController extends \Bundle\Controller\Front\Board\ListController
{
    public function index()
    {
        parent::index();
        
        $req = Request::get()->toArray();
        
        if($req['bdId'] == 'store'){
            
            $bdList = $this->getData('bdList');
            
            $bdList = $this->reGetBdList($bdList,$req);
            
            $listCount = ceil(count($bdList['list'])/5);
            if($listCount == 0){
                $listCount = 1;
            }
            
            $this->setData('listCount',$listCount);
            $this->setData('bdList',$bdList);
        }
        
    }
    
    public function reGetBdList($bdList,$req)
    {
        $wibSql = new WibSql();
        
        foreach ($bdList['list'] as $key => $value){
                
            //db 추가정보
            $query = "SELECT storeSearch, storePhoneNum, storeOpenTime, storeHoliday, address, addressSub, addressLat, addressLng FROM es_bd_store WHERE sno = {$value['sno']} LIMIT 999";
            $result = $wibSql->WibNobind($query);

            if($result){

                $bdList['list'][$key]['storeSearch'] = $result['storeSearch'];
                $bdList['list'][$key]['storePhoneNum'] = $result['storePhoneNum'];
                $bdList['list'][$key]['storeOpenTime'] = $result['storeOpenTime'];
                $bdList['list'][$key]['storeHoliday'] = $result['storeHoliday'];
                $bdList['list'][$key]['address'] = $result['address'];
                $bdList['list'][$key]['addressSub'] = $result['addressSub'];
                $bdList['list'][$key]['addressLat'] = $result['addressLat'];
                $bdList['list'][$key]['addressLng'] = $result['addressLng'];
            }


            $kakaoContents = $result["address"];


            $bdList['list'][$key]['wibkakao'] = $kakaoContents;


            $saveFileNm = explode('^|^', $value['saveFileNm']);

            if($saveFileNm[0]){

                $arr = [];
                //이미지 여러개 뿌리기
                foreach ($saveFileNm as $k => $val){
                    $arr[$k]['imgList'] = '/data/board/'.$value['bdUploadPath'].$val;
                }

                $bdList['list'][$key]['saveFileNmList'] = $arr;
            }
            
            //검색관련
            if($req['storeSearch'] && $req['searchWord']){
                
                if($result['storeSearch'] != $req['storeSearch']){
                    unset($bdList['list'][$key]);
            
                    
                }else if(strpos($bdList['list'][$key]['subject'],$req['searchWord']) === false && strpos($bdList['list'][$key]['address'],$req['searchWord']) === false && strpos($bdList['list'][$key]['addressSub'],$req['searchWord']) === false){

                    unset($bdList['list'][$key]);
                }
                    
                    
                
            }else if($req['storeSearch'] && !($req['searchWord'])){
                   
                if($req['storeSearch'] != $result['storeSearch']){
                    unset($bdList['list'][$key]);
                }
            }else if(!($req['storeSearch']) && $req['searchWord']){
                if(strpos($bdList['list'][$key]['subject'],$req['searchWord']) === false && strpos($bdList['list'][$key]['address'],$req['searchWord']) === false && strpos($bdList['list'][$key]['addressSub'],$req['searchWord']) === false){
                    unset($bdList['list'][$key]);
                }
            }

        }
        return $bdList;
    }
}