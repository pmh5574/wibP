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


class ViewController extends \Bundle\Controller\Front\Board\ViewController
{
    public function index()
    {
        parent::index();
        
        $bdView = $this->getData('bdView');
        $req = $this->getData('req');


        if($req['bdId'] == 'goodsreview'){

            if($bdView['data']['writer'] == '네이버페이 구매자'){

                $bdView['data']['writer'] = $bdView['data']['writerNm'];

            }else{

                if($bdView['data']['writerId']){

                    $_str = substr($bdView['data']['writerId'],0,4);
                    $bdView['data']['writer'] = $_str.'****';

                }else if($bdView['data']['writerNm']){

                    if(!preg_match("/[\xE0-\xFF][\x80-\xFF][\x80-\xFF]/", $value['writerNm'])){
                        $_str = substr($bdView['data']['writerNm'],0,4);
                        $bdView['data']['writer'] = $_str.'****';
                    }

                }
            }
            
            $this->setData('bdView',$bdView);

        }   
    }
}