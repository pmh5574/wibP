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

use Request;
use Component\Wib\WibSql;

abstract class Board extends \Bundle\Component\Board\Board
{
    public function updateStoreData()
    {
        $wibSql = new WibSql();
        
        $req = $this->req;
        
        $sno = 0;
        
        if($req['sno']){
            $sno = $req['sno'];
        }else{
            //방금 저장된 sno가져오기
            $sno = $this->bdStoreGetSno();
        }

        $data = [
            'es_bd_store',
            array(
                'storeSearch' => [$req['storeSearch'],'s'],
                'storePhoneNum' => [$req['storePhoneNum'],'s'],
                'storeOpenTime' => [$req['storeOpenTime'],'s'],
                'storeHoliday' => [$req['storeHoliday'],'s'],
                'address' => [$req['address'],'s'],
                'addressSub' => [$req['addressSub'],'s'],
                'addressLat' => [$req['addressLat'],'s'],
                'addressLng' => [$req['addressLng'],'s']
            ),
            array('sno' => [$sno,'i'])
        ];
        
        $wibSql->WibUpdate($data);
        
    }
    
    public function bdStoreGetSno()
    {
        $wibSql = new WibSql();
        
        $wql = "SELECT sno FROM es_bd_store ORDER BY sno DESC LIMIT 1";
        $lastSno = $wibSql->WibNobind($wql);
        
        return $lastSno['sno'];
    }
}