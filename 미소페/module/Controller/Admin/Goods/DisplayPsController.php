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
namespace Controller\Admin\Goods;

use Framework\Debug\Exception\LayerException;
use Framework\Debug\Exception\AlertCloseException;
use Message;
use Request;
use Framework\Utility\ArrayUtils;
use Component\Wib\WibSql;
use Component\Storage\Storage;
use Component\Board\Board;

class DisplayPsController extends \Bundle\Controller\Admin\Goods\DisplayPsController
{
    protected $storage;
    
    public function index()
    {
        $postValue = Request::post()->toArray();
        $fileData = Request::files()->toArray();
        
        $event_thumbnail = '';
        
        $sno = $postValue['sno'];
        
        $wibSql = new WibSql();
        
        //추가 db에는 이미 event_thumbnail값이 있는데 그냥 아무것도 안하고 저장만 누르면 db값이 초기화돼서 다시 넣어주기용
        if($postValue['mode'] == 'main_register' || $postValue['mode'] == 'main_modify'){
            
            $fileData = Request::files()->toArray();
            
            if(!$sno){
                $query = "SELECT * FROM es_displayTheme ORDER BY sno DESC LIMIT 1";
                $res = $wibSql->WibNobind($query);
                $sno = $res['sno'];
            }
            
            $query = "SELECT * FROM es_displayTheme WHERE sno = {$sno}";
            $result = $wibSql->WibNobind($query);
            
            if(!($fileData['event_thumbnail']['name']) && $result['event_thumbnail']){
                $event_thumbnail = $result['event_thumbnail'];
            }
            
        }
        
        // --- 상품노출 class
        $goods = \App::load('\\Component\\Goods\\GoodsAdmin');

        try {

            switch ($postValue['mode']) {
                // 테마 등록 / 수정
                // 메인 상품 진열 및 테마 설정 등록 / 수정
                case 'main_register':
                case 'main_modify':

                    $goods->saveInfoDisplayTheme($postValue);
                    
                    //추가
                    if($event_thumbnail){
                        
                        $wql = [
                            'es_displayTheme',
                            array('event_thumbnail'=>[$event_thumbnail,'s']),
                            array('sno'=>[$sno,'i'])
                        ];

                        $wibSql->WibUpdate($wql);
                    }
                    
                    
                    $this->layer(__('저장이 완료되었습니다.'));

                    break;

                // 메인 상품 진열 및 테마 설정 삭제
                case 'main_delete':

                    if (empty($postValue['sno']) === false) {
                        foreach ($postValue['sno'] as $sno) {
                            $goods->setDeleteDisplayTheme($sno,$postValue['themeCd'][$sno]);
                        }
                    }

                    unset($postArray);

                    $this->layer(__('삭제 되었습니다.'));

                    break;

                // 메인 상품 진열 및 테마 설정 삭제
                case 'search_register':

                    //검색어 기본 진열
                    gd_set_policy('search.goods', $postValue['goods']);
                    $goods->setRefreshSearchThemeConfig($postValue['goods']['pcThemeCd'],$postValue['goods']['mobileThemeCd']);

                    $this->layer(__('저장이 완료되었습니다.'));

                    break;
                case 'soldout_register':

                        $goods->saveInfoDisplaySoldOut($postValue);


                        $this->layer(__('저장이 완료되었습니다.'));

                    break;
                case 'search_theme':
                    $data = $goods->getJsonListDisplayTheme($postValue['mobileFl'],$postValue['themeCd']);

                    echo $data;
                    exit;

                    break;
                case 'search_goods':

                    $sno = explode(INT_DIVISION,$postValue['sno']);
                    $tmpArr = [];
                    // 자동 진열 일 경우 alert message
                    $alertMessage = '수동진열된 메인분류의 진열상품만 불러올 수 있습니다.';
                    foreach($sno as $k => $v) {
                        $getData = $goods->getDataDisplayTheme($v);
                        // 수동 진열이 하나라도 있을 경우 alert message
                        if ($getData['data']['sortAutoFl'] == 'n') {
                            $alertMessage = '해당 분류에 진열된 상품이 없습니다.';
                        }
                        if($getData['data']['goodsNo']) $tmpArr = array_merge($tmpArr, $getData['data']['goodsNo']);
                    }

                    if (count(gd_isset($tmpArr))) {

                        foreach ($tmpArr as $k => $goodsNoData) {
                            if($goodsNoData) {

                                foreach($goodsNoData as $key => $val) {
                                    $tmpData['goodsNm'] = strip_tags($val['goodsNm']);
                                    $tmpData['goodsPrice'] = gd_currency_display($val['goodsPrice']) ;
                                    $tmpData['scmNm'] = $val['scmNm'];
                                    $tmpData['totalStock'] = $val['totalStock'];
                                    $tmpData['image'] = rawurlencode(gd_html_goods_image($val['goodsNo'], $val['imageName'], $val['imagePath'], $val['imageStorage'], 30, $val['goodsNm'], '_blank'));
                                    $tmpData['brandNm'] = $val['brandNm'];
                                    $tmpData['makerNm'] = $val['makerNm'];
                                    $tmpData['soldOutFl'] = $val['soldOutFl'];
                                    $tmpData['stockFl'] = $val['stockFl'];
                                    $tmpData['goodsNo'] = $val['goodsNo'];
                                    $tmpData['goodsIcon'] = rawurlencode($val['goodsIcon']);
                                    $tmpData['goodsDisplayFl'] = $val['goodsDisplayFl'];
                                    $tmpData['goodsDisplayMobileFl'] = $val['goodsDisplayMobileFl'];


                                    $arrData[] = $tmpData;
                                }
                            }

                        }
                    }

                    //상품이 있을 경우 alert message 초기화
                    if (empty($arrData) === false) {
                        $alertMessage = '';
                    }

                    echo json_encode(array("state"=>true,"info"=>$arrData,"alertMessage"=>$alertMessage));
                    exit;
                    break;

                    //기획전 리스트 관련설정 저장
                case 'event_config' :
                    try {
                        $policy = \App::load('\\Component\\Policy\\Policy');

                        $policy->saveEventConfig($postValue);
                        $this->layer(__('저장이 완료되었습니다.'));
                    } catch (Exception $e) {
                        if (Request::isAjax()) {
                            throw $e;
                        } else {
                            throw new LayerException($e->getMessage(), $e->getCode(), $e);
                        }
                    }
                    break;

                    //기획전 그룹형 그룹 임시 등록
                case 'event_group_register' :
                    $eventGroupTheme = \App::load('\\Component\\Promotion\\EventGroupTheme');
                    $tmpNo = $eventGroupTheme->registEventGroupThemeTmp($postValue);

                    $this->layer(__('기획전에 반영 중 입니다.'), 'parent.setEventGroupThemeLayout("event_group_register", "'.$tmpNo.'");');
                    break;

                    //기획전 그룹형 그룹 수정
                case 'event_group_modify' :
                    $eventGroupTheme = \App::load('\\Component\\Promotion\\EventGroupTheme');
                    $eventGroupNo = $eventGroupTheme->modifyEventGroupThemeAll($postValue);

                    $this->layer(__('기획전에 반영 중 입니다.'), 'parent.setEventGroupThemeLayout("event_group_modify", "'.$eventGroupNo.'");');
                    break;

                    //기획전 그룹형 그룹 복사
                case 'event_group_copy' :
                    $eventGroupTheme = \App::load('\\Component\\Promotion\\EventGroupTheme');
                    $insertID = $eventGroupTheme->copyEventGroupThemeAll($postValue);

                    echo $insertID;
                    break;

                    //기획전 그룹 불러오기 적용
                case 'event_group_load' :
                    $eventGroupTheme = \App::load('\\Component\\Promotion\\EventGroupTheme');

                    $returnData = $eventGroupTheme->loadEventGroup($postValue);

                    echo json_encode($returnData);
                    break;

                    // 메인상품진열 변경/추가(팝업)
                case 'popup_display_change' :
                case 'popup_display_add' :
                    $goods->saveInfoPopupDisplay($postValue);
                    break;

                    // 메인상품진열 삭제(팝업)
                case 'popup_display_delete' :
                    if (empty($postValue['sno']) === false) {
                        foreach ($postValue['sno'] as $sno) {
                            $goods->modifyGoodsNoPopupDisplay($sno, $postValue);
                        }
                    }
                    break;
            }

        } catch (\Exception $e) {
            throw new LayerException($e->getMessage());
        }
        
    }
}