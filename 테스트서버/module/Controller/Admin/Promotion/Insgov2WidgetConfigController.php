<?php

namespace Controller\Admin\Promotion;

use App;

class Insgov2WidgetConfigController extends \Bundle\Controller\Admin\Promotion\Insgov2WidgetConfigController
{
    protected $db = null;
    
    public function __construct() {
        
        parent::__construct();
        
        if (!is_object($this->db)) {
            $this->db = \App::load('DB');
        }
    }
    
    // public function index()
    // {
    //     parent::index();
    //     $widget = App::load('\\Component\\Promotion\\Insgov2Widget');
    //     $widgetData = $widget->getInsgoWidgetData($widgetData,$widgetData['widgetSno']);
    //     //$aa = $widget->getCurlOutput("https://graph.instagram.com/me/media?fields=id%2Ccaption%2Cmedia_type%2Cmedia_url%2Cthumbnail_url%2Cpermalink%2Ctimestamp",'n',null);
    //     //print_r($widgetData);
    // }
	
}