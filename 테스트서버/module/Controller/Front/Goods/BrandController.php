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
namespace Controller\Front\Goods;

use Framework\Debug\Exception\AlertBackException;
use Framework\Debug\Exception\AlertOnlyException;
use Framework\Debug\Exception\Framework\Debug\Exception;
use Message;
use Globals;
use Request;
use Cookie;

class BrandController extends \Controller\Front\Controller
{

    /**
     * 상품목록
     *
     * @author artherot, sunny
     * @version 1.0
     * @since 1.0
     * @copyright Copyright (c), Godosoft
     * @throws Except
     */
    public function index()
    {
        // 모듈 설정
        $goods = \App::load('\\Component\\Goods\\Goods');
        $brand = \App::load('\\Component\\Category\\Brand');
		$this->db = \App::load("DB");
        try {
			//2020-01-30 데이터 있는 값만 출력 HG 추가 			
			$BrandSQL = "select distinct(left(cateNm,1)) as c from es_categoryBrand WHERE cateDisplayFl='y' ORDER BY cateNm ASC";
			$result = $this->db->query($BrandSQL);			
			$englis_alphabet = [];
			foreach($result as $v){ 
				$english_alphabet[] = $v['c']; 
			}
            $kored_alphabet = array('ㄱ','ㄴ','ㄷ','ㄹ','ㅁ','ㅂ','ㅅ','ㅇ','ㅈ','ㅊ','ㅋ','ㅌ','ㅍ','ㅎ');

            $this->setData('english_alphabet', $english_alphabet);
            $this->setData('korea_alphabet', $kored_alphabet);
            $this->setData('brand', strtoupper(Request::get()->get('brand')));

        //echo "<pre>";
        //print_r($english_alphabet);
        //exit;

        } catch (\Exception $e) {
            // echo ($e->ectMessage);
            // 설정 오류 : 화면 출력용
            if ($e->ectName == 'ERROR_VIEW') {
                $item = ($e->ectMessage ? ' - ' . str_replace('\n', ' - ', $e->ectMessage, $e->ectMessage) : '');
                if ($e->ectMessage == 'NOT_ACCESS_CATEGORY') {
                    $return = '/';
                } else {
                    $return = -1;
                }
                throw new AlertOnlyException(__('안내') . $item);

                // 시스템 오류 : 실패 메시지만 보여주고 자세한 내용은 log 참고
            } else {
                $e->actLog();
                throw new AlertBackException(__('오류') . ' - ' . __('오류가 발생 하였습니다.'));
            }
        }


    }
}