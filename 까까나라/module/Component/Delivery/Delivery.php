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
namespace Component\Delivery;

/**
 * 배송비조건 관리 및 지역별 배송비 class
 *
 * @package Bundle\Component\Delivery
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 */
class Delivery extends \Bundle\Component\Delivery\Delivery
{
    public function setSearchDelivery($searchMode = null)
    {
        parent::setSearchDelivery($searchMode);
        $this->arrWhere[] = "d.seeChecks = 'y'";
        
    }
}