<?php
namespace Controller\Front\Board;

use Request;

class PlusReviewPsController extends \Bundle\Controller\Front\Board\PlusReviewPsController 
{
    public function index() 
    {
        $postValue = Request::post()->toArray();
        print_r($postValue);
        parent::index();
        exit;
    }
}
