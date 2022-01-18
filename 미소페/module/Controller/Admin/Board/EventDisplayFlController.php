<?php
namespace Controller\Admin\Board;

use Request;
use Component\Wib\WibSql;

class EventDisplayFlController extends \Controller\Admin\Controller
{
    public function index()
    {
        $wib = new WibSql();

        $getValue = Request::post()->toArray();

        if($getValue){
            
            $data = array(
                0 => 'es_bd_event',
                1 => ['eventDisplayFl' => [$getValue['Fl'], 's']],
                2 => ['sno' => [$getValue['sno'], 'i']]
            );
         
            $wib->WibUpdate($data);

        }
        exit;
    }
}