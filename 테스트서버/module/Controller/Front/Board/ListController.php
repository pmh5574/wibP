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

use Request;

class ListController extends \Bundle\Controller\Front\Board\ListController
{
    public function index()
    {
        parent::index();
        $req = Request::get()->toArray();
        if($req['bdId'] == 'videoreview'){
            $bdList = $this->getData('bdList');
            //print_r($bdList);
            foreach($bdList['list'] as $key => $value){
                // $fileNm = $bdList['list'][$key]['saveFileNm'];
                // $saveFileNm = explode("^|^",$fileNm);            
                // $bdList['list'][$key]['selfsaveFileNm'] = $saveFileNm;
                //contents에 적은 이미지 자체를 출력
                preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $bdList['list'][$key]['contents'], $_matches);
                //$_matches[0]은 img태그 자체 $_matches[1] 이미지 태그안에 글자만
                $bdList['list'][$key]['selfimg'] = $_matches[0];
                $bdList['list'][$key]['selfimgsrc'] = $_matches[1][0];
                //print_r($_matches[1]);
                //print_r($bdList['list']);
                
            }
 
            //print_r($bdList['list']);

            $this->setData('bdList', $bdList);
            $this->getView()->setDefine('list','board/skin/gallery/list.html');
        }    
            
            

            
    }
}