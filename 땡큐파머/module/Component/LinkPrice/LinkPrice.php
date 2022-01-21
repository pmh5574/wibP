<?php

namespace Component\LinkPrice;

use App;
use Cookie;
use Request;
use Component\Database\DBTableField;

/**
* 링크프라이스 관련 
*
* @package Component\LinkPrice
* @author webnmoblie
*/ 
class LinkPrice 
{
	private $db;
	
	public function __construct()
	{
		$this->db = App::load(\DB::class);
	}
	
	/**
	* 링크프라이스 LpInfo 쿠키 정보 추출 
	*
	* @return String 링크프라이스 lpinfo 값 
	*/ 
	public function getLpInfo()
	{
		return Cookie::get("LPINFO");
	}
	
	public function get($orderNo = null)
	{
		if ($orderNo) {
			$sql = "SELECT * FROM lpinfo WHERE order_id = ? ORDER BY id";
			$list = $this->db->query_fetch($sql, ["i", $orderNo]);
		}
		
		return gd_isset($list, []);
	}
	
	/**
	* 링크프라이스 실적 전송 
	*
	* @param Integer $orderNo 주문번호 
	* 
	* @return Boolean true 전송성송, false 전송 실패 
	*/
	public function sent($orderNo = null)
	{
		if ($orderNo) {
			$lpinfo = $this->get($orderNo);
			if ($lpinfo[0]['isSent'])
				return false;

			$order = App::load(\Component\Order\OrderAdmin::class);
			$sql = "SELECT COUNT(*) as cnt FROM lpinfo WHERE order_id = ?";
			$row = $this->db->query_fetch($sql, ["i", $orderNo], false);
			$orderView = $order->getOrderView($orderNo);
			$orderStatus = substr($orderView['orderStatus'], 0, 1);
			if (!in_array($orderStatus, ["o", "p", "g", "d", "s"]))
				return false;
			
			$items = [];
			if ($orderView['goods']) {
				foreach ($orderView['goods'] as $values) {
					foreach ($values as $value) {
						foreach ($value as $v) {
							$items[] = $v;
						} // endforeach 
					} // endforeach 
				} // endforeach 
			} // endif 
			
			if (gd_isset($items)) {
				if (empty($row['cnt'])) {
					$device_type = ($orderView['orderTypeFl'] == 'mobile')?"web-mobile":"web-pc";
					
					$server = Request::server()->toArray();
					foreach ($items as $it) {
						$row = $this->db->query_fetch("SELECT lpinfo FROM " . DB_ORDER_GOODS . " WHERE sno = ?", ["i", $it['sno']], false);
						$data = [
							'order_id' => $orderNo,
							'product_id' => $it['goodsNo'],
							'lpinfo' => $row['lpinfo'],
							'user_agent' => $server['HTTP_USER_AGENT'],
							'ip' => $orderView['orderIp'],
							'device_type' => $device_type,
						];
							
						$arrBind = $this->db->get_binding(DBTableField::tableWmLpinfo(), $data, "insert", array_keys($data));
						$this->db->set_insert_db("lpinfo", $arrBind['param'], $arrBind['bind'], "y");
					}
				} // endif 
				
				
				$gap = $orderView['totalGoodsPrice'] + $orderView['totalDeliveryCharge'] - $orderView['totalRealSettlePrice'];
				
				$params = [
					'order' => [
						'order_id' => $orderNo,
						'final_paid_price' => (Integer)($orderView['totalRealSettlePrice'] - $orderView['totalDeliveryCharge']),
						'currency' => 'KRW',
						'user_name' => $orderView['orderName'],
					],
					'products' => [],
					'linkprice' => [
						'merchant_id' => 'thankyoufm',
						'lpinfo' => $lpinfo[0]['lpinfo'],
						'user_agent' => $lpinfo[0]['user_agent'],
						'remote_addr' => $lpinfo[0]['ip'],
						'device_type' => $lpinfo[0]['device_type'],
					],
				];
				
				$leftOver = 0;
				$discountEach = $gap / count($items);
				$products = [];
				foreach ($items as $it) {
					$goodsPrice = (($it['goodsPrice'] + $it['addGoodsPrice'] + $it['optionPrice']) - ($it['goodsDcPrice'] + $it['memberDcPrice'] + $it['memberOverlapDcPrice'] + $it['myappDcPrice'])) * $it['goodsCnt'];
					
					$goodsPrice = $goodsPrice - ($discountEach + $leftOver);
					
					if ($goodsPrice < 0) {
						$leftOver = $goodsPrice * -1;
 						$goodsPrice = 0;
					} else {
						$leftOver = 0;
					}
					$sql = "SELECT c.cateCd, c.cateNm FROM " . DB_GOODS_LINK_CATEGORY . " AS glc 
								INNER JOIN " . DB_CATEGORY_GOODS . " AS c ON glc.cateCd = c.cateCd WHERE glc.goodsNo = ? ORDER BY glc.cateCd";

					$cateCd = $cateNm = [];
					$list = $this->db->query_fetch($sql, ["i", $it['goodsNo']]);
					if ($list) {
						foreach ($list as $li) {
							$cateCd[] = $li['cateCd'];
							$cateNm[] = $li['cateNm'];
						} // endforeach 
					} // endif 
					
					$paid_at = ($it['regDt'] && $it['regDt'] != '0000-00-00 00:00:00')?date("c", strtotime($it['regDt'])):"";
					$canceled_at = ($it['cancelDt'] && $it['cancelDt'] != '0000-00-00 00:00:00')?date("c", strtotime($it['cancelDt'])):"";
					if ($it['handleMode'] == 'r') 
						$canceled_at = ($it['handleDt'] && $it['handleDt'] != '0000-00-00 00:00:00')?date("c", strtotime($it['handleDt'])):"";
					
					$confirmed_at = ($it['deliveryCompleteDt'] && $it['deliveryCompleteDt'] != '0000-00-00 00:00:00')?date("c", strtotime($it['deliveryCompleteDt'])):"";
					
					$goodsNm = $it['goodsNm'];
					if ($it['optionInfo']) {
						$opts = [];
						foreach ($it['optionInfo'] as $op) {
							$opts[] ="[".$op['optionName'] .":".$op['optionValue']."]";
						}
						
						$goodsNm .= implode("", $opts);
					}
					
					$cateCd = $cateCd?$cateCd:["9999"];
					$products[] = [
						'product_id' => $it['goodsNo']."_".$it['optionSno'],
						'product_name' => $goodsNm,
						'category_code' => implode(",", $cateCd),
						'category_name' => $cateNm?$cateNm:"추가상품",
						'quantity' => $it['goodsCnt'],
						'product_final_price' => (Integer)$goodsPrice,
						'paid_at' => $paid_at,
						'confirmed_at' => $confirmed_at,
						'canceled_at' => $canceled_at,
					];
				}
				$params['products'] = $products;
				$client = new \GuzzleHttp\Client();
				$options = [
					'headers' => [
						'Content-Type' => 'application/json',
						'Charset' => 'utf-8',
					],
					'body' => json_encode($params),
					//'json' => $params,
				];
				
				$response = $client->request('POST', "https://service.linkprice.com/lppurchase_cps_v4.php", $options);
				
				$result = (String)$response->getBody();
				$result = json_decode($result, true);
				if ($result[0]['is_success']) { // 실적 전송 성공시 
					$param = [
						'isSent = ?',
					];
					
					$bind = [
						'is',
						1, 
						$orderNo,
					];
					
					$this->db->set_update_db("lpinfo", $param, "order_id = ?", $bind);
					return true;
				}
			} // endif 
		} // endif 
	}
	
	/**
	* 실적 주문목록 
	*
	* return Array
	*/
	
	public function getOrderList()
	{
		$in = Request::request()->all();
		$param = $bind = $orderList = [];
		if ($in['paid_ymd']) {
			$date = date("Y-m-d", strtotime($in['paid_ymd']));
			$param[] = "og.regDt LIKE ?";
			$this->db->bind_param_push($bind, "s", $date."%");
		}
		
		if ($in['confirmed_ymd']) {
			$date = date("Y-m-d", strtotime($in['confirmed_ymd']));
			$param[] = "og.deliveryCompleteDt LIKE ?";
			$this->db->bind_param_push($bind, "s", $date."%");
		}
		
		if ($in['canceled_ymd']) {
			$date = date("Y-m-d", strtotime($in['canceled_ymd']));
			$param[] = "(og.cancelDt LIKE ? OR oh.handleDt LIKE ?)";
			$this->db->bind_param_push($bind, "s", $date."%");
			$this->db->bind_param_push($bind, "s", $date."%");
		}
		$conds = $param?" WHERE ". implode(" AND ", $param):"";
			
	
		$sql = "SELECT DISTINCT(a.order_id) as orderNo FROM lpinfo AS a 
						INNER JOIN " . DB_ORDER . " AS o ON a.order_id = o.orderNo 
						INNER JOIN " . DB_ORDER_GOODS . " AS og ON a.order_id = og.orderNo 
						LEFT JOIN " . DB_ORDER_HANDLE . " AS oh ON a.order_id = oh.orderNo 
						{$conds} ORDER BY a.regDt"; 
	
		$tmp = $this->db->query_fetch($sql, $bind);
		if ($tmp) {
			$order = App::load(\Component\Order\OrderAdmin::class);
			foreach ($tmp as $t) {			
				$orderView = $order->getOrderView($t['orderNo']);
				
				$lpinfo = $this->get($t['orderNo']);
				$items = [];
				if ($orderView['goods']) {
					foreach ($orderView['goods'] as $values) {
						foreach ($values as $value) {
							foreach ($value as $v) {
								$items[] = $v;
							} // endforeach 
						} // endforeach 
					} // endforeach 
				} // endif 	
				
				if (($orderView['settlePrice'] == 0 && $orderView['dashBoardPrice']['cancelPrice'] > 0) || ($orderView['orderStatus'] == 'o1' && $orderView['dashBoardPrice']['cancelPrice'] > 0))  {
					$orderView['settlePrice'] = $orderView['dashBoardPrice']['cancelPrice'];	
					$deliveryCharge = [];
					$totalGoodsPrice = 0;
					
					foreach ($items as $it) {
						$deliveryCharge[$it['deliverySno']] = $it['refundDeliveryCharge'];
						$totalGoodsPrice += (($it['goodsPrice'] + $it['optionPrice']+ $it['optionTextPrice']) * $it['goodsCnt']) + $it['addGoodsPrice'];
					}
				
					$orderView['totalGoodsPrice'] = $totalGoodsPrice;
					$orderView['totalDeliveryCharge'] = array_sum($deliveryCharge);
					
				}
				
				$gap = $orderView['totalGoodsPrice'] + $orderView['totalDeliveryCharge'] - $orderView['settlePrice'];
				$params = [
					'order' => [
						'order_id' => $t['orderNo'],
						'final_paid_price' => (Integer)($orderView['settlePrice'] - $orderView['totalDeliveryCharge']),
						'currency' => 'KRW',
						'user_name' => $orderView['orderName'],
					],
					'products' => [],
					'linkprice' => [
						'merchant_id' => 'thankyoufm', 
						'lpinfo' => $lpinfo[0]['lpinfo'],
						'user_agent' => $lpinfo[0]['user_agent'],
						'remote_addr' => $lpinfo[0]['ip'],
						'device_type' => $lpinfo[0]['device_type'],
					],
				];
				
				
				$leftOver = $gap;
				$products = [];
				$total = 0;
		
				foreach ($items as $k => $it) {
					$goodsPrice = (($it['goodsPrice'] + $it['addGoodsPrice'] + $it['optionPrice']) - ($it['goodsDcPrice'] + $it['memberDcPrice'] + $it['memberOverlapDcPrice'] + $it['myappDcPrice'])) * $it['goodsCnt'];
					
					$rate = $goodsPrice / $orderView['totalGoodsPrice'];
					$discountEach = floor($gap * $rate);
					$leftOver -= $discountEach;
					
					if ($k == count($items) - 1) {
						$discountEach += $leftOver;
					}
					
					$discountEach = (Integer)$discountEach;
					$goodsPrice = $goodsPrice - $discountEach;
					
					
					if ($goodsPrice < 0)
 						$goodsPrice = 0;
					
					
					$sql = "SELECT c.cateCd, c.cateNm FROM " . DB_GOODS_LINK_CATEGORY . " AS glc 
								INNER JOIN " . DB_CATEGORY_GOODS . " AS c ON glc.cateCd = c.cateCd WHERE glc.goodsNo = ? ORDER BY glc.cateCd";

					$cateCd = $cateNm = [];
					$list = $this->db->query_fetch($sql, ["i", $it['goodsNo']]);
					if ($list) {
						foreach ($list as $li) {
							$cateCd[] = $li['cateCd'];
							$cateNm[] = $li['cateNm'];
						} // endforeach 
					} // endif 
					
					$paid_at = ($it['regDt'] && $it['regDt'] != '0000-00-00 00:00:00')?date("c", strtotime($it['regDt'])):"";
					$canceled_at = ($it['cancelDt'] && $it['cancelDt'] != '0000-00-00 00:00:00')?date("c", strtotime($it['cancelDt'])):"";
					if ($it['handleMode'] == 'r') 
						$canceled_at = ($it['handleDt'] && $it['handleDt'] != '0000-00-00 00:00:00')?date("c", strtotime($it['handleDt'])):"";
					
					$confirmed_at = ($it['deliveryCompleteDt'] && $it['deliveryCompleteDt'] != '0000-00-00 00:00:00')?date("c", strtotime($it['deliveryCompleteDt'])):"";
					
					$goodsNm = $it['goodsNm'];
					if ($it['optionInfo']) {
						$opts = [];
						foreach ($it['optionInfo'] as $op) {
							$opts[] ="[".$op['optionName'] .":".$op['optionValue']."]";
						}
						
						$goodsNm .= implode("", $opts);
					}
					$cateCd = $cateCd?$cateCd:["9999"];
					$products[] = [
						'product_id' => $it['goodsNo']."_".$it['optionSno'],
						'product_name' => $goodsNm,
						'category_code' => implode(",", $cateCd),
						'category_name' => $cateNm?$cateNm:"추가상품",
						'quantity' => $it['goodsCnt'],
						'product_final_price' => (Integer)$goodsPrice,
						'paid_at' => $paid_at,
						'confirmed_at' => $confirmed_at,
						'canceled_at' => $canceled_at,
					];
				}
				
				$params['products'] = $products;
				$orderList[] = $params;
			} // endforeach 
			
		} // endif 

		return $orderList;
	}
	
	/**
	* 링크프라이스 일괄 전송처리 
	* 
	* @return 
	*/
	public function updateAll()
	{
		$sql = "SELECT DISTINCT(orderNo), lpinfo  FROM " . DB_ORDER_GOODS . " WHERE lpinfo <> '' ORDER BY regDt DESC LIMIT 0, 50";
		$res = $this->db->query($sql);
		while ($row = $this->db->fetch($res)) {
			$orderNo = $row['orderNo'];
			$this->sent($orderNo);
		}
	}
}