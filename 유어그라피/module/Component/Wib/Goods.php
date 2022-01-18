<?php
namespace Component\Wib;

use Component\Wib\WibSql;

class Goods {
    
    public $db;
    
    public function __construct() {
        $this->db = new WibSql();
    }
    
    public function getGoodsDetailImg($goodsNo)
    {
        $data = [
            'es_goodsImage as T',
            ['T.imageKind', 'T.imageName', 'P.imagePath'],
            [' LEFT JOIN es_goods AS P ON T.goodsNo = P.goodsNo'],
            ['T.goodsNo' => [$goodsNo, 'i'], 'T.imageKind' => ['detail', 's']]
        ];
        
        return $this->db->WibJoin($data);
    }
    
    // 프레임 리스트
    public function getGoodsList($color, $type)
    {   
        $data = [
            'es_goods as T',
            ['P.imageKind', 'P.imageName', 'T.*'],
            [' LEFT JOIN es_goodsImage AS P ON T.goodsNo = P.goodsNo'],
            [
                'P.imageKind' => ['detail', 's'], 
                'P.imageNo' => ['1', 'i'],
                'T.coreColor' => [$color, 's'],
                'T.coreType' => [$type, 's']
            ],
            ['T.goodsNo asc']
        ];
        return $this->db->WibJoin($data);
    }
    
    public function getColorList()
    {
        $sql = "select coreColor from es_goods where coreColor <> '' group by coreColor";
        return $this->db->WibAll($sql);
    }
    
    public function getBasicGoodsNo($color)
    {
        $data = [
            'es_goods',
            ['goodsNo'],
            ['coreColor' => [$color, 's']],
            ['goodsNo asc'],
            ['1']
        ];
        return $this->db->WibQuery($data);
    }
    
    public function getGoodsInfo($goodsNo)
    {
        $data = [
            'es_goods',
            ['*'],
            ['goodsNo' => [$goodsNo, 's']]
        ];
        return $this->db->WibQuery($data);
    }
    
    public function getManrice()
    {
        $data = [
            'es_manPrice',
            ['*']
        ];
        return $this->db->WibQuery($data);
    }
    
    public function getArtList()
    {
        $data = [
            'es_artprint',
            ['*']
        ];
        return $this->db->WibQuery($data);
    }
    
    public function getCoverList()
    {
        $data = [
            'es_cover',
            ['*']
        ];
        return $this->db->WibQuery($data);
    }
    
}
