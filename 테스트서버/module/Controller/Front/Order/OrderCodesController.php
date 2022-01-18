<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Controller\Front\Order;

use App;
use Request;
use Framework\Debug\Exception\AlertCloseException;

/**
 * 코드쿠폰 사용여부 처리
 *
 * @author WIB_PUB
 */
class OrderCodesController extends \Controller\Front\Controller {
    
    protected $db = null;
    
    public function __construct() {
        
        parent::__construct();
        
        if (!is_object($this->db)) {
            $this->db = \App::load('DB');
        }
    }
    
    public function index()
    {
        // ajax 체크
        if (Request::isAjax()) {
            
            // 쿠폰코드
            $code = Request::post()->get('couponcode');
            // 상품합계금액
            $totalsumprice = intval(Request::post()->get('totalsumprice'));
            
            // 쿠폰코드 기반으로 부모쿠폰 join
            $code_SQL = "SELECT * FROM es_couponPaper A left join es_coupon B on A.psno = B.couponNo WHERE A.code = '" . $code . "' and A.type = 'n'";
            $result = $this->db->query($code_SQL);
            $data = $this->db->fetch($result);
            
            // 배열 초기화
            $jsonArr = array(
                'psno' => '',
                'price' => '',
                'msg' => '',
                'resultcode' => ''
            );
            
            // 퍼센트일경우 상품합계금액 기반으로 계산
            if($data['couponBenefitType'] == 'percent'){
                $jsonArr['price'] = $totalsumprice/100 * intval($data['couponBenefit']);
            }else{
                $jsonArr['price'] = $data['couponBenefit'];
            }
            // 부모쿠폰 넘버 저장
            $jsonArr['psno'] = $data['psno'];
            
            if($data['psno'] != ''){
                $jsonArr['msg'] = '사용할수 있는 쿠폰';
                $jsonArr['resultcode'] = '200';
            }else{
                $jsonArr['msg'] = '사용했거나 존재하지 않는 쿠폰입니다.';
                $jsonArr['resultcode'] = '500';
            }
            
            // json 출력
            $this->json($jsonArr);
            //echo $data['psno'];
            exit;
        }else{
            throw new AlertCloseException('잘못된 접근입니다.');
        }
    }
    
}
