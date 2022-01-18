<?php
namespace Controller\Admin\Board;

use Request;

class PlusReviewPsController extends \Bundle\Controller\Admin\Board\PlusReviewPsController
{
    public function index() 
    {
        $postValue = Request::post()->toArray();
        print_r($postValue);
        parent::index();
        exit;
    }
}