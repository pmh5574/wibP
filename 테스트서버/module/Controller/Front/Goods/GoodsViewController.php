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
namespace Controller\Front\Goods;

use Component\Board\Board;
use Component\Board\BoardBuildQuery;
use Component\Board\BoardList;
use Component\Board\BoardWrite;
use Component\Naver\NaverPay;
use Component\Page\Page;
use Component\Promotion\SocialShare;
use Component\Mall\Mall;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Debug\Exception\Except;
use Framework\Debug\Exception\AlertBackException;
use Component\Validator\Validator;
use Message;
use Globals;
use Request;
use Logger;
use Session;
use Exception;
use Endroid\QrCode\QrCode as EndroidQrCode;
use SocialLinks\Page as SocialLink;
use FileHandler;

class GoodsViewController extends \Bundle\Controller\Front\Goods\GoodsViewController
{
    
    public function index()
    {
        parent::index();
        // $_only = Request::server()->get('REMOTE_ADDR');
        // if($_only == "211.63.210.150"){ 서버관련
        //     print_r("AAA");
        // }
        // print_r("qqwwee");
        // print_r(Request::server()->get('REMOTE_ADDR'));
        

        $goodsNo = Request::get()->get("goodsNo");

        $this->getPdfFile($goodsNo);

        $goodsView = $this->getData('goodsView');
        $couponConfig = $this->getData('couponConfig');
        //print_r($couponConfig);

        $strSQL = "SELECT externalVimeoFl,goodsCd ,goodsNo ,externalVimeoUrl FROM es_goods WHERE goodsNo = '$goodsNo' ";
        $result = $this->db->query($strSQL);
        $data = $this->db->fetch($result);

        $video_filed  = explode("/",$data['externalVimeoUrl']);
        $video_url 	= $video_filed['2'];

        if($video_url=="vimeo.com") {
            $result = str_replace('https://vimeo.com/', 'https://player.vimeo.com/video/', $data['externalVimeoUrl']);//동영상 코드얻기
        }else{
            $result = str_replace('https://www.youtube.com/watch?v=', 'https://www.youtube.com/embed/', $data['externalVimeoUrl']);//동영상 코드얻기
        }
        $this->setData('viemoFl', $data['externalVimeoFl']);
        $this->setData('viemourl', $result);
        //echo "<pre>";
        //print_r($video_url);
        //exit;


        //2020-01-01 추가 사항 묶음상품 및 이미지 가져오기
        $Cd_no = $data['goodsCd'];
        $query="select es_goods.goodsNo,es_goods.imagePath,es_goodsImage.goodsNo,es_goodsImage.imageName,es_goodsImage.imageKind from es_goods INNER join es_goodsImage on es_goods.goodsNo = es_goodsImage.goodsNo and es_goodsImage.imageKind ='main' and es_goods.goodsCd ='$Cd_no' and es_goodsImage.imageKind !='NULL'";
        $ct_godds = $this->db->query_fetch($query);
        $this->setData('goodsCd', $ct_godds);
        $this->setData('goodsgetNo', $goodsNo);

        //echo "<pre>";
        //print_r($query);
        //exit;

        /** 상품별 평점 */
        $ppt = "AVG(goodsSpeedPt) AS goodsSpeedPt, 
                AVG(goodsQualityPt) AS goodsQualityPt, 
                AVG(goodsStatePt) AS goodsStatePt, 
                AVG(goodsColorPt) AS goodsColorPt, 
                AVG(goodsMaterialPt) AS goodsMaterialPt";
        
        $ptSQL = "SELECT " .$ppt. " FROM es_bd_goodsreview WHERE goodsNo = ".$goodsNo;
        $ptresult = $this->db->query_fetch($ptSQL, null);

        //소수점 버리기
        $goodsSpeedPt = number_format($ptresult[0]['goodsSpeedPt'],1);
        $goodsQualityPt = number_format($ptresult[0]['goodsQualityPt'],1);
        $goodsStatePt = number_format($ptresult[0]['goodsStatePt'],1);
        $goodsColorPt = number_format($ptresult[0]['goodsColorPt'],1);
        $goodsMaterialPt = number_format($ptresult[0]['goodsMaterialPt'],1);

        //총평점
        $alltotalpt = ($goodsSpeedPt+$goodsQualityPt+$goodsStatePt+$goodsColorPt+$goodsMaterialPt)/5;

        $this->setData('alltotalpt',$alltotalpt);
        $this->setData('goodsSpeedPt',$goodsSpeedPt);
        $this->setData('goodsQualityPt',$goodsQualityPt);
        $this->setData('goodsStatePt',$goodsStatePt);
        $this->setData('goodsColorPt',$goodsColorPt);
        $this->setData('goodsMaterialPt',$goodsMaterialPt);
        // 상품별 평점


        if($goodsView['optionDisplayFl'] == 'c') {

            $optionValue = [];

            for($i=0; $i<5; $i++) {
                foreach($goodsView['option'] as $k => $goodsOptionInfo) {
                    if(empty($goodsOptionInfo['optionValue'.($i+1)]) === false) {
                        $optionValue[$i][] = $goodsOptionInfo['optionValue'.($i+1)];
                    }
                }
                $optionValue[$i] = array_unique($optionValue[$i]);


                // 색상이름||#ff0000 으로 된 문자열을 분리저장한다.
                if(strpos($optionValue[$i][0], INT_DIVISION) !== false) {

                    foreach($optionValue[$i] as $k2 => $v2) {
                        $arrColorValue = explode(INT_DIVISION, $v2);
                        $optionValue[$i][$k2] = [];
                        $optionValue[$i][$k2]['colorValue'] = $v2;
                        $optionValue[$i][$k2]['colorName'] = $arrColorValue[0];
                        $optionValue[$i][$k2]['colorCode'] = $arrColorValue[1];
                    }

                }

            }


            $goodsView['initOption'] = $optionValue;
            $this->setData('goodsView', $goodsView);
            $this->setData('otlist', $goodsView['option']);

            //$ot = $goodsView['option'];
            //echo "<pre>";
            //print_r($ot);
            //exit;

        }
    }

    public function getPdfFile($goodsNo)
    {
        if(!is_object($this->db)) {
            $this->db = \App::load("DB");
        }

        $this->db->strField = "oriFileName, fileName";
        $this->db->strWhere = "goodsNo = ?";
        $this->db->strLimit = "0, 1";
        $this->db->strOrder = "regDt desc";

        $this->db->bind_param_push($bindParam, "i", $goodsNo);
        $query = $this->db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM wib_files' . implode(' ', $query);
        $data = $this->db->query_fetch($strSQL, $bindParam, false);

        $this->setData("pdfFile", gd_isset($data));
    }
}