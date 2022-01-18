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

use Component\Storage\Storage;
use Framework\Debug\Exception\LayerException;
use Framework\Debug\Exception\LayerNotReloadException;
use Exception;
use Message;
use Request;
use Session;

class GoodsPsController extends \Bundle\Controller\Admin\Goods\GoodsPsController
{
    public function index()
    {
        parent::index();
        
        $postValue = Request::post()->toArray();
        $goods     = \App::load('\\Component\\Goods\\GoodsAdmin');

        $mode = $postValue['mode'];

        try {
            switch ($mode) {   
                
                // 가격/마일리지/재고 수정
                case 'batch_total':
                    
                    if($postValue['isPrice'] =='y') {

                        $data = $goods->setBatchTotal($postValue);
                        echo json_encode(gd_htmlspecialchars_stripslashes(array('info'=>$data,'cnt'=>count($data))),JSON_FORCE_OBJECT);
                        exit;

                    } else {

                        $applyFl = $goods->setBatchTotal($postValue);

                        if($applyFl =='a') {
                            $this->layer(__("승인을 요청하였습니다."));
                        } else {
                            $this->layer(__('간편 수정이 완료 되었습니다.'));
                        }

                    }

            }

        } catch (Exception $e) {
            throw new LayerException($e->getMessage());
        }
    }
}