<?php
namespace Controller\Front\Order;

use Component\Wib\WibSql;

class EventBoardController extends \Controller\Front\Controller{
    public function index()
    {
        $conn = new WibSql();
        
        $eventSql = "select * from morning_eventmanage_table where pc_img1 <> '' order by uid asc";
        $eventList = $conn->WibAll($eventSql);
        
        foreach ($eventList as $value) {
            $listImgVal = explode("/", $value['pc_img1']);
            $listImg = $listImgVal[count($listImgVal)-1];
            
            $eventData = [
                'es_bd_event',
                [
                    'groupNo' => ['0', 'i'],
                    //'groupThread' => ['', 'i'],
                    'memNo' =>  ['-1', 'i'],
                    'writerId' => ['tegut01', 's'],
                    'writerPw' => ['d41d8cd98f00b204e9800998ecf8427e', 's'],
                    'writerIp' => ['122.46.243.233', 's'],
                    'subject' => [$value['subject'], 's'],
                    'contents' => [str_replace("/mall/","https://www.testgut.com/mall/",stripslashes($value['pc_html'])), 's'],
                    'uploadFileNm' => [$listImg, 's'],
                    'saveFileNm' => [$listImg, 's'],
                    'isNotice' => ['n', 's'],
                    'isSecret' => ['n', 's'],
                    'hit' => ['1', 'i'],
                    'memoCnt' => ['0', 'i'],
                    'goodsNo' => ['0', 'i'],
                    'eventStart' => [date("Y-m-d H:i:s",$value['start_date']), 's'],
                    'eventEnd' => [date("Y-m-d H:i:s",$value['end_date']), 's'],
                    'regDt' => [$value['reg_date'], 's'],
                    'modDt' => [$value['up_date'], 's'],
                    'bdUploadStorage' => ['local', 's'],
                    'bdUploadPath' => ['upload/event/', 's'],
                    'bdUploadThumbPath' => ['upload/event/t/', 's'],
                    'parentSno' => ['0', 'i'],
                    'isMobile' => ['n', 's']
                ]
            ];
            
            $sno = $conn->WibInsert($eventData);
            
            // 그룹코드 업데이트 
            $updateData = [
                'es_bd_event',
                [ 'groupNo' => ['-'.$sno, 'i'] ],
                [ 'sno' => [$sno, 'i'] ]
            ];
            $conn->WibUpdate($updateData);
            echo $listImg.'<br>';
        }
        
        exit;
    }
}
