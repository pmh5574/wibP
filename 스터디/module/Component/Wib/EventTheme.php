<?php
namespace Component\Wib;

use Component\Wib\WibSql;
use Request;

class EventTheme {
    public $nowdate;
    public $db;
    public $sno;
    
    public function __construct($sno) {
        // 데이터베이스 설정
        $this->db = new WibSql();
        $this->sno = $sno;
    }
    
    public function getList()
    {
        $event_sql = "select * from es_displayTheme where kind = 'event' and sno = {$this->sno} order by sno desc";
        $data = $this->db->WibAll($event_sql, null);
        $goods = explode("||", $data[0]['goodsNo']);
        
        $mylist = "select A.*, A.goodsNm, A.imagePath, B.imageName, B.imageSize from 
                es_goods A
                left join es_goodsImage B on A.goodsNo = B.goodsNo
                where B.imageKind = 'list' and A.delFl = 'n' and A.goodsNo IN (". implode(",", $goods).") order by A.regDt desc"; 
        $list = $this->db->WibAll($mylist);

        
//        foreach ($data as $key => $value) {
//            preg_match_all("/<img[^>]*src=[\'\"]?([^>\'\"]+)[\'\"]?[^>]*>/", $value['pcContents'], $bestmatchs);
//            if(is_array($bestmatchs[0])){
//                $imgscont = str_replace('\"/', '/', $bestmatchs[0][0]);
//                $imgscont = str_replace('\"', '', $imgscont);
//                $data[$key]['img_cont'] = $imgscont;
//            }
//            if($value['thumList']){
//                $data[$key]['thumImg'] = '<img src="/data/display/'.$value['thumList'].'" title="'.$value['themeNm'].'" class="js-smart-img">';
//            }
//        }
        
        return $list;
    }
    
}
