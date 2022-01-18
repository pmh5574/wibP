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

namespace Controller\Admin\Policy;

use Request;
use Component\Wib\WibSql;

class LayerDeliveryBundleController extends \Controller\Admin\Controller
{
    
    public function index()
    {
        $wib = new WibSql();
        //--- 모듈 호출
        $delivery = \App::load('\\Component\\Delivery\\Delivery');
        $postValue = Request::post()->toArray();

        $data = [];

        switch($postValue['mode']){
            case 'save':

                break;
            case 'modify':
                $data = $wib->modfiy($postValue);

                break;
            case 'delete':
                $message = $wib->delete($postValue);
                $this->layer($message, 'top.location.reload(true);');
                break;
            case 'search_scm':
                $arr = $wib->getDelivery($postValue['deliverySno']);

                echo  json_encode(gd_htmlspecialchars_stripslashes($arr));
                exit;
                break;
        }
        
        //--- 관리자 디자인 템플릿
        $this->getView()->setDefine('layout', 'layout_layer.php');

        // 공급사 페이지 설정
        $this->getView()->setPageName('policy/layer_delivery_bundle.php');

        $this->setData('sno', $postValue['sno']);
        $this->setData('data',$data);
    }

    

//    public function getDelivery($sno)
//    {
//        $db = \App::load('DB');
//
//        $arrWhere[] = 'sno = ?';
//
//        $db->bind_parm_push($arrBind,'s',$sno);
//
//        $strWHERE = ' WHERE ' . implode(' AND ', $arrWhere);
//        
//        $strSQL = 'SELECT sno, method FROM es_scmDeliveryBundle ' . $strWhere;
//        $getData = $db->query_fetch($strSQL, $arrBind, false);
//        
//        return $getData;
//    }

//    public function delete($arrData)
//    {
//        $db = \App::load('DB');
//
//        $total = count($arrData['deliverChk']);
//        $success = 0;
//
//        // print_r($arrData);exit;
//        foreach($arrData['deliverChk'] as $dataSno){
//
//            $getData = $this->getSnoDeliveryBundle($dataSno, true);
//            if($getData['cnt'] == 0){
//                $arrBind = array('i', $dataSno);
//                $db->set_delete_db('es_scmDeliveryBundle', 'sno = ?', $arrBind);
//                $success++;
//            }
//        }
//
//        if($total!=$success){
//            $msg = __('배송조건을 포함하고있는 상품이 존재하는경우 삭제하실 수 없습니다.');
//        }
//
//        return sprintf(__('총 %1$d개의 배송비조건 중 %2$d건을 삭제했습니다.<br>'.$msg), $total, $success);
//           
//    }
//
//    
//    public function getSnoDeliveryBundle($sno, $debugFl = true)
//    {
//        $db = \App::load('DB');
//        $strSQL = "SELECT COUNT(*) cnt FROM es_goods WHERE goodsDeliveryKey = {$sno}";
//        $result = $db->fetch($strSQL);
//        // print_r($result);exit;
//        return $result;
//    }
}

