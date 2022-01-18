<?php
namespace Controller\Admin\Order;

class UserPageController extends \Controller\Admin\Controller
{
    public function index()
    {
        try {
            $setData = 'Hello World!';
            $this->setData('setData', $setData);
        
        } catch (\Exception $e) {
            throw $e;
        }
    }//index end
}//class end