<?php
namespace Controller\Front\Member;

use Request;
use Component\Wib\WibSql;

class josonDataController extends \Controller\Front\Controller{
    
    public $db;
    
    public function index()
    {
        $this->db = new WibSql();
        
        $page = Request::get()->get('page');
        $curlpage = ($page)?$page:1;
        
        $url = "https://www.testgut.com/mall/moveGodo/memExcel.php?page={$curlpage}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        
        $data = json_decode($result);
        //echo count($data);
        //exit;
        //print_r($data);
        //exit;
        // 회원 배열
        $listArray = $this->memberArray();

        // 회원데이터 입력
        $this->dataInsert($listArray, $data);
      
        $count = count($data);
        $setcount = ($count >= 10000)?$count:0;
        $setcount = 0;
        //echo $data;
        //print_r($data);
        $this->setData('page', $curlpage);
        $this->setData('count', $setcount);
        $this->setData('realCount', $count);
        
        exit;
    }

    public function dataInsert($array, $data)
    {
        $grType = array('3' => '1', '4' => '2', '5' => '3');
        
        foreach ($data as $value) {
            $snsType = false;
            $snsName = '';
            $snsNum = '';
            $snsDate = '';
            
            $arrData = [];
            $snsData = [];
            //배열정보
            foreach ($array as $arval) {
                
                $indata = isset($value->{$arval[3]})?$value->{$arval[3]}:'';
                
                switch ($arval[2]){
                    
                    case 'groupSno':
                        $indata = $grType[$value->member_class];
                    break;
                
                    case 'sexFl':
                        $indata = ($value->member_sex == '1')?'m':'w';
                    break; 
                
                    case 'calendarFl':
                        $indata = ($value->birth_lunar == '0')?'s':'l';
                    break;
                
                    case 'regDt':
                    case 'entryDt':
                        $indata = ($value->register_date)?date('Y-m-d H:i:s', $value->register_date):'';
                    break;
                        
                    case 'modDt':
                        $indata = ($value->modify_date)?date('Y-m-d H:i:s', $value->modify_date):'';
                    break;
                
                    case 'lastSaleDt':
                        $indata = ($value->last_buy_date)?date('Y-m-d H:i:s', $value->last_buy_date):'';
                    break;
                
                    case 'lastLoginDt':
                        $indata = ($value->lastlogin)?date('Y-m-d H:i:s', $value->lastlogin):'';
                    break;
                
                    case 'smsFl':
                        $indata = ($value->member_autosms == '0')?'n':'y';
                    break;
                    case 'maillingFl':
                        $indata = ($value->member_automail == '0')?'n':'y';
                    break;
                
                    case 'memPw':
                        $indata = ($value->member_pass != '')?$value->member_pass:'';
                        //echo $value->member_name.'<br>';
                        //7fccc1d993711f450682273acd694b29
                        //echo $data.'<br>';
                    break;
                }
                
                if($arval[4]){
                    $indata = $arval[4];
                }
                
                if($indata){
                    $arrData[$arval[2]] = array($indata, $arval[1]);
                }
                
                if($value->login_type != ''){
                    $snsType = true;
                    $snsName = $value->login_type;
                    $snsNum = $value->fbaccesstoken;
                    $snsDate = date('Y-m-d H:i:s', $value->register_date);
                }
                
            }
            print_r($arrData);
            //exit;
            // 데이터 저장
            $lastId = $this->db->Wibinsert(array(
                'es_member',
                $arrData
            ));
            //echo '<br>'.$lastId.'<br>';
            if($snsType){
                $snsData = [
                    'mallSno' => ['1', 'i'],
                    'memNo' => [$lastId, 's'],
                    'appId' => ['godo', 's'],
                    'snsTypeFl' => [$snsName, 's'],
                    'snsJoinFl' => ['y', 's'],
                    'uuid' => [$snsNum, 's'],
                    'regDt' => [$snsDate, 's'],
                    'modDt' => [$snsDate, 's'],
                    'connectFl' => ['y', 's']
                ];
                $this->db->Wibinsert(array(
                    'es_memberSns',
                    $snsData
                ));
            }
            
            
            
            
        }
        
    }
    
    // 주문정보 테이블 배열정보
    public function memberArray()
    {
        $list = array(
            array('상점번호', 'i', 'mallSno', '', '1') , 
            array('아이디', 's', 'memId', 'member_id'), 
            array('회원등급sno', 'i', 'groupSno', 'member_class'), 
            array('등급수정일', 's', 'groupModDt', '', '0000-00-00 00:00:00'), 
            array('등급유효일', 's', 'groupValidDt', '', '0000-00-00 00:00:00'), 
            array('이름', 's', 'memNm', 'member_name'), 
            array('닉네임', 's', 'nickNm'), 
            array('비밀번호(암호화문자)', 's', 'memPw', 'member_pass'), 
            array('가입승인', 's', 'appFl', '', 'y'), 
            array('성별', 's', 'sexFl', 'member_sex'), // 추가 작업 필요
            array('생일', 's', 'birthDt', 'member_birthday'), // substr 작업 필요
            array('양력,음력', 's', 'calendarFl', 'birth_lunar'), // 추가 작업 필요
            array('이메일', 's', 'email', 'member_email'), 
            array('우편번호', 's', 'zonecode', 'member_zip'), 
            array('주소', 's', 'address', 'member_address'), 
            array('상세주소', 's', 'addressSub', 'member_address_de'), 
            array('전화번호', 's', 'phone'), 
            array('휴대폰', 's', 'cellPhone', 'member_tel1'), 
            array('팩스번호', 's', 'fax'), 
            array('회사명', 's', 'company', 'member_jobname1'), 
            array('업태', 's', 'service'), 
            array('종목', 's', 'item'), 
            array('사업자번호', 's', 'business_no'), 
            array('대표자명', 's', 'ceo_name'), 
            array('사업장우편번호', 's', 'comZonecode'), 
            array('사업장주소', 's', 'comAddress'), 
            array('사업장상세주소', 's', 'comAddressSub'), 
            array('마일리지', 's', 'mileage', 'realPoint'), 
            array('예치금', 's', 'deposit'), 
            array('메일수신동의', 's', 'maillingFl', 'member_automail'), 
            array('SMS수신동의', 's', 'smsFl', 'member_autosms'), 
            array('결혼여부', 's', 'marriFl', 'member_marriage', 'n'), 
            array('결혼기념일', 's', 'marriDate', 'member_marriageday'), 
            array('직업', 's', 'job', 'member_job'), 
            array('관심분야', 's', 'interest', 'member_interest'),
            array('재가입여부', 's', 'reEntryFl', 're_entry_fl'), 
            array('회원가입일', 's', 'entryDt', 'register_date'), 
            array('가입경로', 's', 'entryPath'), 
            array('최종로그인', 's', 'lastLoginDt', 'last_login_dt'), 
            array('최종로그인IP', 's', 'lastLoginIp', 'modify_ip'),
            array('최종구매일', 's', 'lastSaleDt', 'last_buy_date'), 
            array('로그인횟수', 'i', 'loginCnt', 'member_login'),
            array('상품주문건수', 'i', 'saleCnt', 'orderCnt'),
            array('총 주문금액', 's', 'saleAmt', 'totalPrice'),
            array('남기는말', 's', 'memo', 'member_introduction'), 
            array('추천인ID', 's', 'recommId', 'member_recommend'), 
            array('추천인아이디등록여부', 's', 'recommFl', 'member_recommend_fl'), 
            array('추가1', 's', 'ex1'), 
            array('추가2', 's', 'ex2'), 
            array('추가3', 's', 'ex3'), 
            array('추가4', 's', 'ex4'), 
            array('추가5', 's', 'ex5'), 
            array('추가6', 's', 'ex6'), 
            array('개인정보 수집 및 이용 필수', 's', 'privateApprovalFl', 'private_approval_fl', 'y'), 
            array('개인정보 수집 및 이용 선택', 's', 'privateApprovalOptionFl', 'private_approval_option_fl', 'y'), 
            array('개인정보동의 제3자 제공', 's', 'privateOfferFl', 'private_offer_fl', 'y'),
            array('개인정보동의 취급업무 위탁', 's', 'privateConsignFl', 'private_consign_fl', 'y'),
            array('내외국인구분', 's', 'foreigner', 'foreigner', '1'), 
            array('본인확인 중복가입확인정보', 's', 'dupeinfo'), 
            array('성인여부', 's', 'adultFl'), 
            array('성인여부인증시간', 's', 'adultConfirmDt'), 
            array('본인확인 번호', 's', 'pakey'), 
            array('본인확인 방법', 's', 'rncheck', 'rncheck', 'none'), 
            array('관리자 메모', 's', 'adminMemo', 'member_admin_memo'), 
            array('휴면회원여부', 's', 'sleepFl', 'human_member', 'n'), 
            array('휴면회원전환안내메일발송여부', 'sleepMailFl', 'sleep_mail_fl', 'n'), 
            array('개인정보유효기간', 's', 'expirationFl', 'expiration_fl', '1'), 
            array('등록일', 's', 'regDt', 'register_date'), 
            array('수정일', 's', 'modDt', 'register_date'), 
            array('통관번호', 's', 'certinumber', 'certinumber'),
            array('로그인 제한', 's', 'loginLimit', '', 'null')
        );
        
        return $list;
    }
}
