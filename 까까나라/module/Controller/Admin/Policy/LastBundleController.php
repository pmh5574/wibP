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

namespace Controller\Admin\Policy;

use Request;
use Component\Wib\WibSql;

class LastBundleController extends \Controller\Admin\Controller
{
    

    public function index()
    {
        $wib = new WibSql();

        $postValue = Request::post()->toArray();

        switch($postValue['mode']){
            case 'save':
                $data = [
                    'es_scmDeliveryBundle',
                    array('method'=>[$postValue['method'],'s'],
                          'deliveryType'=>[$postValue['deliveryType'],'s'],
                    )
                ];
        
                $sno = $wib->WibInsert($data);

                $this->redirect('/policy/delivery_bundle.php');

                break;

            case 'modify':
                $data = [
                    'es_scmDeliveryBundle',
                    array('method'=>[$postValue['method'],'s'],
                          'deliveryType'=>[$postValue['deliveryType'],'s'],
                    ),
                    array('sno'=>[$postValue['sno'],'s'])
                ];

                $wib->WibUpdate($data);

                $this->redirect('/policy/delivery_bundle.php');

                break;
        }

        
        
        
        exit;
    }

    
}

