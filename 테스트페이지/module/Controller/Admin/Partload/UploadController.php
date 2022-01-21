<?php
namespace Controller\Admin\Partload;

use Request;


class UploadController extends \Bundle\Controller\Admin\Controller
{
    public function index()
    {
        $this->getView()->setDefine('layout', 'layout_blank.php');
        
    }
}
