<?php
/**
 * 타임세일 메인출력 컴포넌트 with wibsql
 */
namespace Component\Wib;

use Component\Wib\WibSql;

class TimeSale {
    
    public $nowdate;
    public $db;
    
    public function __construct() {
        // 오늘 날짜 설정
        $this->nowdate = date('Y-m-d H:i:s');
        // 데이터베이스 설정
        $this->db = new WibSql();
    }
    
    // 타임세일 리스트
    public function getList($sno)
    {
        $sql = [
            'es_timeSale',
            ['*'],
            ['startDt' => [$this->nowdate, 's', '<='], 'endDt' => [$this->nowdate, 's', '>='], 'sno' => [$sno, 'i', '=']],
            [' sno desc'],
            [' 0,1']
        ];
        return $this->db->WibQuery($sql);
    }
    
    // 타임세일에 해당되는 상품리스트
    public function getPrdList($prdlist)
    {
        $timeGoodsList = str_replace("||", ",",  $prdlist);
        
        $timeGoods = "SELECT A.*, B.imageName FROM es_goods A left join es_goodsImage B on A.goodsNo = B.goodsNo 
                    where A.goodsNo IN (".$timeGoodsList.") and B.imageKind = 'main' ";
        
        return $this->db->WibAll($timeGoods, null);
    }
    
}
