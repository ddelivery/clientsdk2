<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/10/15
 * Time: 11:03 PM
 */

namespace DDelivery;


class Utils {



    public static function generateToken(){
        $rand = rand( -1000, 1000 );
        $token = md5( self::getUserHost() . $rand ) . md5( time() + $rand );
        return $token;
    }

    public static function getUserHost(){
        if (!empty($_SERVER['HTTP_X_REAL_IP'])){
            $ip=$_SERVER['HTTP_X_REAL_IP'];
        }elseif (!empty($_SERVER['HTTP_CLIENT_IP'])){
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
} 