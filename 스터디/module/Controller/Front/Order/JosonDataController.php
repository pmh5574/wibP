<?php
namespace Controller\Front\Order;

use Request;
use Component\Wib\WibSql;

class josonDataController extends \Controller\Front\Controller{
    
    public $db;
    
    public function index()
    {
//        $now = date();
//        $strbe = date('Y-m-d H:i:s', strtotime($now. '-3 month'));
//        echo $strbe.'<br>';
//        
//        $str = "2017-11-08 15:49:02";
//        echo strtotime($str).'<br>';
//        
//        $str2 = "1510123742";
//        echo date('Y-m-d H:i:s', $str2);

        $this->db = new WibSql();
        
        $page = Request::get()->get('page');
        $curlpage = ($page)?$page:1;
        
        $url = "https://www.testgut.com/mall/moveGodo/orderExcel.php?page={$curlpage}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        //echo count($result);
        $data = json_decode($result);
        
        // 주문배열
        $listArray = $this->orderArray();
        // 상품배열
        $goodsarray = $this->goodsArray();
        
        //$this->dataInsert($listArray, $data);
        // 주문데이터 입력
        $this->dataInsert($listArray, $data);
        
        // 주문정보 입력
        
        // 배송정보 입력
        
        // 핸들러 입력
        
        $count = count($data);
        $setcount = ($count >= 200)?$count:0;
        //echo $data;
        //print_r($data);
        $this->setData('page', $curlpage);
        $this->setData('count', $setcount);
        $this->setData('realCount', $count);
    }
    
    public function dataInsert($array, $data)
    {
        $g_payment_array = array("","카드결제","온라인입금","실시간계좌이체","핸드폰결제","가상계좌이체","PAYCO신용카드","토스페이","PAYCO간편계좌","카카오페이");
        
        $memarrs = [];
        $sql = [
            'es_member',
            ['memNo', 'memId', 'memNm']
        ];
        $wibconn = $this->db->WibQuery($sql);
        foreach ($wibconn as $mValue) {
            $memarrs[$mValue['memId']] = $mValue['memNo'];
        }
        //print_r($memarrs);
        foreach ($data as $value) {
            $indata = [];
            $paydate = ''; // 결제완료일
            $delidate = ''; // 배송완료일
            $finishdate = ''; // 구매확정일
            //배열정보
            
            foreach ($array as $arval) {
                
                $vatPrice = intval($value->buyer_price)*0.1;
                $taxPrice = intval($value->buyer_price) - intval($vatPrice);
                $pgType = $g_payment_array[$value->buyer_payment];
                
                $data = isset($value->{$arval[3]})?$value->{$arval[3]}:'';
                
                switch ($arval[2]){
                    
                    case 'orderStatus':
                        $delitype = array('0'=>'o1', '1'=>'o1', '2'=>'p1', '3'=>'g1', '4'=>'g4', '5'=>'d1', '7'=>'s1', '8'=>'c1', '9'=>'b4', '10'=>'c4', '11'=>'r3');
                        $data = $delitype[$value->buyer_state];
                        if($delitype != 'o1'){
                            $paydate = ($value->pay_date)?date('Y-m-d H:i:s', $value->pay_date):'';
                            $delidate = ($value->pantos_delivery_date)?date('Y-m-d H:i:s', $value->pantos_delivery_date):'';
                            $finishdate = ($value->delivery_date)?date('Y-m-d H:i:s', $value->delivery_date):'';
                        }
                    break;
                    
                    case 'memNo':
                        $data = $memarrs[$value->buyer_id];
                        //echo '아이디 : '.$data;
                    break;
                
                    case 'taxSupplyPrice':
                    case 'taxSupplyPrice':
                        $data = $taxPrice;
                    break;
                
                    case 'taxVatPrice':
                    case 'realTaxVatPrice':
                        $data = $vatPrice;
                    break;
                
                    case 'pgRealTaxSupplyPrice':
                        if($pgType == '카드결제' || $pgType = 'PAYCO신용카드'){
                            $data = $taxPrice;
                        }
                    break;
                    
                    case 'pgRealTaxVatPrice':
                        if($pgType == '카드결제' || $pgType = 'PAYCO신용카드'){
                            $data = $vatPrice;
                        }
                    break;
                
                    case 'totalGoodsPrice':
                        $data = intval($value->buyer_price) - intval($value->buyer_delivery);
                    break;
                
                    case 'totalDeliveryWeight':
                        $data = floatval($value->real_kg) * 1000;
                    break;
                                
                    case 'pgAppDt':
                    case 'regDt':
                        $data = ($data)?date('Y-m-d H:i:s', $data):'';
                    break;
                
                    case 'receiptFl':
                        $data = ($value->buyer_receipt > 0)?'y':'n';
                    break;
                
                    case 'settleKind':
                        switch ($pgType) {
                            case '카드결제': 
                            case 'PAYCO신용카드':
                                $data = 'pc'; 
                            break;
                            case '온라인입금': $data = 'gb'; break;
                            case '실시간계좌이체': 
                            case '토스페이': 
                            case 'PAYCO간편계좌': 
                            case '카카오페이':
                                $data = 'pb'; 
                            break;
                            case '가상계좌이체': $data = 'pv'; break;
                        }
                    break;
                            
                }
                
                if($arval[4]){
                    $data = $arval[4];
                }
                
                //echo $arval[0].' : '.$data.'<br>';
                
                if($data){
                    $indata[$arval[2]] = array($data, $arval[1]);
                }
                
            }
            
            //print_r($indata);
            // 데이터 저장
            $this->db->Wibinsert(array(
                'es_order',
                $indata
            ));
            
            // 상품주문 리스트
            $goodArray = $this->goodsArray();
            $this->goodsInsert($goodArray, $value, $paydate, $delidate, $finishdate);
            
            // 주문정보 리스트
            $orderinfoArray = $this->orderinfoArray();
            $this->infoInsert($orderinfoArray, $value);
        }
    }
    
    public function goodsInsert($arr, $val, $paydate, $delidate, $finishdate)
    {
        
        $orderNum = 1;
        foreach ($val->goodslist as $value) {
            $indata = [];
             // 고도 상품 정보
            $goodssql = "select goodsNo, goodsCd, cateCd, goodsPrice, goodsWeight, originNm from es_goods where tegut_code = '{$value->purchase_goods_id}' ";
            $goodsresult = $this->db->WibAll($goodssql);
            $goodsInfo = $goodsresult[0];
            
            $vatPrice = floatval($value->purchase_price)*0.1;
            $taxPrice = floatval($value->purchase_price) - floatval($vatPrice);
            
            foreach ($arr as $arval) {
                
                $data = isset($value->{$arval[3]})?$value->{$arval[3]}:'';
                
                switch ($arval[2]) {
                    
                    case 'orderNo':
                        $data = $val->buyer_sess;
                    break;
                    
                    case 'orderCd':
                        $data = $orderNum;
                    break;
                
                    case 'orderStatus':
                        $delitype = array('0'=>'o1', '1'=>'o1', '2'=>'p1', '3'=>'g1', '4'=>'g4', '5'=>'d1', '7'=>'s1', '8'=>'c1', '9'=>'b4', '10'=>'c4', '11'=>'r3');
                        $data = $delitype[$val->buyer_state];
                    break;
                
                    case 'invoiceNo':
                        $data = $val->buyer_delivery_num;
                    break;
                
                    case 'goodsNo':
                        $data = $goodsInfo['goodsNo'];
                    break;
                    
                    case 'goodsCd':
                        $data = $goodsInfo['goodsCd'];
                    break;
                
                    case 'goodsWeight': 
                        $data = floatval($goodsInfo['goodsWeight'])*1000;
                    break;
                
                    case 'taxSupplyGoodsPrice':
                    case 'realTaxSupplyGoodsPrice':
                        $data = $taxPrice;
                    break;
                
                    case 'taxVatGoodsPrice':
                    case 'realTaxVatGoodsPrice':
                        $data = $vatPrice;
                    break;
                
                    case 'fixedPrice':
                    case 'costPrice':
                        $data = $goodsInfo['goodsPrice'];
                    break;
                
                    case 'cateCd':
                        $data = $goodsInfo['cateCd'];
                    break;
                
                    case 'originNm':
                        $data = $goodsInfo['originNm'];
                    break;
                
                    case 'visitAddress':
                        $data = $val->buyer_address2;
                    break;
                
                    case 'regDt':
                    case 'modDt':
                        $data = ($data)?date('Y-m-d H:i:s', $data):'';
                    break;
                    
                    // 결제완료 상품일경우 결제완료일 입력
                    case 'paymentDt':
                        if($paydate != ''){
                            $data = $paydate;
                        }
                    break;
                    
                    // 배송완료 제품일경우 완료일 입력 
                    case 'deliveryCompleteDt':
                        if($paydate != ''){
                            $data = $delidate;
                        }
                    break;
                    
                    // 구매확정일자~  구입완료(정산) 상품일경우 해당일 입력
                    case 'finishDt':
                        if($paydate != ''){
                            $data = $finishdate;
                        }
                    break;
                    
                    // 취소일경우 취소일 입력 ( 사용자취소 및 관리자취소 포함
                    case 'cancelDt':
                        if($val->buyer_state == '8' || $val->buyer_state == '11'){
                            $data = $val->update_date;
                        }else{
                            $data = '';
                        }
                    break;
                    
                }
                
                if($arval[4] || $arval[4] == '0'){
                    $data = $arval[4];
                }
                
                if($data){
                    $indata[$arval[2]] = array($data, $arval[1]);
                }
                //echo $arval[0].' : '.$data.'<br>';
            }
            //echo '111111111111111111111111111111111<br><br>';
            $orderNum++;
            
            // 데이터 저장
            if($taxPrice > 0){
                $this->db->Wibinsert(array(
                    'es_orderGoods',
                    $indata
                ));
            }
            // 사은품일경우 추가작업필요
            
        }
    }
    
    public function infoInsert($arr, $parent)
    {
        $indata = [];
        foreach ($arr as $arval) {
            $data = isset($parent->{$arval[3]})?$parent->{$arval[3]}:'';

            if($arval[4] || $arval[4] == '0'){
                $data = $arval[4];
            }
            
            switch ($arval[2]){
                case 'regDt':
                    $data = ($data)?date('Y-m-d H:i:s', $data):'';
                break;
            }
            
            if($data){
                $indata[$arval[2]] = array($data, $arval[1]);
            }
            //echo $arval[0].' : '.$data.'<br>';
        }
        $this->db->Wibinsert(array(
            'es_orderInfo',
            $indata
        ));
    }
    
    // 주문정보 테이블 배열정보
    public function orderinfoArray()
    {
        $list = array(
            array('주문번호', 's', 'orderNo', 'buyer_sess'),
            array('주문자 이름', 's', 'orderName', 'buyer_name1'),
            array('주문자 e-mail', 's', 'orderEmail', 'buyer_email'),
            array('주문자 전화번호 국가코드', 's', 'orderPhonePrefixCode', '', 'kr'),
            array('주문자 전화번호 국가번호', 'i', 'orderPhonePrefix', '', '82'),
            array('주문자 전화번호', 's', 'orderPhone', 'buyer_tel1'),
            array('주문자 휴대폰 국가코드', 's', 'orderCellPhonePrefixCode', '', 'kr'),
            array('주문자 휴대폰 국가번호', 'i', 'orderCellPhonePrefix', '', '82'),
            array('주문자 핸드폰 번호', 's', 'orderCellPhone', 'buyer_tel2'),
            array('주문자 우편번호', 's', 'orderZipcode', 'buyer_zip1'),
            array('주문자 우편번호(10자리)', 's', 'orderZonecode', 'buyer_zip1'),
            array('주문인 주/지방/지역', 's', 'orderState'),
            array('주문인 도시', 's', 'orderCity'),
            array('주문자 주소', 's', 'orderAddress', 'buyer_address1'),
            array('주문자 나머지 주소', 's', 'orderAddressSub', '', '.'),
            array('수취인 이름', 's', 'receiverName', 'buyer_name3'),
            array('수취인 국가 코드', 's', 'receiverCountryCode', '', 'kr'),
            array('수취인 전화번호 국가코드', 's', 'receiverPhonePrefixCode', '', 'kr'),
            array('수취인 전화번호 국가번호', 'i', 'receiverPhonePrefix', '', '82'),
            array('수취인 전화번호', 's', 'receiverPhone', 'buyer_tel3'),
            array('수취인 휴대폰 국가코드', 's', 'receiverCellPhonePrefixCode', '', 'kr'),
            array('수취인 핸드폰 국가번호', 'i', 'receiverCellPhonePrefix', '', '82'),
            array('수취인 핸드폰 번호', 's', 'receiverCellPhone', 'buyer_tel4'),
            array('수취인 안심번호 사용여부 (n:미사용, y:사용, w:발급대기, c:사용해지)', 's', 'receiverUseSafeNumberFl', '', 'n'),
            array('수취인 안심번호', 's', 'receiverSafeNumber'),
            array('수취인 안심번호 발급일자', 's', 'receiverSafeNumberDt'),
            array('수취인 우편번호', 's', 'receiverZipcode', 'buyer_zip2'),
            array('수취인 우편번호(10자리)', 's', 'receiverZonecode'),
            array('receiverCountry', 's', 'receiverCountry'),
            array('수취인 주/지방/지역', 's', 'receiverState'),
            array('수취인 도시', 's', 'receiverCity'),
            array('수취인 주소', 's', 'receiverAddress', 'buyer_address2'),
            array('수취인 나머지 주소', 's', 'receiverAddressSub', '', '.'),
            array('배송방법(y:방문배송, n:방문배송제외, a:방문배송포함)', 's', 'deliveryVisit', '', 'n'),
            array('방문 수령지 주소', 's', 'visitAddress'),
            array('방문자명', 's', 'visitName'),
            array('방문자연락처', 's', 'visitPhone'),
            array('방문수령 메모', 's', 'visitMemo'),
            array('개인통관고유번호 (해외상품 구매용)', 's', 'customIdNumber'), // 업데이트 처리
            array('주문시 남기는글', 's', 'orderMemo'), // 업데이트 처리
            array('묶음배송코드', 's', 'packetCode'),
            array('배송지순서', 's', 'orderInfoCd', '', '1'),
            array('등록일', 's', 'regDt', 'register_date'),
            array('수정일', 's', 'modDt', 'update_date'),
            array('y: 비회원마케팅동의함 n: 동의안함,회원', 's', 'smsFl', '', 'n')
        );
        
        return $list;
    }
    
    // 상품테이블 배열정보
    public function goodsArray()
    {
        $list = array(
            array('주문번호', 's', 'orderNo', 'purchase_sess'),
            array('상점 고유번호', 'i', 'mallSno', '', '1'),
            array('외부채널품목고유번호', 's', 'apiOrderGoodsNo', '', '1'),
            array('주문 코드(순서)', 'i', 'orderCd', 'orderCd'),
            array('수량별 부분취소시 그룹 코드', 'i', 'orderGroupCd', '', '0'),
            array('사용자 처리 코드 (SNO)', 'i', 'userHandleSno', '', '0'),
            array('환불/반품/교환 처리 SNO', 'i', 'handleSno', '0'), // 환불/반품/교환 테이블은 업데이트 형식으로 진행예정
            array('기획전 일련번호', 'i', 'eventSno', '', '0'),
            array('주문 상태', 's', 'orderStatus', 'parentStatus'), // 주문테이블 주문상태 저장
            array('배송테이블 sno', 's', 'orderDeliverySno', 'orderDeliverySno', '0'), // 베송테이블은 업데이트 형식으로 진행예정
            array('택배사 SNO', 'i', 'invoiceCompanySno', '', '29'),
            array('송장번호', 's', 'invoiceNo', 'buyer_delivery_num'), // 부모값에서 검색
            array('SCM ID', 'i', 'scmNo', '', '1'),
            array('매입처 고유번호', 'i', 'purchaseNo', '', '0'),
            array('공급사 수수료율', 'i', 'commission', '', '0.00'),
            array('공급사 정산 고유 번호', 'i', 'scmAdjustNo', '', '0'),
            array('공급사 정산 후 환불의 정산 고유 번호', 'i', 'scmAdjustAfterNo', '', '0'),
            array('주문상품종류', 's', 'goodsType', '', 'goods'),
            array('타임세일 구매 여부', 's', 'timeSaleFl', '', 'n'),
            array('추가상품 종속성 여부', 's', 'parentMustFl', '', 'n'),
            array('추가상품의 부모상품', 'i', 'parentGoodsNo', '', '0'),
            array('상품 번호', 'i', 'goodsNo', 'goodsNo'), // 상품검색후 진행해야함
            array('상품 코드', 's', 'goodsCd', '테굿데이터'), // 자체상품코드
            array('모델명', 's', 'goodsModelNo'),
            array('상품명', 's', 'goodsNm', 'purchase_subject'),
            array('기준몰 상품명', 's', 'goodsNmStandard', 'purchase_subject'),
            array('상품 무게', 'i', 'goodsWeight', '테굿데이터'), // 상품검색후 진행해야함
            array('상품 수량', 'i', 'goodsCnt', 'purchase_num'),
            array('상품 가격', 'i', 'goodsPrice', 'purchase_price'),
            array('복합과세 상품 공급가', 'i', 'taxSupplyGoodsPrice', 'taxSupplyGoodsPrice'),
            array('복합과세 상품 부가세', 'i', 'taxVatGoodsPrice', 'taxVatGoodsPrice'),
            array('복합과세 상품 면세', 'i', 'taxFreeGoodsPrice', '', '0.00'),
            array('실제 남아있는 복합과세 상품 공급가', 'i', 'realTaxSupplyGoodsPrice', 'realTaxSupplyGoodsPrice'),
            array('실제 남아있는 복합과세 상품 부가세', 'i', 'realTaxVatGoodsPrice', 'realTaxVatGoodsPrice'),
            array('실제 남아있는 복합과세 상품 면세', 'i', 'realTaxFreeGoodsPrice', '', '0.00'),
            array('주문할인 금액의 안분된 예치금', 'i', 'divisionUseDeposit', '', '0.00'),
            array('주문할인 금액의 안분된 마일리지', 'i', 'divisionUseMileage', '', '0.00'),
            array('주문할인 금액의 안분된 배송비 예치금', 'i', 'divisionGoodsDeliveryUseDeposit', '', '0.00'),
            array('주문할인 금액의 안분된 배송비 마일리지', 'i', 'divisionGoodsDeliveryUseMileage', '', '0.00'),
            array('주문할인 금액의 안분된 주문쿠폰', 'i', 'divisionCouponOrderDcPrice', '', '0.00'),
            array('주문할인 금액의 안분된 주문쿠폰', 'i', 'divisionCouponOrderMileage', '', '0.00'),
            array('추가 상품 갯수', 'i', 'addGoodsCnt', '', '0'),
            array('추가 상품 금액', 'i', 'addGoodsPrice', '', '0'),
            array('추가 상품 금액', 'i', 'optionPrice', '', '0.00'),
            array('옵션 매입가', 'i', 'optionCostPrice', '', '0.00'),
            array('텍스트 옵션 금액', 'i', 'optionTextPrice', '', '0.00'),
            array('정가', 'i', 'fixedPrice', '테굿데이터'), // 상품검색후 진행해야함
            array('매입가', 'i', 'costPrice', '테굿데이터'), // 상품검색후 진행해야함
            array('상품 할인 금액 (상품에만 적용)', 'i', 'goodsDcPrice', '', '0.00'),
            array('회원 할인 금액 (추가상품 제외)', 'i', 'memberDcPrice', '', '0.00'),
            array('회원 그룹중복 할인 금액 (추가상품 제외)', 'i', 'memberOverlapDcPrice', '', '0.00'),
            array('상품쿠폰 할인 금액 (추가상품 제외)', 'i', 'couponGoodsDcPrice', '', '0.00'),
            array('타임세일 할인 금액 (상품에만 적용)', 'i', 'timeSalePrice', '', '0.00'),
            array('브랜드 무통장결제 세일 할인 금액 (상품에만 적용)', 'i', 'brandBankSalePrice', '', '0.00'),
            array('마이앱 할인 금액 (추가상품 제외)', 'i', 'myappDcPrice', '', '0.00'),
            array('상품별 착불시 발생된 배송비', 'i', 'goodsDeliveryCollectPrice', '', '0.00'),
            array('상품 적립마일리지 (추가상품 제외)', 'i', 'goodsMileage', '', '0.00'),
            array('회원 적립마일리지 (추가상품 제외)', 'i', 'memberMileage', '', '0.00'),
            array('상품쿠폰 적립 마일리지 (1/n) (추가상품 제외)', 'i', 'couponGoodsMileage', '', '0.00'),
            array('상품별배송비 결제방법 (pre - 선불, later - 착불)', 's', 'goodsDeliveryCollectFl', '', 'pre'),
            array('마일리지 차감 여부', 's', 'minusDepositFl', '', 'n'),
            array('복원 여부 (적립 적립금)', 's', 'minusRestoreDepositFl', '', 'n'),
            array('사용 마일리지 차감 여부', 's', 'minusMileageFl', '', 'n'),
            array('사용 마일리지 복원 여부', 's', 'minusRestoreMileageFl', '', 'n'),
            array('적립 마일리지 지급 여부', 's', 'plusMileageFl', '', 'n'),
            array('적립 마일리지 복원 여부', 's', 'plusRestoreMileageFl', '', 'n'),
            array('차감 여부 (재고)', 's', 'minusStockFl', '', 'n'),
            array('복원 여부 (재고)', 's', 'minusRestoreStockFl', '', 'n'),
            array('상품옵션 일련번호', 'i', 'optionSno'),
            array('옵션 정보', 's', 'optionInfo'),
            array('텍스트 옵션 정보', 's', 'optionTextInfo'),
            array('상품 부가세 정보', 's', 'goodsTaxInfo', '', 't^|^10.0'),
            array('카테고리 코드', 's', 'cateCd', 'purchase_category'),
            array('상품에 연결된 전체 카테고리 코드', 's', 'cateAllCd'),
            array('브랜드 코드', 's', 'brandCd'),
            array('제조사', 's', 'makerNm'), // 상품검색후 진행해야함
            array('원산지', 's', 'originNm', 'tg_originNm'), // 상품검색후 진행해야함
            array('hscode', 's', 'hscode'),
            array('배송 관련 로그', 's', 'deliveryLog'),
            array('취소완료일자', 's', 'cancelDt', 'update_date'),
            array('입금일자', 's', 'paymentDt', 'pay_date'),
            array('송장번호 등록일', 's', 'invoiceDt'),
            array('배송일자', 's', 'deliveryDt'),
            array('배송완료일자', 's', 'deliveryCompleteDt', 'pantos_delivery_date'),
            array('구매확정일자', 's', 'finishDt', 'delivery_date'),
            array('마일리지 지급 유예에 따른 실 지급일', 's', 'mileageGiveDt'),
            array('간편구매 추가데이터', 's', 'checkoutData'),
            array('주문/매출 통계 처리 상태', 's', 'statisticsOrderFl'),
            array('상품 통계 처리 상태', 's', 'statisticsGoodsFl'),
            array('문자발송여부', 's', 'sendSmsFl'),
            array('배송방식', 's', 'deliveryMethodFl', '', 'delivery'),
            array('에누리', 'i', 'enuri'),
            array('주문당시상품할인정보', 's', 'goodsDiscountInfo'),
            array('주문당시상품적립정보', 's', 'goodsMileageAddInfo'),
            array('외부 인입 플랫폼', 's', 'inflow'),
            array('메인 상품 진열에서 장바구니 담은 정보', 's', 'linkMainTheme'),
            array('방문 수령지 주소', 's', 'visitAddress', 'parent'), // 부모값에서 검색
            array('등록일', 's', 'regDt', 'register_date'),
            array('수정일', 's', 'modDt', 'register_date'),
            array('상품 용량', 'i', 'goodsVolume', '', '0.00'),
            array('쿠폰으로 적립되는 마일리지 품목별 지급현황', 's', 'couponMileageFl', '', 'n'),
        );
        return $list;
    }
    
    
    // 주문테이블 배열정보
    public function orderArray()
    {
        $list = array(
            array('주문번호', 's', 'orderNo', 'buyer_sess'),
            array('타채널주문번호', 's', 'apiOrderNo', ''),
            array('상점번호', 'i', 'mallSno', 'buyer_sess', '1'),
            array('회원번호', 'i', 'memNo', 'wibMemId'),
            array('주문상태', 's', 'orderStatus', 'buyer_state'),
            array('주문자IP', 's', 'orderIp', 'buyer_ip_address'),
            array('주문채널', 's', 'orderChannelFl', 'buyer_sess', 'shop'),
            array('주문유형', 's', 'orderTypeFl', 'mobile_shop'),
            array('앱주문시휴대폰OS', 's', 'appOs', ''),
            array('앱주문시 푸시코드', 's', 'pushCode', ''),
            array('주문건수 앱 통계 처리상태', 's', 'statisticsAppOrderCntFl', 'buyer_sess', 'n'),
            array('비회원 이메일', 's', 'orderEmail', 'noIdMail'),
            array('주문 상품명', 's', 'orderGoodsNm', 'purchase_subject'),
            array('주문 상품명 기준몰', 's', 'orderGoodsNmStandard', 'purchase_subject'),
            array('주문 상품 갯수', 'i', 'orderGoodsCnt', 'purchase_cnt'),
            array('총 주문 금액', 'i', 'settlePrice', 'buyer_price'),
            array('해외PG승인금액적용환율코드', 's', 'overseasSettleCurrency', 'buyer_sess', 'KRW'),
            array('해외PG승인금액(환율변환적용)', 'i', 'overseasSettlePrice'),
            array('최초 총 과세 금액', 'i', 'taxSupplyPrice', 'taxSupplyPrice'),
            array('최초 총 부가세 금액', 'i', 'taxVatPrice', 'taxVatPrice'),
            array('최초 총 면세 금액', 'i', 'taxFreePrice', 'taxFreePrice', '0.00'),
            array('최초 총 과세금액(환불제외)', 'i', 'realTaxSupplyPrice', 'realTaxSupplyPrice'),
            array('실제 총 부가세(환불제외)', 'i', 'realTaxVatPrice', 'realTaxVatPrice'),
            array('실제 총 면세 금액(환불제외)', 'i', 'realTaxFreePrice', '', '0.00'),
            array('주문시 사용한 마일리지', 'i', 'useMileage', 'buyer_use_point'),
            array('주문시 사용한 예치금', 'i', 'useDeposit', 'buyer_sess', '0.00'),
            array('총 상품 금액', 'i', 'totalGoodsPrice', 'buyer_price'),
            array('총 배송비', 'i', 'totalDeliveryCharge', 'buyer_delivery'),
            array('해외배송 EMS 보험료', 'i', 'totalDeliveryInsuranceFee', '', '0.00'),
            array('총 상품 할인 금액', 'i', 'totalGoodsDcPrice', '', '0.00'),
            array('총 회원 할인 금액', 'i', 'totalMemberDcPrice', '', '0.00'),
            array('총 회원등급 브랜드 무통장 할인 금액', 'i', 'totalMemberBankDcPrice', '', '0.00'),
            array('총 그룹별 회원 중복 할인금액', 'i', 'totalMemberOverlapDcPrice',  '', '0.00'),
            array('회원 배송비 무료', 'i', 'totalMemberDeliveryDcPrice', '', '0.00'),
            array('총 상품 쿠폰 할인 금액', 'i', 'totalCouponGoodsDcPrice', '', '0.00'),
            array('총 주문 쿠폰 할인 금액', 'i', 'totalCouponOrderDcPrice', 'coupon_use_price'),
            array('총 배송 쿠폰 할인 금액', 'i', 'totalCouponDeliveryDcPrice', '', '0.00'),
            array('총 마이앱 할인 금액', 'i', 'totalMyappDcPrice', '', '0.00'),
            array('총 적립 마일리지', 'i', 'totalMileage', 'buyer_add_point'),
            array('총 상품 적립 마일리지', 'i', 'totalGoodsMileage', '', '0.00'),
            array('총 회원 적립 마일리지', 'i', 'totalMemberMileage', '', '0.00'),
            array('총 상품쿠폰 적립 마일리지', 'i', 'totalCouponGoodsMileage', '', '0.00'),
            array('총 주문쿠폰 적립 마일리지', 'i', 'totalCouponOrderMileage', '', '0.00'),
            array('총 운영자추가할인', 'i', 'totalEnuriDcPrice', 'buyer_sess', '0.00'),
            array('적립금 지급 예외', 's', 'mileageGiveExclude', 'buyer_sess', 'y'),
            array('배송 총 무게', 'i', 'totalDeliveryWeight', 'real_kg'),
            array('첫 구매 여부', 's', 'firstSaleFl', '', 'n'),
            array('첫구매 쿠폰 지급 여부', 's', 'firstCouponFl', 'buyer_sess', 'n'),
            array('구매 쿠폰 지급 여부', 's', 'eventCouponFl', 'buyer_sess', 'n'),
            array('메일 전송/SMS 전송 여부', 's', 'sendMailSmsFl', '<root><mail_ORDER>y</mail_ORDER><mail_INCASH>n</mail_INCASH><mail_DELIVERY>n</mail_DELIVERY><sms_ORDER>y</sms_ORDER><sms_INCASH>n</sms_INCASH><sms_ACCOUNT>y</sms_ACCOUNT><sms_DELIVERY>n</sms_DELIVERY><sms_INVOICE_CODE>n</sms_INVOICE_CODE><sms_DELIVERY_COMPLETED>n</sms_DELIVERY_COMPLETED><sms_CANCEL>n</sms_CANCEL><sms_REPAY>n</sms_REPAY><sms_REPAYPART>n</sms_REPAYPART><sms_SOLD_OUT>n</sms_SOLD_OUT></root>'),
            array('주문방법', 's', 'settleKind', 'buyer_payment'),
            array('무통장 입금 은행', 's', 'bankAccount', 'bankAccount'),
            array('무통장 입금자', 's', 'bankSender', 'buyer_name2'),
            array('영수증 신청여부', 's', 'receiptFl', 'buyer_receipt'),
            array('예치금 정책', 's', 'depositPolicy'),
            array('마일리지 정책', 's', 'mileagePolicy'),
            array('주문상태 정책', 's', 'statusPolicy'),
            array('주문상시의 회원등급별 할인정책', 's', 'memberPolicy'),
            array('주문상시의 쿠폰 기본정책', 's', 'couponPolicy'),
            array('주문상시의 상점통화 기본정책', 's', 'currencyPolicy'),
            array('주문당시의 환율 기본정책', 's', 'exchangeRatePolicy'),
            array('주문당시의 마이앱 기본정책', 's', 'myappPolicy'),
            array('고객상담메모(관리자용)', 's', 'buyer_admin_memo'),
            array('고객상담메모(관리자용)', 's', 'buyer_admin_memo'),
            array('관리자 메모', 'adminMemo', 's', 'buyer_admin_memo'),
            array('주문PG로그', 's', 'orderPGLog'),
            array('주문 배송 로그', 's', 'orderDeliveryLog'),
            array('주문 관리자 로그', 's', 'orderAdminLog'),
            array('PG명', 's', 'pgName', '', 'kcp'),
            array('PG 결과코드', 's', 'pgResultCode', 'pgResultCode', '00'),
            array('PG 거래번호', 's', 'pgTid', 'pgTid', '^|^'),
            array('PG 승인번호', 's', 'pgAppNo', 'buyer_card_oknum'),
            array('PG 승인일자', 's', 'pgAppDt', 'pay_date'),
            array('PG 승인카드코드', 's', 'pgCardCd'),
            array('PG 가상계좌 입금은행', 's', 'pgSettleNm'),
            array('PG 가상계좌 입금일자', 's', 'pgSettleCd', 'approval_date'),
            array('PG 실패 이유', 's', 'pgFailReason'), // 자세한 확인필요
            array('PG 취소여부', 's', 'pgCancelFl'), // 자세한 확인필요
            array('실제 총 PG 과세금액', 'i', 'pgRealTaxSupplyPrice', 'pgRealTaxSupplyPrice'),
            array('실제 총 PG 부가세', 'i', 'pgRealTaxVatPrice', 'pgRealTaxVatPrice'),
            array('실제 총 PG 면세금액', 'i', 'pgRealTaxFreePrice', '', '0.00'),
            array('에스크로 전문번호', 's', 'escrowSendNo'),
            array('에스크로 배송등록 여부', 's', 'escrowDeliveryFl', 'buyer_sess', 'n'),
            array('에스크로 배송등록 확인일자', 's', 'escrowDeliveryDt'),
            array('에스크로 배송업체', 's', 'escrowDeliveryCd'),
            array('에스크로 송장번호', 's', 'escrowInvoiceNo'),
            array('에스크로 구매확인', 's', 'escrowConfirmFl'),
            array('에스크로 거절확인', 's', 'escrowDenyFl', '', 'n'),
            array('간편결제 추가데이터', 's', 'fintechData'),
            array('간편구매 추가데이터', 's', 'checkoutData'),
            array('주문데이터 checksum 코드', 's', 'checksumData'),
            array('주문추가필드', 's', 'addField'),
            array('뱅크다 일련번호', 's', 'bankdaManualNo'),
            array('자동입금수동여부', 's', 'bankdaManualFl'),
            array('자동입금수동처리관리자아이디', 's', 'bankdaManualMangerId'),
            array('입금일자 (통계에서만사용)', 's', 'paymentDt'),
            array('복수배송지사용여부', 's', 'multiShippingFl', 'buyer_sess', 'n'),
            array('페이코 쇼핑 트래킹키', 's', 'trackingKey', 'payco_trackingKey'),
            array('주문 환불/반품/교환/ 자동승인처리여부', 's', 'userHandleProcess', '', 'n'),
            array('등록일', 's', 'regDt', 'register_date'),
            array('수정일', 's', 'modDt', 'update_date'),
            array('차지백여부', 's', 'pgChargeBack', 'buyer_sess', 'n'),
            array('페이스북 픽셀 쿠키값', 's', 'fbPixelKey')
            
        );
        
        return $list;
    }
}
