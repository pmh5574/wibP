<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Controller\Front\Frame;

use Request;
use Session;
use Component\Database\DBTableField;
use Component\GoodsStatistics\GoodsStatistics;
use Component\Wib\WibSql;

/**
 * Description of CartPsController
 *
 * @author user
 */
class CartPsController extends \Controller\Front\Controller{
    
    public $db;
    public $isWrite;
    
    public function __construct() {
        parent::__construct();
        
        if (!is_object($this->db)) {
            $this->db = \App::load('DB');
        }
    }
    
    public function index(){
        
        $arrData = Request::post()->toArray();
        $server = Session::all();

        $arrData['goodsCnt'] = 1;
        $arrData['mallSno'] = 1;
        $arrData['siteKey'] = $server['siteKey'];
        
        $arrBind = $this->db->get_binding(DBTableField::tableCart(), $arrData, 'insert');
        $this->db->set_insert_db('es_cart', $arrBind['param'], $arrBind['bind'], 'y');
        $cartSno = $this->db->insert_id();

        // 텍스트 옵션 추가
        $arrBindt = [];
        $inlot = "INSERT INTO es_goodsOptionText SET goodsNo = ?, optionName = ?, mustFl = ?, addPrice = ?, inputLimit = ?, regDt = ?";
        $this->db->bind_param_push($arrBindt, 'i', '1');
        $this->db->bind_param_push($arrBindt, 's', '프레임');
        $this->db->bind_param_push($arrBindt, 's', 'n');
        $this->db->bind_param_push($arrBindt, 'i', $arrData['price']);
        $this->db->bind_param_push($arrBindt, 'i', '1');
        $this->db->bind_param_push($arrBindt, 's', date('Y-m-d H:i:s'));
        $this->db->bind_query($inlot, $arrBindt);
        $textSno = $this->db->insert_id();
        
        $optionText = [
            $textSno => 'addOption'
        ];
        
        $arrBindev = [];
        $strSQLev = "UPDATE es_cart SET optionText = ?, imgPath = ? WHERE sno = ?";
        $this->db->bind_param_push($arrBindev, 's', json_encode($optionText));
        $this->db->bind_param_push($arrBindev, 's', $arrData['img']);
        $this->db->bind_param_push($arrBindev, 'i', $cartSno);
        $result = $this->db->bind_query($strSQLev, $arrBindev); 
        
        echo $cartSno;
        exit;
        
    }
}
