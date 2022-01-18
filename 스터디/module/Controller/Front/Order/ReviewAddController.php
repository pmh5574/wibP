<?php
namespace Controller\Front\Order;

use Request;
use Component\Wib\WibSql;

class ReviewAddController extends \Controller\Front\Controller{
    public function index()
    {
        $conn = new WibSql();
        
        $reviewSql = "select * from morning_gcomment_table";
        $reviewList = $conn->WibAll($reviewSql);
        
        foreach ($reviewList as $value) {
            
            // 상품번호
            $goodsNo = $conn->WibAll("select goodsNo from es_goods where goodsCd = '{$value['guid']}'");
            
            // 회원번호 검색
            $memNum = $conn->WibAll("select memNo from es_member where memId = '{$value['comment_id']}'");
            
            //<img src="http://www.testgut.com/mall/iv_photo/
            // 각 파일별 이미지 재저장
            // 서버 공간 확보시 절대경로 변경예정
            $content = $value['comment_body'];
            if($value['comment_file1']){
                $content .= '<br><img src="http://www.testgut.com/mall/iv_photo/'.$value['comment_file1'].'" alt="">';
            }
            if($value['comment_file2']){
                $content .= '<br><img src="http://www.testgut.com/mall/iv_photo/'.$value['comment_file2'].'" alt="">';
            }
            if($value['comment_file3']){
                $content .= '<br><img src="http://www.testgut.com/mall/iv_photo/'.$value['comment_file3'].'" alt="">';
            }
            
            // 회원넘버
            $memNo = ($memNum[0]['memNo'])?$memNum[0]['memNo']:'0';
            
            // 회원아이디
            $writerId = ($value['comment_id'])?$value['comment_id']:'';
            
            // 블로그 링크
            $urlLink = ($value['comment_url'])?$value['comment_url']:'';
            
            // 리뷰 INSERT
            $reviewData = [
                'es_bd_goodsreview',
                [
                    'memNo' => [$memNo, 'i'],
                    'writerNm' => [$value['comment_name'], 's'],
                    'writerId' => [$writerId, 's'],
                    'writerPw' => [$value['comment_password'], 's'],
                    'writerIp' => [$value['comment_ip'], 's'],
                    'subject' => [$value['comment_subject'], 's'],
                    'contents' => [$content, 's'],
                    'urlLink' => [$urlLink, 's'],
                    'isNotice' => ['n', 's'],
                    'isSecret' => ['n', 's'],
                    'hit' => ['0', 'i'],
                    'memoCnt' => ['0', 'i'],
                    'goodsNo' => [$goodsNo[0]['goodsNo'], 'i'],
                    'goodsPt' => [$value['comment_star'], 'i'],
                    'isDelete' => ['n', 's'],
                    'bdUploadStorage' => ['local', 's'],
                    'bdUploadPath' => ['upload/goodsreview/', 's'],
                    'bdUploadThumbPath' => ['upload/goodsreview/t/', 's'],
                    'regDt' => [ date("Y-m-d H:i:s",$value['register_date']), 's'],
                    'isDelete' => ['n', 's'],
                    'isMobile' => ['n', 's']
                ]
            ];

            $sno = $conn->WibInsert($reviewData);
            
            // 그룹코드 업데이트 
            $updateData = [
                'es_bd_goodsreview',
                [ 'groupNo' => ['-'.$sno, 'i'] ],
                [ 'sno' => [$sno, 'i'] ]
            ];
            $conn->WibUpdate($updateData);
        }
        //echo print_r($reviewList);
        exit;

    }


}
