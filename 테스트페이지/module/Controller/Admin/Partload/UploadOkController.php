<?php
namespace Controller\Admin\Partload;

use Request;

use Component\Board\BoardView;
use Component\Board\ArticleActAdmin;

class UploadOkController extends \Bundle\Controller\Admin\Controller
{
    public function index()
    {
        $this->getView()->setDefine('layout', 'layout_blank.php');
        $getCom = new ArticleActAdmin();
        $req = Request::post()->toArray();
        $getfile = Request::files()->get('upfiles');
        $asd = $getCom->openApiBoardImage($getfile);
        gd_debug($asd);


        $uploads_dir = '/www/imarketing21_godomall_com/data/goods/ex_file';        
        $allowed_ext = array('jpg','jpeg','png','gif');
        gd_debug($getfile);
        $error = $getfile['error'];
        $name = $getfile['name'];
        if( $error != UPLOAD_ERR_OK ) {
            switch( $error ) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    echo "파일이 너무 큽니다. ($error)";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    echo "파일이 첨부되지 않았습니다. ($error)";
                    break;
                default:
                    echo "파일이 제대로 업로드되지 않았습니다. ($error)";
            }
            exit;
        }

        move_uploaded_file($getfile['tmp_name'], "$uploads_dir/$name");

    }
}