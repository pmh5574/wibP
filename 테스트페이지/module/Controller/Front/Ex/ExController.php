<?php
namespace Controller\Front\Ex;

use Globals;
use Session;
use Response;
use Request;

class ExController extends \Controller\Front\Controller

{
    protected $db = null; // 변수 선언

    public function index()
    {   
        if (!is_object($this->db)){
            $this->db = \App::load('DB');
        }
        
        $db = \App::load('DB');
        $query = "SELECT * FROM es_minho";
        $result = $db->query_fetch($query,true);
        $req = Request::server()->get('REQUEST_URI');
        foreach($result as $key=>$value){
            $Exfor[] = $value;
            gd_debug($key);

        }

        gd_debug($Exfor);

        $data = array(
            "0" => array(
                "data1" => "sample data1",
                "data1" => "sample data1",
                "data1" => "sample data1",
            )
           
            
        );
        
        foreach($result as $key=>$value){
            $_Result = $value;
            //print_r($value['sno']);
            foreach($_Result as $k=>$val){
                print_r($val);
            }
            gd_debug($value);
            echo "<br>";
           // print_r($value);

        }
        print_r($result[1]['sno']);
        print_r($result[2]['sno']);
        print_r($result[3]['sno']);
        print_r($result[4]['sno']);
        print_r($result[5]['sno']);

        for($i=0; $i<count($result); $i++){
            print_r($result[$i]['sno']);
        }
        

        //
        $data = array(
            "data" => "sample data1",
            "data" => "sample data2",
            "data" => "sample data3",
            
        );
        

        foreach($data as $key=>$value){
            echo "<br>";
            
            //print_r($value);
            
            
        }

        //print_r($data);
        echo 'Test <br>';
        echo '<pre>';
        print_r(Request::get()->all());
        print_r("post :" . Request::post()->all());
        print_r(Session::all());
        print_r(Cookie::all());
        echo '</pre>';
        $this -> setData('result', $result);
        $this -> setData('data', $data);
    }
}