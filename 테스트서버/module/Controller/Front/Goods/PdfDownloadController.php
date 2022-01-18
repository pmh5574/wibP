<?php
namespace Controller\Front\Goods;

use Request;
use App;

class PdfDownloadController extends \Controller\Front\Controller {
    public function __construct() 
    {
        parent::__construct();
        if(!is_object($this->db))
        {
            $this->db = App::load("DB");
        }
    }

    public function index() 
    {
        

        $goodsNo = Request::get()->get("goodsNo");

        $this->db->strField = "fileName, oriFileName";
        $this->db->strWhere = "goodsNo = ?";
        $this->db->strLimit = "0, 1";
        $this->db->bind_param_push($bindParam, "i", gd_isset($goodsNo));
        $query = $this->db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM wib_files' . implode(' ', $query);
        $getData = $this->db->query_fetch($strSQL, $bindParam, false);

        $this->setData("test", "test");

        
        if(gd_isset($getData['fileName'])) {
            
            $pathGoods = Request::server()->get('DOCUMENT_ROOT') . '/data/goods/';

            $real_filename = urldecode($getData['oriFileName']); 
            $file = $pathGoods.$getData['fileName'];

            header('Content-Type: application/x-octetstream');
            header('Content-Length: '.filesize($file));
            header('Content-Disposition: attachment; filename='.$real_filename);
            header('Content-Transfer-Encoding: binary');

            $fp = fopen($file, "r");
            fpassthru($fp);
            fclose($fp);
        }

    }
}