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

use Request;

class GoodsBoardListController extends \Bundle\Controller\Front\Goods\GoodsBoardListController
{
    public function index()
    {
        parent::index();

        
        $bdList = $this->getData('bdList');
        
        $_only  = Request::server()->get('REMOTE_ADDR');
        
        foreach ($bdList['list'] as $key => $value){

            if($value['writer'] == '네이버페이 구매자'){

                $bdList['list'][$key]['writer'] = $value['writerNm'];

            }else{

                if($value['writerId']){

                    $_str = substr($value['writerId'],0,4);
                    $bdList['list'][$key]['writer'] = $_str.'****';

                }else if($value['writerNm']){
                    //한글인 경우 제외
                    if(!preg_match("/[\xE0-\xFF][\x80-\xFF][\x80-\xFF]/", $value['writerNm'])){
                        $_str = substr($value['writerNm'],0,4);
                        $bdList['list'][$key]['writer'] = $_str.'****';
                    } 

                }
            }
        }
        
        
//        $this->setData('bdList',$bdList);
        
    }
}