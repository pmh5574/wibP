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
namespace Controller\Admin\Promotion;

use Component\Coupon\CouponAdmin;
use Component\Sms\SmsSender;
use Framework\Debug\Exception\LayerNotReloadException;
use Message;
use Request;

class CouponPsController extends \Bundle\Controller\Admin\Promotion\CouponPsController
{
    protected $db = null;
    
    public function __construct() {
        
        parent::__construct();
        
        if (!is_object($this->db)) {
            $this->db = \App::load('DB');
        }
    }
    
    public function index()
    {
        parent::index();
    }
}