<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link http://www.godo.co.kr
 */
namespace Controller\Front\Board;

use Request;
use Component\Wib\WibSql;

class ListController extends \Bundle\Controller\Front\Board\ListController
{
    public $mapList = [];
    
    public function index()
    {
        parent::index();
        
        $req = Request::get()->get('bdId');
        if($req == 'store'){
            
            $newdlist = $this->getData('bdList');
            $bdlist = $this->newlist($newdlist, $req);
            $addr = $this->wibaddr();
            
            $this->setData('addrfirst', Request::get()->get('addrfirst'));
            $this->setData('addrsecond', Request::get()->get('addrsecond'));
            $this->setData('keyword', Request::get()->get('keyword'));
            $this->setData('addr', $addr);
            $this->setData('bdList', $bdlist);
            $this->setData('mapList', json_encode($this->mapList));
            
        }
    }
    
    public function newlist($bdlist, $board)
    {
        $conn = new WibSql();
        foreach ($bdlist['list'] as $key => $value) {
            $sql = [
                'es_bd_'.$board,
                ['sb_first', 'sb_second', 'sb_last', 'sb_all', 'addrLat', 'addrLng', 'phone'],
                ['sno' => [$value['sno'], 'i']]
            ];
            $adData = $conn->WibQuery($sql);

            $bdlist['list'][$key]['sb_first'] = $adData['sb_first'];
            $bdlist['list'][$key]['sb_second'] = $adData['sb_second'];
            $bdlist['list'][$key]['sb_last'] = $adData['sb_last'];
            $bdlist['list'][$key]['sb_all'] = $adData['sb_all'];
            $bdlist['list'][$key]['addrLat'] = $adData['addrLat'];
            $bdlist['list'][$key]['addrLng'] = $adData['addrLng'];
            $bdlist['list'][$key]['phone'] = $adData['phone'];
            
            $this->mapList[$key]['title'] = $value['subject'];
            $this->mapList[$key]['addrLat'] = $adData['addrLat'];
            $this->mapList[$key]['addrLng'] = $adData['addrLng'];
        }
        
        return $bdlist;
    }
    
    public function wibaddr()
    {
        $conn = new WibSql();
        
        $sql = 'select * from es_wibaddr where pa_idx = 0 order by idx asc';
        return $conn->WibAll($sql);
    }
}