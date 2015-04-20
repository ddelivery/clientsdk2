<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/10/15
 * Time: 8:19 PM
 */

namespace DDelivery\Server;

use DDelivery\Utils;

class CurlProvider {

    public static function getCurl(){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_REFERER, Utils::fullUrl($_SERVER, false));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        return $curl;
    }


    public static function processJson($url, $params){
        $curl = self::getCurl();
        //$headers= array('Accept: application/json','Content-Type: application/json');
        curl_setopt($curl, CURLOPT_URL, $url);
        //curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode( $result, true );
    }


    public static function processPost($url, $params){
        $curl = self::getCurl();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode( $result, true );
    }



    public static function processGet($url, $params){

        $curl = self::getCurl();
        $url = $url . '?' .http_build_query($params);
        curl_setopt($curl, CURLOPT_URL, $url  );
        $result = curl_exec($curl);

        curl_close($curl);
        return json_decode( $result, true );
    }
}