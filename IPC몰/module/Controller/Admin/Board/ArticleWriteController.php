<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link http://www.godo.co.kr
 */
namespace Controller\Admin\Board;

use Component\Wib\WibSql;

class ArticleWriteController extends \Bundle\Controller\Admin\Board\ArticleWriteController
{
    public function index()
    {
        parent::index();
        
        $conn = new WibSql();
        
        $sql = 'select * from es_wibaddr where pa_idx = 0 order by idx asc';
        $data = $conn->WibAll($sql);
        
        $this->setData('addrlist', $data);
        
    }
}