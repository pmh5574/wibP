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


class ArticleWriteController extends \Bundle\Controller\Admin\Board\ArticleWriteController
{
    public function index()
    {
        parent::index();
        $db = \App::load('DB');

        $req = $this->getData('req');
        $bdWrite = $this->getData('bdWrite');
        
        if($req['bdId'] == 'purchase' && $req['mode'] != 'reply'){
            
            $query = "SELECT * FROM es_bd_purchase WHERE sno = {$bdWrite['data']['sno']}";
            $result = $db->fetch($query);

            $bdWrite['data']['address1'] = $result['address1'];
            $bdWrite['data']['address2'] = $result['address2'];
            $bdWrite['data']['district'] = $result['district'];
            $bdWrite['data']['country'] = $result['country'];
            $bdWrite['data']['postalCode'] = $result['postalCode'];

            $this->setData('bdWrite', $bdWrite);
            
        }else if($req['bdId'] == 'purchase' && $req['mode'] == 'reply'){
            
            $query = "SELECT * FROM es_bd_purchase WHERE sno = {$req['sno']}";
            $result = $db->fetch($query);

            $bdWrite['data']['address1'] = $result['address1'];
            $bdWrite['data']['address2'] = $result['address2'];
            $bdWrite['data']['district'] = $result['district'];
            $bdWrite['data']['country'] = $result['country'];
            $bdWrite['data']['postalCode'] = $result['postalCode'];
            
            $this->setData('bdWrite', $bdWrite);
        }
        
    }
}