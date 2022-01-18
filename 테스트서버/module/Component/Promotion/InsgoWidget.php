<?php

namespace Component\Promotion;


class InsgoWidget extends \Bundle\Component\Promotion\InsgoWidget
{
    public function getCurlOutput($curlUrl,$postType,$postData)
    {
        print_r($curlUrl);
        echo '<br>///<br>';
        print_r($postData);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $curlUrl);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.103 Safari/537.36');
        curl_setopt($ch, CURLOPT_REFERER, 'https://www.instagram.com/');
        $result = html_entity_decode(curl_exec($ch), true);
        curl_close($ch);
        
        
        preg_match_all('/.display(.*);/i', $result, $match);
        
        $arrImgData = explode(',', preg_replace("/[\"\']/i", "", $match[0][0]));
        
        print_r($arrImgData);
        
        //print_r($result);
        return $result;
        //return parent::getCurlOutput($curlUrl,$postType,$postData);
    }
}