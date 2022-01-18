<?php
namespace Component\Wib;

/**
 * 고도몰 기본쿼리를 편하게 사용할수 있도록 작업
 * select, update, insert 가능 [ 필요할때마다 기능 추가중 ]
 */
class WibSql 
{
    
    protected $db = null;
    
    public $joinTotal;
    
    public function __construct() {  
        if (!is_object($this->db)) {
            $this->db = \App::load('DB');
        }
    }
    
    public function WibNobind($wql)
    {
        $result = $this->db->query($wql);
        return $this->db->fetch($result);
    }
}