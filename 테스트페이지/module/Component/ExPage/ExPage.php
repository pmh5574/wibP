<?php
namespace Component\ExPage;

use Request;

/**
 * Pagination
 */
 
class ExPage
{
    // 전역변수 선언
    public $print_pre = null;
    public $first_page = null;
    public $print_next = null;
    public $end_page = null;

    protected $db = null;
    protected $total_page = null;
    protected $req_page = null;

    public function __construct()
    {
        if(!is_object($this->db)){
            $this->db = \App::load("DB");
        }
        $this->req_page = Request::get()->get('page');
    }

    /**
     * sql table count 함수
     * 
     * $max_result table count query 결과값
     */
    public function getCount()
    {
        $max_strSQL  = "SELECT COUNT(*) cnt FROM es_goods"; // count 쿼리문 입력
        $max_result = $this->db->query_fetch($max_strSQL, null); // count 쿼리문 출력
        $vide_page = $max_result[0]['cnt']/10; // 한 페이지에 n개씩 표시하기 위한 총 페이지수

        $this->total_page = ceil($vide_page); // $vide_page 소수 값 올림

        return $max_result;
    }

    /**
     * limit query 문 출력
     * 게시물 출력 함수
     * @param string field1 field2 -> limit 변수
     */

    public function putSelect($max_result)
    {
        $strSQL  ="SELECT goodsNo, goodsNm FROM es_goods ORDER BY goodsNo DESC limit ?,? ";
        $field2 = 10; 
        // 페이지에 표시할 게시물 수
        // 요청 페이지에 맞는 데이터 n개 잘라서 보내기
        // limit 두번째 변수

        if($this->req_page){
            // @param req_page = 1, limit = 0 , 10
            // limit 첫번째 변수
            $field1 = ($this->req_page-1)*10; 
                
            
            $this->db->bind_param_push($arrBind, 'i', $field1);        
            $this->db->bind_param_push($arrBind, 'i', $field2);    

            $result = $this->db->query_fetch($strSQL , $arrBind, true);
            $pcnt = $max_result[0]['cnt'] - $field1; //

            foreach($result as $key => $value){
                $result[$key]['no'] = $pcnt--;
            }
            
            return $result;
        }
    }

    /**
     * 페이지 목록 함수
     * @param int 
     */

    public function putAtag()
    {
        $start_page = ceil($this->req_page/3); // 페이지 목록 시작 번호
        $page_range = $start_page*3;    // 페이지 목록 범위 끝 번호
        if($start_page >1){
            $start_page = $page_range-2;
        }

        // 페이지 목록 갯수
        // html 페이지 a tag 1~n개 구해서 태그 넘기기
        for($i=$start_page; $i<=$page_range; $i++){
            $pre = $this->req_page-3;
            $next = $start_page+3;
            if(3<$this->req_page){
                $this->print_pre = "<a href='ex_page.php?page=$pre'>pre</a>";
                $this->first_page = "<a href='ex_page.php?page=1'>맨 앞</a>";
            }

            if($i>=$this->total_page){
                $print_page[] = "<a href='ex_page.php?page=$i'>$i</a>";
                break;
            }

            if($i<$this->total_page){
                $print_page[] = "<a href='ex_page.php?page=$i'>$i</a>";
            }
            
            if($i%3==0){
                $this->print_next = "<a href='ex_page.php?page=$next'>next</a>";
                $this->end_page = "<a href='ex_page.php?page=$this->total_page'>맨 뒤</a>";

            }
        }
        return $print_page;
    }
}

