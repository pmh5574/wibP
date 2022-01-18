<?php
namespace Controller\Admin\Board;

use Component\Wib\WibSql;
use Request;

class AddrListController extends \Controller\Admin\Controller
{
    public function index()
    {

        $addr_first = Request::post()->get('idx');
        
        $conn = new WibSql();
        $sql = [
            'es_wibaddr',
            ['idx', 'name'],
            ['pa_idx' => [$addr_first, 'i']],
            [' idx asc ']
        ];
        
        $list = $conn->WibQuery($sql);
        if(!is_array($list[0]) && $list){
            $list = array($list);
        }
        
        if($addr_first){
            echo json_encode(array('code' => '200', 'msg' => 'ok', 'list' => $list));
        }else{
            echo json_encode(array('code' => '400', 'msg' => '잘못된 요청입니다', ''));
        }
        
        exit;
        
    }
}
