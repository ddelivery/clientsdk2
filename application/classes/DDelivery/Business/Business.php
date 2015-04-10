<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/10/15
 * Time: 8:00 PM
 */

namespace DDelivery\Business;


use DDelivery\Server\Api;
use DDelivery\Storage\TokenStorageInterface;
use DDelivery\Utils;

class Business {

    const TOKEN_LIFE_TIME = 60;

    /**
     * @var Api
     */
    private  $api;

    /**
     * @var TokenStorageInterface
     */
    private  $tokenStorage;


    public function checkHandshakeToken($token){
        $result = $this->api->checkHandshakeToken($token);
        if( $result['success'] == 1 ){
            return true;
        }
        return false;
    }


    public function renderAdmin(){
        $token = $this->generateToken();
        $result = $this->api->accessAdmin($token);
        if( $result['success'] == 1 ){
            return $result['data']['token'];
        }
        return null;
    }

    public function renderModuleToken($cart){
        $result = $this->api->pushCart($cart);
        if( $result['success'] == 1 ){
            return $result['data']['token'];
        }
        return null;
    }

    public function generateToken(){
        $token = Utils::generateToken();
        $this->tokenStorage->createToken($token, self::TOKEN_LIFE_TIME);
        return $token;
    }

    /**
     * @param $token
     * @return bool
     */
    public function checkToken($token){
        if( $this->tokenStorage->checkToken($token) ){
            return true;
        }
        return false;
    }

    public function setApi($api){
        $this->api = $api;
    }
} 