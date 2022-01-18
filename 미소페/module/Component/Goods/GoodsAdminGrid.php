<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright Copyright (c) 2017 GodoSoft.
 * @link http://www.godo.co.kr
 */
namespace Component\Goods;

use Component\Member\Manager;
use Component\Database\DBTableField;
use Session;
use Globals;
use Framework\Utility\ArrayUtils;


class GoodsAdminGrid extends \Bundle\Component\Goods\GoodsAdminGrid
{
  /**
	 * 페이지명을 기준으로 모드 반환
	 *
	 * @param string $viewType
	 *
	 * @return string $goodsGridMode
	 */
	public function getGoodsAdminGridMode($viewType)
	{
        $goodsGridMode = '';
        
		switch(\Request::getFileUri()){
			//상품리스트
			case 'goods_list.php' :
				$goodsGridMode = 'goods_list';
				break;

			//입금대기 리스트
			case 'goods_list_delete.php' :
				$goodsGridMode = 'goods_list_delete';
				break;

            //상품리스트
            case 'goods_register_option.php' :
                $goodsGridMode = 'goods_option_list';
                break;
                
            //상품리스트 //추가 goods_total_manager.php
            case 'goods_batch_stock.php' || 'goods_total_manager.php':
            $goodsGridMode = 'goods_batch_stock_list';
            break;
		}

		return $goodsGridMode;
	}
}
