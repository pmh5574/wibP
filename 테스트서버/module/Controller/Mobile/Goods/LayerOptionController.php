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
namespace Controller\Mobile\Goods;
use App;
use Framework\Utility\StringUtils;
use Session;
use Request;
use Exception;
use Framework\Utility\ArrayUtils;
use Framework\Utility\SkinUtils;
use Globals;

/**
 * Class LayerDeliveryAddress
 *
 * @package Bundle\Controller\Front\Order
 * @author  su
 */
class LayerOptionController extends \Bundle\Controller\Mobile\Goods\LayerOptionController
{
	   public function index()
    {
        parent::index();

        $goodsNo = Request::get()->get("goodsNo");

        $this->getPdfFile($goodsNo);

        $goodsView = $this->getData('goodsView');
        

        $strSQL = "SELECT goodsCd ,goodsNo FROM es_goods WHERE goodsNo = '$goodsNo' ";
        $result = $this->db->query($strSQL);
        $data = $this->db->fetch($result);
        

        //2020-01-01 추가 사항 묶음상품 및 이미지 가져오기
        $Cd_no = $data['goodsCd'];
        $query="select es_goods.goodsNo,es_goods.imagePath,es_goodsImage.goodsNo,es_goodsImage.imageName,es_goodsImage.imageKind from es_goods INNER join es_goodsImage on es_goods.goodsNo = es_goodsImage.goodsNo and es_goodsImage.imageKind ='main' and es_goods.goodsCd ='$Cd_no' and es_goodsImage.imageKind !='NULL'";
        $ct_godds = $this->db->query_fetch($query);
        $this->setData('goodsCd', $ct_godds);
        $this->setData('goodsgetNo', $goodsNo);

        //echo "<pre>";
        //print_r($goodsNo);
        //exit;


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