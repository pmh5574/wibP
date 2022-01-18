<?php
namespace Controller\Front\Order;

use Component\Wib\WibSql;

class NoticeController extends \Controller\Front\Controller{
    public function index()
    {
        $conn = new WibSql();
        
        $eventSql = "select * from morning_bmain_notice order by uid asc";
        $eventList = $conn->WibAll($eventSql);
        
        foreach ($eventList as $value) {
            $eventData = [
                'es_bd_notice',
                [
                    'groupNo' => ['0', 'i'],
                    //'groupThread' => ['', 'i'],
                    'memNo' =>  ['-1', 'i'],
                    'writerId' => ['tegut01', 's'],
                    'writerPw' => ['d41d8cd98f00b204e9800998ecf8427e', 's'],
                    'writerIp' => ['122.46.243.233', 's'],
                    'subject' => [$value['board_subject'], 's'],
                    'contents' => [str_replace("/mall/","https://www.testgut.com/mall/",stripslashes($value['board_body'])), 's'],
                    'isNotice' => ['n', 's'],
                    'isDelete' => ['n', 's'],
                    'hit' => [$value['board_hit'], 'i'],
                    'memoCnt' => ['0', 'i'],
                    'goodsNo' => ['0', 'i'],
                    'regDt' => [date("Y-m-d H:i:s",$value['register_date']), 's'],
                    'bdUploadStorage' => ['local', 's'],
                    'bdUploadPath' => ['upload/event/', 's'],
                    'bdUploadThumbPath' => ['upload/event/t/', 's'],
                    'parentSno' => ['0', 'i']
                ]
            ];
            
            $sno = $conn->WibInsert($eventData);
            
            // 그룹코드 업데이트 
            $updateData = [
                'es_bd_notice',
                [ 'groupNo' => ['-'.$sno, 'i'] ],
                [ 'sno' => [$sno, 'i'] ]
            ];
            $conn->WibUpdate($updateData);
        }
        
        exit;
    }
}
