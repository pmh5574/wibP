<?php

/**
 * 상품노출형태 관리
 * @author atomyang
 * @version 1.0
 * @since 1.0
 * @copyright ⓒ 2016, NHN godo: Corp.
 */
namespace Component\Promotion;

use Component\Database\DBTableField;
use Component\Validator\Validator;
use Globals;
use LogHandler;
use Request;

class TimeSale extends \Bundle\Component\Promotion\TimeSale
{
    public function getGoodsTimeSale($goodsNo)
    {
        if (gd_is_plus_shop(PLUSSHOP_CODE_TIMESALE) === false) return false;

        $arrBind = [];

        if (\Request::isMobile()) {
            $where[]  = 'mobileDisplayFl =?';
        } else {
            $where[]  = 'pcDisplayFl =?';
        }
        $this->db->bind_param_push($arrBind, 's','y');
        $where[] = "FIND_IN_SET(?,replace(ts.goodsNo,'".INT_DIVISION."',','))";
        $this->db->bind_param_push($arrBind, 's', $goodsNo);

        $where[]  = 'startDt < ? AND  endDt > ? ';
        $this->db->bind_param_push($arrBind, 's', date('Y-m-d H:i:s'));
        $this->db->bind_param_push($arrBind, 's', date('Y-m-d H:i:s'));
        //ts.*뒤에 반환값 추가
        $this->db->strField    = "ts.*, FIND_IN_SET(".$goodsNo.",replace(ts.goodsNo,'".INT_DIVISION."',',')) as eachBenefitFindNo";
        $this->db->strWhere = implode(' AND ', gd_isset($where));

        $query  = $this->db->query_complete();
        $strSQL = 'SELECT '.array_shift($query).' FROM '.DB_TIME_SALE.' as ts '.implode(' ', $query);

        $getData    = $this->db->query_fetch($strSQL, $arrBind);

        unset($arrBind);

        if($getData) {
            return gd_htmlspecialchars_stripslashes($getData[0]);
        } else {
            return false;
        }
    }
}