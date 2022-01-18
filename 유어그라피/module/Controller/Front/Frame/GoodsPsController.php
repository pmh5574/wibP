<?php
namespace Controller\Front\Frame;

use Component\Wib\Goods;
use Request;

class GoodsPsController extends \Controller\Front\Controller
{
    public $result;
    public $goods;
    
    public function __construct() {
        parent::__construct();
        
        $this->goods = new Goods();
    }
    
    public function index()
    {
        
        if(Request::isAjax()){
            $arrData = Request::post()->toArray();
            switch ($arrData['mode']) {
                case 'get_detail_image':
                    $this->result = $this->getGoodsDetailImg($arrData['goodsNo']);
                break;
            
                case 'get_frame_list':
                    
                    $list = $this->getGoodsList($arrData['color'], $arrData['frameBaseType']);
                    $result = [
                        'code' => '200',
                        'list' => $list,
                        'total' => count($list)
                    ];
                    
                    if(!$list){$result['code'] = '500';}
                    
                    $this->json($result);
                break;
                
                case 'get_frame':
                    
                    $frame = $this->getGoodsInfo($arrData['goodsNo']);
                    $result = [
                        'code' => '200',
                        'data' => $frame
                    ];
                    
                    if(!$frame){$result['code'] = '500';}
                    
                    $this->json($result);
                    
                break;
            }
            
            exit;
        }
        
    }
    
    public function getGoodsInfo($goodsNo)
    {
        return $this->goods->getGoodsInfo($goodsNo);   
    }
    
    public function getCoverList()
    {
        return $this->goods->getCoverList();   
    }
    
    public function getColorList()
    {
        return $this->goods->getColorList();
    }
    
    public function getGoodsDetailImg($goodsNo)
    {
        return $this->goods->getGoodsDetailImg($goodsNo);
    }
    
    public function getGoodsList($color, $type)
    {
        $result = $this->goods->getGoodsList($color, $type);
        
        if(!is_array($result[0]) && isset($result['goodsNo'])){
            $return[] = $result;
            $result = $return;
        }
        
        return $result;
    }
    
    public function getBasicGoodsNo($color)
    {
        $goods = $this->goods->getBasicGoodsNo($color);
        return $goods['goodsNo'];
    }
    
    public function getManrice()
    {
        return $this->goods->getManrice();
    }
    
    public function getArtList()
    {
        return $this->goods->getArtList();
    }

}
