<?php
namespace Controller\Front;

use Globals;
use Session;
use Response;
use Request;

/**
 * 테스트용
 */
class MapApiController extends \Controller\Front\Controller
{

    public $db;


    /**
     * {@inheritdoc}
     */
    public function index()
    {
        $this->db = \App::load('DB');
        $no=Request::get()->get('no');
        $w=Request::get()->get('w');
        $viewmap=Request::get()->get('viewmap');
        $map=Request::get()->get('map');
        $strSQL = "SELECT * FROM es_store WHERE goods_store_no ='$no'";
        $result = $this->db->query($strSQL);
        $data = $this->db->fetch($result);
        $this->setData('store_no', $data['goods_store_no']);
        $this->setData('title', $data['goods_store_name']);
        $this->setData('add1', $data['goods_store_address1']);
        $this->setData('add2', $data['goods_store_address2']);
        $this->setData('add3', $data['goods_store_address3']);
        $this->setData('add4', $data['goods_store_address4']);
        $this->setData('tel', $data['goods_store_tel']);
        $this->setData('w', $w);
        $this->setData('map', $map);
        $this->setData('viewmap', $viewmap);
        $address11=Request::post()->get('address1');
        $address22=Request::post()->get('address2');
        $this->setData('address11', $address11);
        $this->setData('address22', $address22);


        //2019-12-24 매장정보추가
        if($address11){ $search1="where goods_store_address1 ='$address11' ";}
        if($address22){ $search2="and goods_store_address2 = '$address22'";}
        $query="SELECT * FROM es_store $search1 $search2 ORDER BY goods_store_no DESC ";
        $map_q = $this->db->query_fetch($query);
        $this->setData('maplist', $map_q);


        //echo "<pre>";
        //print_r($map_q);
        //exit;

        parent::index();

        if (Request::post()->get('store_no')) {
            $this->dbUpdate();
        }
        if (Request::get()->get('del')) {
            $this->dbdelete();
        }
        if (Request::post()->get('goodsNo_')) {
            $this->dbInsert();
        }

    }

    public function dbInsert()
    {
        $_goodsNo=Request::post()->get('goodsNo_');
        $store_name=Request::post()->get('store_name');
        $address=Request::post()->get('address');
        $address4=Request::post()->get('address4');
        $store_tel=Request::post()->get('store_tel');

        $add_filed  = explode(" ",$address);
        $address1 	= $add_filed['0'];
        $address2  	= $add_filed['1'];
        $address3 	= "{$add_filed['2']}{$add_filed['3']}{$add_filed['4']}{$add_filed['5']}{$add_filed['6']}";


        $strUpdateSQL = "INSERT INTO es_store SET goodsNo = '" . $_goodsNo . "' , goods_store_name = '" . $store_name . "' , goods_store_address1 = '" . $address1 . "', goods_store_address2 = '" . $address2 . "', goods_store_address3 = '" . $address3 . "', goods_store_address4 = '" . $address4 . "', goods_store_tel = '" . $store_tel . "'";
        $this->db->query($strUpdateSQL);
        echo "<script>alert('매장추가 되었습니다.');window.location='map_api.php'</script>";
        // echo "<pre>";
        //print_r($strUpdateSQL);
        //exit;

    }

    public function dbUpdate()
    {

        $store_no=Request::post()->get('store_no');
        $store_name=Request::post()->get('store_name');
        $address=Request::post()->get('address');
        $address4=Request::post()->get('address4');
        $store_tel=Request::post()->get('store_tel');

        $add_filed  = explode(" ",$address);
        $address1 	= $add_filed['0'];
        $address2  	= $add_filed['1'];
        $address3 	= "{$add_filed['2']}{$add_filed['3']}{$add_filed['4']}{$add_filed['5']}{$add_filed['6']}";

        $strUpdateSQL = "UPDATE es_store SET goods_store_name = '" . $store_name . "' , goods_store_address1 = '" . $address1 . "', goods_store_address2 = '" . $address2 . "', goods_store_address3 = '" . $address3 . "', goods_store_address4 = '" . $address4 . "', goods_store_tel = '" . $store_tel . "'  WHERE goods_store_no = '" . $store_no . "'";
        $this->db->query($strUpdateSQL);
        echo "<script>alert('정보수정 되었습니다.');window.location='map_api.php?w=u&no=$store_no'</script>";
        // echo "<pre>";
        //print_r($strUpdateSQL);
        //exit;

    }

    public function dbdelete()
    {

        $store_no=Request::get()->get('del');
        $strDeleteSQL = "DELETE FROM es_store WHERE goods_store_no = '" . $store_no . "'";
        $this->db->query($strDeleteSQL);
        echo "<script>alert('삭제되었습니다.');window.location='map_api.php'</script>";

    }
    /**
     * {@inheritdoc}
     */

}