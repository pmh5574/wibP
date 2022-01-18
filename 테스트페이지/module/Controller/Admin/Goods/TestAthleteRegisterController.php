<?php
namespace Controller\Admin\Goods;

use Request;

class TestAthleteRegisterController extends \Controller\Admin\Controller
{

    public function index()    
    {
        // --- 메뉴 설정
		if(Request::get()->get('delFl') === 'y') {
			$this->callMenu('goods', 'goods', 'delete_list');
		} else {
			$this->callMenu('goods', 'goods', 'list');
		}
        $this->db = \App::load('DB');

        $sno = Request::get()->get('sno');
        $mode = Request::get()->get('mode');

        if($mode == 'save'){
  
        }
        if($mode == 'modify'){
            $strSQL = "SELECT * FROM es_athlete WHERE sno = ".$sno;
            $result = $this->db->query_fetch($strSQL, null);
            
            $this->setData("result", $result);
        }

        if($mode == 'delete'){
            $query = "DELETE FROM es_athlete WHERE sno = ?";
            $this->db->bind_param_push($arrBind, 'i', $sno);
            $this->db->bind_query($query, $arrBind);
            echo "<script>alert('삭제 완료'); location.href='../goods/test_athlete.php'; </script>";exit;
        }
                
        $this->setData('sno', $sno);
    }
}