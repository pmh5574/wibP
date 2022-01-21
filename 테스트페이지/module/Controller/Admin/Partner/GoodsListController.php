<?php
namespace Controller\Admin\Partner;

use Request;


class GoodsListController extends \Controller\Admin\Controller
{
    protected $db = null;
    public function index()
    {
        try {
            $this->callMenu('partner', 'goods', 'list');
            
            $req_page = Request::get()->get('page');
            if(!is_object($this->db)){
                $this->db = \App::load("DB");
            }
            
            $max_strSQL  ="SELECT count(*) cnt FROM es_goods"; // count 쿼리문 입력
            $max_result = $this->db->query_fetch($max_strSQL ,  false); // count 쿼리문 출력
    
            $vid_page = $max_result[0]['cnt']/10; // 한 페이지에 n개씩 표시하기 위한 총 페이지수
    
            $total_page = ceil($vid_page); // 소수 $vid_page 올림
    
            $strSQL  ="SELECT goodsNo, goodsNm FROM es_goods ORDER BY goodsNo DESC limit ?,? ";
            $field2 = 10; // 페이지에 표시할 게시물
            // 요청 페이지에 맞는 데이터 n개 잘라서 보내기
            if($req_page){
                $field1 = ($req_page-1)*10; // 요청한 페이지에 응답할 게시물 시작 번호
                    
                
                $this->db->bind_param_push($arrBind, 'i', $field1);        
                $this->db->bind_param_push($arrBind, 'i', $field2);    
    
                $result = $this->db->query_fetch($strSQL , $arrBind, true);
                $pcnt = $max_result[0]['cnt'] - $field1; //?
                foreach($result as $key => $value){
                    $result[$key]['no'] = $pcnt--;
                }
            
                
                $this->setData("result",$result);
            }else{
                $req_page=1;
                $field1 = ($req_page-1)*10; // 요청한 페이지에 응답할 게시물 시작 번호
                    
                
                $this->db->bind_param_push($arrBind, 'i', $field1);        
                $this->db->bind_param_push($arrBind, 'i', $field2);    
    
                $result = $this->db->query_fetch($strSQL , $arrBind, true);
                $pcnt = $max_result[0]['cnt'] - $field1; //?
                foreach($result as $key => $value){
                    $result[$key]['no'] = $pcnt--;
                }
                
                $this->setData("result",$result);

            }
            // 게시글 번호 구해서 result 배열에 게시글번호 넣기
    
            // $ab = ($req_page-1)%5;
            // $start_page = $req_page - $ab;
    
            // start_page 페이지 목록이 바뀌기 전에는 start_page가 바뀌면 안됨
            $start_page = ceil($req_page/3);
            $page_range = $start_page*3;
            if($start_page >1){
                $start_page = $page_range-2;
            }
            
    
                // html 페이지 1~n개 구해서 태그 넘기기
                for($i=1; $i<=$total_page; $i++){
                    $pre = $req_page-3;
                    $next = $start_page+3;
                    if(3<$req_page){
                        $print_pre = "<li><a href='goods_list.php?page=$pre'>pre</a></li>";
                        $first_page = "<li><a href='goods_list.php?page=1'>맨 앞</a></li>";
                    }
    
                    if($i>=$total_page){
                        $print_page[] = "<li><a href='goods_list.php?page=$i'>$i</a></li>";
                        break;
                    }
                    
    
                    if($i<$total_page){
                        $print_page[] = "<li><a href='goods_list.php?page=$i'>$i</a></li>";

                    }   
                    if($i%3==0){
                        $print_next = "<li><a href='goods_list.php?page=$next'>next</a></li>";
                        $end_page = "<li><a href='goods_list.php?page=$total_page'>맨 뒤</a></li>";
                    }
                }
            $this->setData("first_page",$first_page);
            $this->setData("print_pre",$print_pre);
            $this->setData("start_page",$start_page);
            $this->setData("end_page",$end_page);
            $this->setData("print_next",$print_next);
            $this->setData("total_page",$total_page);
            $this->setData("print_page",$print_page);
            
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
