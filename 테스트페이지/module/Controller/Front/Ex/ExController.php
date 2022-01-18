<?php
namespace Controller\Front\Ex;

class ExController extends \Controller\Front\Controller

{

    public function index()
    {
        $data = array(
            "data1" => "sample data1",
            "data2" => "sample data2",
            "data3" => "sample data2",
        );
        $this -> setData($data);
    }
}