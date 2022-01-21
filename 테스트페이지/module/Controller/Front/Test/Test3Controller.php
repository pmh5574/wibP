<?php
namespace Controller\Front\Test;

use Globals;
use Session;
use Response;
use Request;

class Test3Controller extends \Controller\Front\Controller
{
    protected $db = null;

    public function index()
    {
        if (!is_object($this->db)) {
            $this->db = \App::load('DB');
        }
        $setData = '회원';
        $this->setData('setData', $setData);


        //db에서호출
        $data = $this->dbSelect($memNo);
        // print_r($data);
        $this->setData('data', $data[0]);
    }

    public function dbSelect()
    {
        $strSQL = "SELECT memNo FROM es_member WHERE memNo <= 5";
        $data = $this->db->query_fetch($strSQL, null);
        return $data;
    }
}