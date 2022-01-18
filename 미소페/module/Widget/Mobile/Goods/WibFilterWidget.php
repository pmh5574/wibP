<?php
namespace Widget\Mobile\Goods;

//use App;
use Request;

class WibFilterWidget extends \Widget\Mobile\Widget
{
    public $filter;
    public $gets;
    
    public function __construct() {
        parent::__construct();
        
        // 컬러 코드
        $color = $this->getColor();
        
        // 필터 셋팅
        $this->filter = [
            'color' => $color,
            'season' => ['S/S', 'F/W', '봄', '여름', '가을', '겨울', '사계절'],
            'pattern' => ['225', '230', '235', '240', '245', '250',  '255',  '260', '265', '270']
        ];
        
        $this->gets = Request::get()->toArray();
    }
    
    public function index()
    {
        
        $color = [];
        foreach ($this->filter['color'] as $key => $fvalue) {
            $color[$key] = '';
            foreach ($this->gets['filterColor'] as $gvalue) {
                if($fvalue[0] == $gvalue){
                    $color[$key] = $gvalue;
                }
            }
        }
        
        $cateNum = [
            'MEN' => '023',
            'WOMEN' => '022'
        ];
        $this->setData('cateNum', $cateNum);
        
        $pattern = $this->getCheckList('pattern', 'filterPattern');
        $season = $this->getCheckList('season', 'filterSeason');
        $price = $this->gets['priceCheck'];
        $category = $this->getCategory();
        
        $this->setData('fcolor', $color);
        $this->setData('fpattern', $pattern);
        $this->setData('fseason', $season);
        $this->setData('fprice', $price);
        
        $fpStart = ($this->gets['fpStart'])?$this->gets['fpStart']:0;
        $fpEnd = ($this->gets['fpEnd'])?$this->gets['fpEnd']:150000;
        $this->setData('fpStart', str_replace(",","",$fpStart));
        
        $this->setData('fpEnd', str_replace(",","",$fpEnd));
        $this->setData('color', $this->filter['color']);
        $this->setData('season', $this->filter['season']);
        $this->setData('pattern', $this->filter['pattern']);
        $this->setData('category', $category);
        $this->setData('cateGoods', $this->gets['cateGoods']);
    }
    
    public function getCheckList($paName, $getName)
    {
        $pattern = [];
        foreach ($this->filter[$paName] as $key => $fvalue) {
            $pattern[$key] = '';
            foreach ($this->gets[$getName] as $gvalue) {
                if($fvalue == $gvalue){
                    $pattern[$key] = $gvalue;
                }
            }
        }
        
        return $pattern;
    }

    public function getColor()
    {
        $db = \App::load('DB');
        $color_sql = "select itemNm from es_code where groupCd = 05001 ";
        $result = $db->query_fetch($color_sql);
        
        $return = [];
        foreach ($result as $key => $value) {
            $data = explode("^|^", $value['itemNm']);
            $return[] = [$data[0], '#'.$data[1], 'c'.($key+1)];
        }
        
        return $return;
    }
    
    // MEN 023 : WOMEN 022
    public function getCategory()
    {
        $db = \App::load('DB');
        $name = [
            '023' => 'MEN',
            '022' => 'WOMEN'
        ];
        $list = [];
        for($i=0;$i<2;$i++){
            $num = $i+2;
            $sql = "select * from es_categoryGoods where cateCd like '02".$num."%' and cateCd != '02".$num."' and cateDisplayFl = 'y' order by cateCd asc ";
            $list[$name['02'.$num]] = $db->query_fetch($sql);
        }
        
        return $list;
    }

}
