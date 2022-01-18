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
 * @link      http://www.godo.co.kr
 */
namespace Controller\Front\Order;

use Component\Cart\Cart;
use Request;

/**
 * 주문서 작성
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class OrderController extends \Bundle\Controller\Front\Order\OrderController
{
    public function index()
    {
        // 모듈 설정
        $cart = new Cart();
        
        // 선택된 상품만 주문서 상품으로
        if (Request::get()->has('cartIdx')) {
            $cartIdx = $cart->getOrderSelect(Request::get()->get('cartIdx'));
            // 장바구니 정보 (최상위 로드)
            $cartInfo = $cart->getCartGoodsData($cartIdx, null, null, false, true);
        }else{
            $cartIdx = 'nn';
            $cartInfo = '';
        }
        
        $data = new \DateTime('now');
        
        $firstDate = clone $data;
        $firstDate->modify('first day of'); // 조회하는 월의 1일
        
        $lastDate = clone $data;
        $lastDate->modify('last day of');   // 조회하는 월의 마지막일
        
        $firstWeek = new \DateTime($firstDate->format('Y-m-d'));
        $firstWeek->modify('last Sunday');  // 조회하는 월 1일이 있는 주의 일요일
        
        $lastWeek = new \DateTime($lastDate->format('Y-m-d'));
        $lastWeek->modify('next Saturday'); // 조회하는 월 마지막일이 있는 주의 토요일
        
        $firstWeekJ = $firstWeek->modify('-1 days')->format('j');
        $firstWeekT = $firstWeek->format('t');
        $this->arrStamp[] = null;
        for ($i = $firstWeekJ; $i < $firstWeekT; $i++) {
            $firstWeek->modify('+1 day');
            $this->arrStamp[] = [
                'date'  => $firstWeek->format('Y-m-d'),
                'day'   => $firstWeek->format('j'),
                //'class' => $nextMonthCss,
            ];
        }
        $lastDateT = $lastDate->format('t');
        $firstDate->modify('-1 days');

        for ($i = 1; $i <= $lastDateT; $i++) {
            $firstDate->modify('+1 day');
            $ymd = $firstDate->format('Y-m-d');
            $this->arrStamp[] = [
                'date'  => $ymd,
                'day'   => $firstDate->format('j'),
               // 'class' => (is_array($this->history) && in_array($ymd, $this->history)) ? $attendCss : '',
            ];
        }
        $lastWeekJ = $lastWeek->format('j');
        $lastWeek->modify('first day of');
        $lastWeek->modify('-1 days');
        for ($i = 1; $i <= $lastWeekJ; $i++) {
            $lastWeek->modify('+1 day');
            $this->arrStamp[] = [
                'date'  => $lastWeek->format('Y-m-d'),
                'day'   => $lastWeek->format('j'),
                //'class' => $nextMonthCss,
            ];
        }
        
        print_r($data);
        echo '<br>';
        print_r($firstDate);
        echo '<br>';
        print_r($lastDateT);
        echo '<br>';
        print_r($firstWeek);
        echo '<br>';
        print_r($lastWeek);
        
        echo '<br>';
        print_r($this->arrStamp);
        
        parent::index();
    }
}