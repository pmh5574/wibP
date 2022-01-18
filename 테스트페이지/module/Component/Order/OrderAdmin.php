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
namespace Component\Order;


use Component\Member\Manager;
/**
 * 주문 class
 * 주문 관련 관리자 Class
 *
 * @package Bundle\Component\Order
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 */
class OrderAdmin extends \Bundle\Component\Order\OrderAdmin
{
    /**
     * 관리자 주문 리스트 엑셀
     * 반품/교환/환불 정보까지 한번에 가져올 수 있게 되어있다.
     *
     * @param string $searchData 검색 데이타
     * @param string $searchPeriod 기본 조회 기간
     *
     * @return array 주문 리스트 정보
     */
    public function getOrderListForAdminExcel($searchData, $searchPeriod, $isUserHandle = false, $orderType='goods',$excelField,$page,$pageLimit)
    {
        unset($this->arrWhere);
        unset($this->arrBind);
        //$excelField  / $page / $pageLimit 해당 정보가 없을경우 튜닝한 업체이므로 기존형태로 반환해줘야함
        // --- 검색 설정
        $this->_setSearch($searchData, $searchPeriod, $isUserHandle);

        if ($searchData['statusCheck'] && is_array($searchData['statusCheck'])) {
            foreach ($searchData['statusCheck'] as $key => $val) {
                foreach ($val as $k => $v) {
                    $_tmp = explode(INT_DIVISION, $v);
                    if($orderType =='goods' && $searchData['view'] =='order') unset($_tmp[1]);
                    if($_tmp[1]) {
                        $tmpWhere[] = "(og.orderNo = ? AND og.sno = ?)";
                        $this->db->bind_param_push($this->arrBind, 's', $_tmp[0]);
                        $this->db->bind_param_push($this->arrBind, 's', $_tmp[1]);
                    } else {
                        $tmpWhere[] = "(og.orderNo = ?)";
                        $this->db->bind_param_push($this->arrBind, 's', $_tmp[0]);
                    }
                }
            }

            $this->arrWhere[] = '(' . implode(' OR ', $tmpWhere) . ')';
            unset($tmpWhere);
        }

        // 주문상태 정렬 예외 케이스 처리
        if ($searchData['sort'] == 'og.orderStatus asc') {
            $searchData['sort'] = 'case LEFT(og.orderStatus, 1) when \'o\' then \'1\' when \'p\' then \'2\' when \'g\' then \'3\' when \'d\' then \'4\' when \'s\' then \'5\' when \'e\' then \'6\' when \'b\' then \'7\' when \'r\' then \'8\' when \'c\' then \'9\' when \'f\' then \'10\' else \'11\' end';
        } elseif ($searchData['sort'] == 'og.orderStatus desc') {
            $searchData['sort'] = 'case LEFT(og.orderStatus, 1) when \'f\' then \'1\' when \'c\' then \'2\' when \'r\' then \'3\' when \'b\' then \'4\' when \'e\' then \'5\' when \'s\' then \'6\' when \'d\' then \'7\' when \'g\' then \'8\' when \'p\' then \'9\' when \'o\' then \'10\' else \'11\' end';
        }

        // 정렬 설정
        if($orderType === 'goods'){
            $orderSort = gd_isset($searchData['sort'], $this->orderGoodsMultiShippingOrderBy);
        }
        else {
            $orderSort = gd_isset($searchData['sort'], $this->orderGoodsOrderBy);
        }
        if($orderType === 'goods'){
            if(!preg_match("/orderInfoCd/", $orderSort)){
                $orderSort = $orderSort . ", oi.orderInfoCd asc";
            }
        }

        // 사용 필드
        $arrInclude = [
            'o.orderNo',
            'o.orderChannelFl',
            'o.apiOrderNo',
            'o.memNo',
            'o.orderChannelFl',
            'o.orderGoodsNm',
            'o.orderGoodsCnt',
            'o.settlePrice as totalSettlePrice',
            'o.totalDeliveryCharge',
            'o.useDeposit as totalUseDeposit',
            'o.useMileage as totalUseMileage',
            '(o.totalMemberDcPrice + o.totalMemberDeliveryDcPrice) AS totalMemberDcPrice',
            'o.totalGoodsDcPrice',
            '(o.totalCouponGoodsDcPrice + o.totalCouponOrderDcPrice + o.totalCouponDeliveryDcPrice)as totalCouponDcPrice',
            'totalCouponOrderDcPrice',
            'totalCouponDeliveryDcPrice',
            'o.totalMileage',
            'o.totalGoodsMileage',
            'o.totalMemberMileage',
            '(o.totalCouponGoodsMileage+o.totalCouponOrderMileage) as totalCouponMileage',
            'o.settleKind',
            'o.bankAccount',
            'o.bankSender',
            'o.receiptFl',
            'o.pgResultCode',
            'o.pgTid',
            'o.pgAppNo',
            'o.paymentDt',
            'o.addField',
            'o.mallSno',
            'o.orderGoodsNmStandard',
            'o.overseasSettlePrice',
            'o.currencyPolicy',
            'o.exchangeRatePolicy',
            'o.totalEnuriDcPrice',
            '(o.realTaxSupplyPrice + o.realTaxVatPrice + o.realTaxFreePrice) AS totalRealSettlePrice',
            'o.checkoutData',
            'o.trackingKey',
            'o.fintechData',
            'o.checkoutData',
            'o.orderTypeFl',
            'o.appOs',
            'o.pushCode',
            'o.memberPolicy',
            'o.totalMyappDcPrice',
            'oi.regDt as orderDt',
            'oi.orderName',
            'oi.orderEmail',
            'oi.orderPhone',
            'oi.orderCellPhone',
            'oi.receiverName',
            'oi.receiverPhone',
            'oi.receiverCellPhone',
            'oi.receiverUseSafeNumberFl',
            'oi.receiverSafeNumber',
            'oi.receiverSafeNumberDt',
            'oi.receiverZonecode',
            'oi.receiverZipcode',
            'oi.receiverAddress',
            'oi.receiverAddressSub',
            'oi.receiverCity',
            'oi.receiverState',
            'oi.receiverCountryCode',
            'oi.orderMemo',
            'oi.packetCode',
            'oi.orderInfoCd',
            'oi.visitName',
            'oi.visitPhone',
            'oi.visitMemo',
            '(og.orderDeliverySno) AS orderDeliverySno ',
            '(og.scmNo) AS scmNo ',
            '(og.apiOrderGoodsNo) AS apiOrderGoodsNo ',
            '(og.sno) AS orderGoodsSno ',
            '(og.orderCd) AS orderCd ',
            '(og.orderStatus) AS orderStatus ',
            '(og.goodsNo) AS goodsNo ',
            '(og.goodsCd) AS goodsCd ',
            '(og.goodsModelNo) AS goodsModelNo ',
            '(og.goodsNm) AS goodsNm ',
            '(og.optionInfo) AS optionInfo ',
            '(og.goodsCnt) AS goodsCnt ',
            '(og.goodsWeight) AS goodsWeight ',
            '(og.goodsVolume) AS goodsVolume ',
            '(og.cateCd) AS cateCd ',
            '(og.goodsCnt) AS goodsCnt ',
            '(og.brandCd) AS brandCd ',
            '(og.makerNm) AS makerNm ',
            '(og.originNm) AS originNm ',
            '(og.addGoodsCnt) AS addGoodsCnt ',
            '(og.optionTextInfo) AS optionTextInfo ',
            '(og.goodsTaxInfo) AS goodsTaxInfo ',
            '(og.goodsPrice) AS goodsPrice ',
            '(og.fixedPrice) AS fixedPrice ',
            '(og.costPrice) AS costPrice ',
            '(og.commission) AS commission ',
            '(og.optionPrice) AS optionPrice ',
            '(og.optionCostPrice) AS optionCostPrice ',
            '(og.optionTextPrice) AS optionTextPrice ',
            '(og.invoiceCompanySno) AS invoiceCompanySno ',
            '(og.invoiceNo) AS invoiceNo ',
            '(og.deliveryCompleteDt) AS deliveryCompleteDt ',
            '(og.visitAddress) AS visitAddress ',
            'og.goodsDeliveryCollectFl',
            'og.deliveryMethodFl',
            'og.goodsNmStandard',
            'og.goodsMileage',
            'og.memberMileage',
            'og.couponGoodsMileage',
            'og.divisionUseDeposit',
            'og.divisionUseMileage',
            'og.divisionCouponOrderDcPrice',
            'og.goodsDcPrice',
            '(og.memberDcPrice+og.memberOverlapDcPrice+od.divisionMemberDeliveryDcPrice) as memberDcPrice',
            'og.memberDcPrice as orgMemberDcPrice',
            'og.memberOverlapDcPrice as orgMemberOverlapDcPrice',
            'og.goodsDiscountInfo',
            'og.myappDcPrice',
            '(og.couponGoodsDcPrice + og.divisionCouponOrderDcPrice) as couponGoodsDcPrice',
            '(og.goodsTaxInfo) AS addGoodsTaxInfo ',
            '(og.commission) AS addGoodsCommission ',
            '(og.goodsPrice) AS addGoodsPrice ',
            'og.timeSalePrice',
            'og.finishDt',
            'og.deliveryDt',
            'og.deliveryCompleteDt',
            'og.goodsType',
            'og.hscode',
            'og.checkoutData AS og_checkoutData',
            'og.enuri',
            'og.goodsRealPrice',//추가
            'oh.handleReason',
            'oh.handleDetailReason',
            'oh.refundMethod',
            'oh.refundBankName',
            'oh.refundAccountNumber',
            'oh.refundDepositor',
            'oh.refundPrice',
            'oh.refundDeliveryCharge',
            'oh.refundDeliveryInsuranceFee',
            'oh.refundUseDeposit',
            'oh.refundUseMileage',
            'oh.refundUseDepositCommission',
            'oh.refundUseMileageCommission',
            'oh.completeCashPrice',
            'oh.completePgPrice',
            'oh.completeCashPrice',
            'oh.completeDepositPrice',
            'oh.completeMileagePrice',
            'oh.refundCharge',
            'oh.refundUseDeposit',
            'oh.refundUseMileage',
            'oh.regDt as handleRegDt',
            'oh.handleDt',
            'od.deliveryCharge',
            'od.orderInfoSno',
            'od.deliveryPolicyCharge',
            'od.deliveryAreaCharge',
            'od.realTaxSupplyDeliveryCharge',
            'od.realTaxVatDeliveryCharge',
            'od.realTaxFreeDeliveryCharge',
            'od.divisionDeliveryUseMileage',
            'od.divisionDeliveryUseDeposit',
        ];
        if($searchData['statusMode'] === 'o'){
            // 입금대기리스트에서 '주문상품명' 을 입금대기 상태의 주문상품명만으로 노출시키기 위해 개수를 구함
            $arrInclude[] = 'SUM(IF(LEFT(og.orderStatus, 1)=\'o\', 1, 0)) AS noPay';
        }

        // join 문
        $join[] = ' LEFT JOIN ' . DB_ORDER . ' o ON o.orderNo = og.orderNo ';
        $join[] = ' LEFT JOIN ' . DB_ORDER_HANDLE . ' oh ON og.handleSno = oh.sno AND og.orderNo = oh.orderNo ';
        $join[] = ' LEFT JOIN ' . DB_ORDER_DELIVERY . ' od ON og.orderDeliverySno = od.sno ';
        $join[] = ' LEFT JOIN ' . DB_ORDER_INFO . ' oi ON (og.orderNo = oi.orderNo) 
                    AND (CASE WHEN od.orderInfoSno > 0 THEN od.orderInfoSno = oi.sno ELSE oi.orderInfoCd = 1 END)';

        //매입처
        if((($this->search['key'] =='all' && empty($this->search['keyword']) === false)  || $this->search['key'] =='pu.purchaseNm' || empty($excelField) === true || in_array("purchaseNm",array_values($excelField))) && gd_is_plus_shop(PLUSSHOP_CODE_PURCHASE) === true && gd_is_provider() === false) {
            $arrIncludePurchase =[
                'pu.purchaseNm'
            ];

            $arrInclude = array_merge($arrInclude, $arrIncludePurchase);
            $join[] = ' LEFT JOIN ' . DB_PURCHASE . ' pu ON og.purchaseNo = pu.purchaseNo ';
            unset($arrIncludePurchase);
        }

        //공급사
        if(in_array("scmNm",array_values($excelField)) || in_array("scmNo",array_values($excelField)) || empty($excelField) === true || empty($searchData['scmFl']) === false || ($searchData['key'] =='all' && $searchData['keyword'])) {
            $arrIncludeScm =[
                'sm.companyNm as scmNm'
            ];

            $arrInclude = array_merge($arrInclude, $arrIncludeScm);
            $join[] = ' LEFT JOIN ' . DB_SCM_MANAGE . ' sm ON og.scmNo = sm.scmNo ';
            unset($arrIncludeScm);
        }

        //회원
        if(in_array("memNo",array_values($excelField)) || in_array("memNm",array_values($excelField)) ||  in_array("groupNm",array_values($excelField)) || empty($excelField) === true || $searchData['memFl'] || ($searchData['key'] =='all' && $searchData['keyword'])) {
            $arrIncludeMember =[
                'IF(m.memNo > 0, m.memNm, oi.orderName) AS memNm',
                'm.memId',
                'mg.groupNm',
            ];

            $arrInclude = array_merge($arrInclude, $arrIncludeMember);
            $join[] = ' LEFT JOIN ' . DB_MEMBER . ' m ON o.memNo = m.memNo ';
            $join[] = ' LEFT OUTER JOIN ' . DB_MEMBER_GROUP . ' mg ON m.groupSno = mg.sno ';
            unset($arrIncludeMember);
        }

        //사은품
        if(in_array("oi.presentSno",array_values($excelField)) || empty($excelField) === true || in_array("ogi.giftNo",array_values($excelField))) {
            $arrIncludeGift =[
                'GROUP_CONCAT(ogi.presentSno SEPARATOR "/") AS presentSno ',
                'GROUP_CONCAT(ogi.giftNo SEPARATOR "/") AS giftNo '
            ];

            $arrInclude = array_merge($arrInclude, $arrIncludeGift);

            $join[] = ' LEFT JOIN ' . DB_ORDER_GIFT . ' ogi ON ogi.orderNo = o.orderNo ';
            unset($arrIncludeGift);
        }

        //상품 브랜드 코드 검색
        if(empty($this->search['brandCd']) === false || empty($excelField) === true || empty($this->search['brandNoneFl'])=== false) {
            $join[] = ' LEFT JOIN ' . DB_GOODS . ' as g ON og.goodsNo = g.goodsNo ';
        }

        //택배 예약 상태에 따른 검색
        if ($this->search['invoiceReserveFl']) {
            $join[] = ' LEFT JOIN ' . DB_ORDER_GODO_POST . ' ogp ON ogp.invoiceNo = og.invoiceNo ';
        }

        // 쿠폰검색시만 join
        if ($this->search['couponNo'] > 0) {
            $join[] = ' LEFT JOIN ' . DB_ORDER_COUPON . ' oc ON o.orderNo = oc.orderNo ';
            $join[] = ' LEFT JOIN ' . DB_MEMBER_COUPON . ' mc ON mc.memberCouponNo = oc.memberCouponNo ';
        }

        // 반품/교환/환불신청 사용에 따른 리스트 별도 처리 (조건은 검색 메서드 참고)
        if ($isUserHandle) {
            $arrIncludeOuh = [
                'count(ouh.sno) as totalClaimCnt',
                'userHandleReason',
                'userHandleDetailReason',
                'userRefundAccountNumber',
                'adminHandleReason',
                'ouh.regDt AS userHandleRegDt'
            ];
            $join[] = ' LEFT JOIN ' . DB_ORDER_USER_HANDLE . ' ouh ON og.userHandleSno = ouh.sno ';

            $arrInclude = array_merge($arrInclude, $arrIncludeOuh);
            unset($arrIncludeOuh);
        }
        // @kookoo135 고객 클레임 신청 주문 제외
        if ($this->search['userHandleViewFl'] == 'y') {
            $this->arrWhere[] = ' NOT EXISTS (SELECT 1 FROM ' . DB_ORDER_USER_HANDLE . ' WHERE (og.userHandleSno = sno OR og.sno = userHandleGoodsNo) AND userHandleFl = \'r\')';
        }

        // 현 페이지 결과
        if($page =='0') {
            $this->db->strField = 'og.orderNo';
            $this->db->strJoin = implode('', $join);
            $this->db->strWhere = implode(' AND ', gd_isset($this->arrWhere));
            if($orderType =='goods') $this->db->strGroup = "CONCAT(og.orderNo,og.orderCd,og.goodsNo)";
            else  $this->db->strGroup = "CONCAT(og.orderNo)";

            //총갯수관련
            $query = $this->db->query_complete();
            $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_ORDER_GOODS . ' og ' . implode(' ', $query);
            $result['totalCount'] = $this->db->query_fetch($strSQL, $this->arrBind);
        }

        $this->db->strField = implode(', ', $arrInclude).",totalGoodsPrice";
        $this->db->strJoin = implode('', $join);
        $this->db->strWhere = implode(' AND ', gd_isset($this->arrWhere));
        if($orderType =='goods') $this->db->strGroup = "CONCAT(og.orderNo,og.orderCd,og.goodsNo)";
        else  $this->db->strGroup = "CONCAT(og.orderNo)";
        $this->db->strOrder = $orderSort;
        if($pageLimit) $this->db->strLimit = (($page * $pageLimit)) . "," . $pageLimit;
        $query = $this->db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_ORDER_GOODS . ' og ' . implode(' ', $query);
        if(empty($excelField) === false) {
            if (Manager::isProvider()) {
                $result['orderList'] = $this->db->query_fetch($strSQL, $this->arrBind);
            }
            else {
                $result['orderList'] = $this->db->query_fetch_generator($strSQL, $this->arrBind);
            }
        }
        else {
            $result = $this->db->query_fetch($strSQL, $this->arrBind);
        }

        if (Manager::isProvider()) {
            $result = $this->getProviderTotalPriceExcelList($result, $orderType);
        }

        return $result;
    }

}