<?php
namespace Component\Wib;

use Component\Wib\WibSql;

class WibGoods
{
    public function WibFe($data)
    {
        $gdlist = $data;
        
        $wib = new WibSql();
        
        if($gdlist){
            foreach($gdlist as $key =>$value){
                foreach($value as $key2 => $val2){

                    $goodsNo = $val2['goodsNo'];

                    $data = [
                        'es_goods',
                        'goodsDiscountPer',
                        array('goodsNo' => [$goodsNo, 'i'])
                    ];

                    $result = $wib->WibQuery($data);

                    $gdlist[$key][$key2]['goodsDiscountPer'] = $result['goodsDiscountPer'];

                }
            }
        }
        
        return $gdlist;
    }
}
