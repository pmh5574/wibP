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
namespace Controller\Mobile\Goods;

use Request;
use Component\Wib\WibSql;

class EventSaleController extends \Bundle\Controller\Mobile\Goods\EventSaleController
{
    public function index()
    {
        parent::index();
        
        $wibSql = new WibSql();

        $sno = Request::get()->get('sno');
           
        $wql = "SELECT * FROM es_displayTheme WHERE kind = 'event' AND sno != {$sno} AND wantChecks = 'y'";
        $result = $wibSql->WibAll($wql);

        $otherEvent = $this->getStatus($result);

        $this->setData('otherEvent',$otherEvent);
        
        
    }
    
    public function getStatus($result)
    {
        foreach($result as $key => $value){
                
            $nowDate = strtotime(date("Y-m-d H:i:s"));
            $displayStartDate = strtotime($value['displayStartDate']);
            $displayEndDate = strtotime($value['displayEndDate']);

            if ($nowDate < $displayStartDate) {
                $result[$key]['statusText'] = __('대기');
            } else if ($nowDate > $displayStartDate && $nowDate < $displayEndDate) {
                $result[$key]['statusText'] = __('진행중');
            } else if ($nowDate > $displayEndDate) {
                $result[$key]['statusText'] = __('종료');
            } else {
                $result[$key]['statusText'] = __('오류');
            }
        }
        
        return $result;
    }
}