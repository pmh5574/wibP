<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Smart to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link http://www.godo.co.kr
 */
namespace Component\Board;

use Request;
/**
 * 게시판 처리 Class
 * 글쓰기, 수정, 답변, 삭제
 */
class BoardAct extends \Bundle\Component\Board\BoardAct
{
    public function saveData()
    {
        //returnUrl(queryString)에 galleryk 또는 galleryA가 있는 경우
        $this->db = \App::load('DB');
        
        $addScrpt = '';
        $req = ($this->req);
        $str = $req['returnUrl'];
        $substr = "galleryK";
        $result = strpos($str, $substr);
        $address1 = $req['address1'];
        //$address2 = $req['address2'];
        $district = $req['district'];
        $country = $req['country'];
        //$postalCode = $req['postalCode'];
        $contents = $req['contents'];  

        //contents에 내용없으면 .으로 처리
        if($contents == null || $contents == ""){
            //Request::post()->set("contents", '222');
            $this->req['contents'] = ".";
        }

        //purchase에서
        if($result != null || $result != ""){
            //주소 값 없으면 한번 더 막기
            if($address1 == "" || $address1 == null || $district == "" || $district == null || $country == "" || $country == null){
                echo "<script>alert('Please enter your deilvery address.'); window.history.back();</script>";exit;                   
            }  
            else{
                $msgs = parent::saveData();
                $strSQL = "SELECT sno FROM es_bd_purchase ORDER BY sno DESC LIMIT 1";
                $re = $this->db->query_fetch($strSQL,null);
                
                $strSSQL = "UPDATE es_bd_purchase SET address1 = '" .$req['address1']. "', address2 = '" .$req['address2']. "', district = '" .$req['district']. "', country = '" .$req['country']. "', postalCode = '" .$req['postalCode']. "' WHERE sno = " .$re[0]['sno'];
                $this->db->bind_query($strSSQL,null);
                echo "<script>alert('Saved');location.href='../main/html.php?htmid=proc/complete.html&myShop'</script>";exit;
            }              
        }
        $msgs = parent::saveData();
        $substr2 = "galleryA";
        $result2 = strpos($str, $substr2);

        //inquiry에서
        if($result2 != null || $result != ""){
            echo "<script>alert('Saved');location.href='../main/html.php?htmid=proc/complete2.html&myShop'</script>";exit;                     
        }
        return $msgs;
    }

}