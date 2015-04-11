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

    /**
     * @param $token
     * @return array
     */
    public function checkHandshakeToken($token){
        $params = array(
             'token' => $token
        );
        return (array)CurlProvider::processGet($this->getUrl('passport', 'handshake'), $params);
    }

    public function sendOrder( $sdkId, $cmsId, $payment_variant, $status, $payment_price ){
        $params = array(
            'id' => $sdkId,
            'shop_refnum' => $cmsId,
            'payment_variant' => $payment_variant,
            'local_status' => $status,
            'payment_price' => $payment_price
        );

        return (array)CurlProvider::processPost($this->getUrl('order', 'send'), $params);
    }

    public function viewOrder($sdkId){
        $params = array(
            'id' => $sdkId
        );
        return (array)CurlProvider::processGet($this->getUrl('order', 'view'), $params);
    }

    public function editOrder($sdkId, $cmsId, $payment_variant, $status){
        $params = array(
            'id' => $sdkId,
            'shop_refnum' => $cmsId,
            'payment_variant' => $payment_variant,
            'local_status' => $status,
        );
        return (array)CurlProvider::processPost($this->getUrl('order', 'edit'), $params);
    }

    public function accessAdmin($token){
        $params = array(
            'token' => $token
        );
        return (array)CurlProvider::processGet($this->getUrl('passport', 'auth'), $params);
    }

    public function pushCart( array $cart ){
        return (array)CurlProvider::processJson($this->getUrl('passport', 'shop'), $cart);
    }


    public function getUrl( $controller, $method ){
        return $this->apiServer . $controller . '/' . $this->apiKey . '/' . $method . '.json';
    }
} 