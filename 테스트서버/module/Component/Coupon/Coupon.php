<?php

/**
 *
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link      http://www.godo.co.kr
 *
 */
namespace Component\Coupon;

use Request;

/**
 * Coupon Class
 *
 * @author    sj, artherot
 * @version   1.0
 * @since     1.0
 * @copyright ⓒ 2016, NHN godo: Corp.
 */
class Coupon extends \Bundle\Component\Coupon\Coupon
{
    public function checkCouponType($couponNo = 0, $couponType = 'y', $memberCouponNo = null) {
        
        $checker = parent::checkCouponType($couponNo, $couponType, $memberCouponNo);
        
        /**
         * 비회원 사용가능 페이퍼 쿠폰 테스트 추가저장
         * 랜덤8자리 코드 생성후 메인쿠폰 코드값으로 지정하여 저장
         * 생성후 삭제관련내용은 차후 추가예정
         */
        
        // 쿠폰타입
        $couponTypePost = Request::post()->get('couponUseType');
        // 쿠폰생성 매수
        $couponNumber = intval(Request::post()->get('codeNumber'));
        // 모드 : insertCouponRegist => 쿠폰등록모드
        $mode = Request::post()->get('mode');
        // 조건 맞을시 작동
        if($couponTypePost == 'code' && $couponNumber > 0 && $mode == 'insertCouponRegist'){
            
//            $createsql = "CREATE TABLE `es_couponPaper` (
//  `sno` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
//  `psno` int(10) UNSIGNED NOT NULL,
//  `code` varchar(10) NOT NULL,
//  `type` enum('y','n') DEFAULT 'n',
//  `regdt` datetime DEFAULT NULL,
//  `useid` varchar(50) DEFAULT NULL,
//  `ordercode` varchar(16) DEFAULT NULL,
//  PRIMARY KEY(sno),
//  INDEX (sno),
//  INDEX (psno) 
//) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
//$this->db->query_fetch($createsql, null);

            
            // 생성매수만큼 데이터 저장
            for($i=0;$i<$couponNumber;$i++){
               $paperArr = substr(md5($i.date('YmdHis')), 0, 8);

               $arrBind = [];
               $paperSQL = "INSERT INTO es_couponPaper SET psno = ?, code = ?";
               $this->db->bind_param_push($arrBind, 'i', $couponNo);
               $this->db->bind_param_push($arrBind, 's', $paperArr);
               $this->db->bind_query($paperSQL, $arrBind);
               
            }
            
            
            // 쿠폰 구분자 업데이트 => 타입이 enum형식이라 업데이트 불가능
            //$parentUpdate =  "update es_coupon SET couponUseType = 'paper' where couponNo = ".$data[0]['couponNo'];
            //$paper_reault = $this->db->query($parentUpdate);
            //$this->db->fetch($paper_reault);
            
        }
        return $checker;
        
    }
}