<?php

namespace Component\Promotion;

use Framework\Security\XXTEA;
use Framework\Debug\Exception\AlertBackException;
use Exception;
use Component\Database\DBTableField;
use App;
use Session;
use Logger;

class Insgov2Widget extends \Bundle\Component\Promotion\Insgov2Widget
{
      /**
     * 인스고 위젯 미리보기(관리자, 프론트)
     *
     * @param $postData
     * @param $insgoSno
     * @param bool $isCache
     * @return mixed
     * @throws Exception
     */
    public function getInsgoWidgetData($postData,$insgoSno,$isCache = true)
    {
        if (is_array($postData)) {
            $dataArray = $postData;
        } else {
            $dataArray = $this->getUriHash($postData);
        }
        
        // 인스고 통신 정보 세팅
        $apiPostData['fields'] = 'id,caption,media_type,media_url,thumbnail_url,permalink,timestamp';
        $apiPostData['access_token'] = $dataArray['widgetAccessToken'];
        $apiPostData['count'] = $this->getParameter($dataArray)['count'];
        $apiUrl = $this->getInsgoWidgetApiUrl($apiPostData);
        $responseData = $this->getInsogoData($apiUrl, 'n', $dataArray, $insgoSno, $isCache);
        // echo '<!--';
        // print_r($responseData);
        // echo '-->';
        // 통신완료 후, 미디어 데이터 가공
        $data['displayType'] = $dataArray['widgetDisplayType'];
        if($responseData['data']){
            foreach ($responseData['data'] as $k => $v) {
                $data['thumbnails'][$k]['image'] = $v['images'];
                $data['thumbnails'][$k]['viewUrl'] = $v['link'];
            }
            $data['data'] = $dataArray;
        }else{
            $data['error'] = $responseData['error'];
        }

        return $data;
    }

	protected  function getCurlOutput($curlUrl,$postType,$postData)
    {
        //print_r($curlUrl);
        //print_r($postType);
        //print_r($postData);
        Logger::channel('insgo')->info(__METHOD__ . ' Insgov2Widget CURL Start URL: ' . $curlUrl . ' Data: ', [$postData]);

        // Curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $curlUrl);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        $decodeResponse = json_decode($result, true);
        // print_r($decodeResponse);
		// echo '<!--';
        // print_r($decodeResponse);
        // echo '-->';
        if($decodeResponse['error']){
            $data['error'] = $decodeResponse['error'];
        }else {
            if(empty($decodeResponse['data']) === true){
                $data['error']['msg'] = 'empty InsgoData';
                $data['error']['code'] = '000';
                Logger::channel('insgo')->info(__METHOD__ . ' Insgov2Widget CURL Connection Success, Data empty: ' . $curlUrl . ' Data: ', [$data]);
            }else{
                Logger::channel('insgo')->info(__METHOD__ . ' Insgov2Widget CURL Connection Success, Data Not empty: ' . $curlUrl);
                $arrData['data'] = $decodeResponse['data'];
                
                foreach($arrData['data'] as $key => $val){
                    echo '<!--';
                    print_r($arrData['data']);
                    echo '-->';
                    if($val['media_type'] == 'VIDEO'){
                        $data['data'][$key]['images']['thumbnail']['url'] = $val['thumbnail_url'];
                        $data['data'][$key]['images']['low_resolution']['url'] = $val['thumbnail_url'];
                        $data['data'][$key]['images']['standard_resolution']['url'] = $val['thumbnail_url'];
                    }else{
                        $data['data'][$key]['images']['thumbnail']['url'] = $val['media_url'];
                        $data['data'][$key]['images']['low_resolution']['url'] = $val['media_url'];
                        $data['data'][$key]['images']['standard_resolution']['url'] = $val['media_url'];
                    }
                    $data['data'][$key]['link'] = $val['permalink'];
                }
            }
        }
        //print_r($decodeResponse);
        //print_r($result);exit;
        //print_r($arrData['data']);exit;
		//print_r($result);
        return $data;
    }

    /**
     * 인스고 API url세팅
     *
     * @param $param
     * @return string
     */
    protected function getInsgoWidgetApiUrl($param)
    {
        $retUrl = $this->_insgoMediaAPI . http_build_query($param);
        //print_r($retUrl);exit;
        return $retUrl;
    }

     /**
     * 인스고 위젯 관리 데이터(insgo_widget_config.php, 미리보기, 프론트)
     *
     * @param null $sno
     * @param null $arrInclude
     * @param bool $returnArray
     * @param bool $tuning true: config.php, false: 미리보기&프론트
     * @return mixed
     */
    public function getData($sno = null, $tuning = false)
    {
        // 인스고 설정값
        $insgoConfig = gd_policy('promotion.insgo');
        //print_r($insgoConfig);

        // 인스고 설정값 복호화
        $arrData = $this->getUriHash($insgoConfig['insgoData']);
        //print_r($arrData);
        // 인스고위젯관리 데이터
        if ($tuning) { // 인스고위젯관리(관리자)
            foreach ($arrData as $key => $val) {
                $newKey = str_replace('insgo', 'widget', $key);
                $tmpData[$newKey] = $val;
            }
            $data = $this->dataTuning($tmpData);
        }else{ // 인스고 미리보기(관리자), 프론트
            $data = $arrData;
        }
        // 공통 정보
        $data['displayType'] = $arrData['widgetDisplayType'];   // 위젯 타입
        $data['thumbnailSize'] = $arrData['widgetThumbnailSize'];   // 썸네일 사이즈
        $data['thumbnailBorder'] = $arrData['widgetThumbnailBorder'];   // 이미지 테두리
        $data['thumbnailSizePx'] = $arrData['widgetThumbnailSizePx'];   // 썸네일 사이즈가 수동인 경우 px값
        $data['overEffect'] = $arrData['widgetOverEffect']; // 마우스 오버 시 효과

        // 위젯타입 - 스크롤
        $data['width'] = $arrData['widgetWidth']; // 위젯 가로사이즈
        $data['autoScroll'] = $arrData['widgetAutoScroll']; // 자동스크롤
        $data['sideButtonColor'] = $arrData['widgetSideButtonColor']; // 좌우 전환 버튼 색상값
        $data['scrollSpeed'] = $arrData['widgetScrollSpeed'];   // 전환속도 선택

        // 위젯타입 - 슬라이드
        $data['scrollTime'] = $arrData['widgetScrollTime']; // 전환시간 선택
        $data['effect'] = $arrData['widgetEffect']; // 효과 선택

        $data['widgetSno'] = $insgoConfig['sno'];
        $data['widgetAccessToken'] = $insgoConfig['accessToken'];
        //print_r($data);
        return $data;
    }
}