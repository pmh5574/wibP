<?php
namespace Bundle\Controller\Front\Test;

class SampleController extends \Controller\Front\Controller
{
    /**
     * 설명
     * Description 
     */
    public function index()
    {
        $data = array(
            "data1" => "sample data1",
            "data2" => "sample data2",
            "data3" => "sample data3",
        );
        $this->setData($data);
    }
}