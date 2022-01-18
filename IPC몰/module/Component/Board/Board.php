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
namespace Component\Board;

use Session;
use Component\Wib\WibSql;

abstract class Board extends \Bundle\Component\Board\Board
{
    public function saveData()
    {
        $msg = parent::saveData();
        
        if($this->req['bdId'] == 'store' ){
            
            if ($this->req['mode'] == 'modify') {
                $modify = $this->buildQuery->selectOne($this->req['sno']);
                $groupNo = $modify['groupNo'];
            }else{
                $groupNo = Session::get('groupNo_'.$this->req['bdId']);
            }
            
            $sb_first = $this->req['addrfirst'];
            $sb_second = $this->req['addrsecond'];
            $sb_last = $this->req['addrLast'];
            $addrLat = $this->req['addrLat'];
            $addrLng = $this->req['addrLng'];
            $phone = $this->req['phone'];
            
            $data = array();
            if($sb_first){
                $data['sb_first'] = [$sb_first, 's'];
            }
            if($sb_second){
                $data['sb_second'] = [$sb_second, 's'];
            }
            if($sb_last){
                $data['sb_last'] = [$sb_last, 's'];
            }
            if($addrLat){
                $data['addrLat'] = [$addrLat, 's'];
            }
            if($addrLng){
                $data['addrLng'] = [$addrLng, 's'];
            }
            if($phone){
                $data['phone'] = [$phone, 's'];
            }
            
            if($sb_first && $sb_second && $sb_last){
                $data['sb_all'] = [$this->getAddrs($sb_first).' '.$this->getAddrs($sb_second).' '.$sb_last, 's'];
            }
            
            $conn = new WibSql();
            $update = [
                'es_bd_'.$this->req['bdId'],
                $data,
                ['groupNo' => [$groupNo, 'i']]
            ];
            $conn->WibUpdate($update);
            
        }
        
        return $msg;
    }
    
    public function getList($isPaging = true, $listCount = 10, $subjectCut = 0, $arrWhere = [], $arrInclude = null, $displayNotice)
    {
        if($this->req['bdId'] == 'store'){
            $addfirst = $this->req['addrfirst'];
            $addrsecond = $this->req['addrsecond'];
            $keyword = $this->req['keyword'];
            
            if($addfirst != ''){
                $arrWhere[]  = " sb_first = '{$addfirst}' ";
            }
            
            if($addrsecond != ''){
                $arrWhere[]  = " sb_second = '{$addrsecond}' ";
            }
            
            if($keyword != ''){
                $arrWhere[]  = " ( subject like '%{$keyword}%' or  sb_all like '%{$keyword}%' )";
            }
        }
        
        
        return parent::getList($isPaging, $listCount, $subjectCut, $arrWhere, $arrInclude, $displayNotice);
    }
    
    public function getAddrs($cate)
    {
        $conn = new WibSql();
        $sql = [
            'es_wibaddr',
            ['name'],
            ['idx' => [$cate, 'i']]
        ];
        $list = $conn->WibQuery($sql);
        return $list['name'];
    }
}