<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Enamoo S5 to newer
 * versions in the future.
 *
 * @copyright Copyright (c) 2015 GodoSoft.
 * @link http://www.godo.co.kr
 */
namespace Widget\Front\Board;

use Component\PlusShop\PlusReview\PlusReviewArticleFront;
class PlusReviewPhotoWidget extends \Bundle\Widget\Front\Board\PlusReviewPhotoWidget
{
    public function index()
    {
        parent::index();
        $req = [
            'cols'=> $this->getData('cols'),
            'rows'=> $this->getData('rows'),
            'thumSizeType'=> $this->getData('thumSizeType'),
            'thumWidth'=> $this->getData('thumWidth'),
        ];



        $plusReviewArticle = new PlusReviewArticleFront();
        $pageNum = $req['cols'] * $req['rows'];
        $data= $plusReviewArticle->getList(['pageNum'=>$pageNum,'reviewType'=>'photo'],false,false);


        //추가 사항
        foreach($data['list'] as $k=>$v){
            $d = $this->_get_other_data($v['goodsNo'],$plusReviewArticle->db);
            $data['list'][$k]['plusgoodsPt'] = $d['goodsPt'];
            $data['list'][$k]['goodsImageSrc'] = '/data/goods/'.$v['imagePath'].$d['goodsImage'];
        }


        $this->setData('data',$data['list']);
        $this->setData('req',$req);
        //print_r($data['list']);
    }



    //추가 메쏘드
    private function _get_other_data($goodsNo,$db){
        //평가 가져오기
        $q = "select sum(goodsPt) as goodsPt,count(*) as cnt from es_plusReviewArticle where goodsNo='$goodsNo' and applyFl='y'";
        $data =  $db->query_fetch($q, null);
        //$return['goodsPt'] = floatval($data[0]['goodsPt']/$data[0]['cnt']);
        $contgoodsPt=number_format($data[0]['goodsPt']/$data[0]['cnt'],1);// 평가 평균계산하기
        $return['goodsPt'] = floatval($contgoodsPt);// 평가 평균수치 처리

        //상품 이미지 가져오기
        $q = "select * from es_goodsImage where goodsNo='$goodsNo' and imageKind='main'";
        $data =  $db->query_fetch($q, null);
        $return['goodsImage'] = $data[0]['imageName'];
        return $return;
    }
}