<?php
namespace Controller\Front\Test;

class ExController extends \Controller\Front\Controller

{   
    public function pre()
    {
        echo "전 처리";
    }

    protected $db = null;

    public function index()
    {   
        echo "<h1>메인 처리</h1>";

       
        if (!is_object($this->db)){
            $this->db = \App::load('DB');
        }
        
        $db = \App::load('DB');
        $arrBind = [];
        $query = "SELECT * FROM es_minho";
        $result = $db->query_fetch($query,$arrBind,false);

        print_r($result);
        $this->setData("result",$result);

        
        $uri = URI_HOME;
        $this->setData("uri",$uri);
        //echo '인덱스내에 스트링을 출력하면 해당 내용이 결과로 저장되며, 파일 확장자에 따라서 자동으로 Mime-Type이 설정됩니다.';
        //$this->streamedDownload('sample_test.txt');
        
    }

    public function post()
    {
        echo "<br>";
        echo "후 처리";
    
    }
}