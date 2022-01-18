<?php

namespace Controller\Admin\Promotion;

use Request;

class WibCouponDetailController extends \Controller\Admin\Controller
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
        // 쿠폰코드
        $code = Request::get()->get('psno');
            
        $code_SQL = "SELECT * FROM es_couponPaper WHERE psno = '" . $code . "' ";
        $data = $this->db->query_fetch($code_SQL);
        
        
        $this->setData('data', $data);
    }
}
