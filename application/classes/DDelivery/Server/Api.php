<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/10/15
 * Time: 8:05 PM
 */

namespace DDelivery\Server;


class Api {

    public $apiKey;

    public $apiServer;


    public function checkHandshakeToken($token){
        $params = array(
             'token' => $token
        );
        return CurlProvider::processGet($this->getUrl('passport', 'handshake'), $params);
    }


    public function accessAdmin($token){
        $params = array(
            'token' => $token
        );
        return CurlProvider::processGet($this->getUrl('passport', 'auth'), $params);
    }

    public function pushCart( array $cart ){
        return CurlProvider::processJson($this->getUrl('passport', 'shop'), $cart);
    }


    public function getUrl( $controller, $method ){
        return $this->apiServer . $controller . '/' . $this->apiKey . '/' . $method . '.json';
    }
} 