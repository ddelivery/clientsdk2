<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/10/15
 * Time: 8:00 PM
 */

namespace DDelivery\Business;


use DDelivery\Adapter\Adapter;
use DDelivery\Server\Api;
use DDelivery\Storage\OrderStorageInterface;
use DDelivery\Storage\SettingStorageInterface;
use DDelivery\Storage\TokenStorageInterface;
use DDelivery\Utils;

class Business {



    /**
     * Время действия токена в минутах
     */
    const TOKEN_LIFE_TIME = 20;

    /**
     * @var Api
     */
    private  $api;

    /**
     * @var TokenStorageInterface
     */
    private  $tokenStorage;

    /**
     * @var SettingStorageInterface
     */
    private  $settingStorage;

    /**
     * @var OrderStorageInterface
     */
    private  $orderStorage;


    public  function __construct( Api $api, TokenStorageInterface $tokenStorage,
                                  SettingStorageInterface $settingStorage,
                                  OrderStorageInterface $orderStorage ){
        $this->api = $api;
        $this->tokenStorage = $tokenStorage;
        $this->settingStorage = $settingStorage;
        $this->orderStorage = $orderStorage;
    }



    /**
     * Создать стореджи
     */
    public function initStorage(){
        $this->tokenStorage->createStorage();
        $this->settingStorage->createStorage();
        $this->orderStorage->createStorage();
    }

    /**
     * Визивается при окончании оформления заказа
     * для привязки заказа на стороне цмс и на стороне сервера
     *
     * @param $sdkId
     * @param $cmsId
     * @param $payment
     * @param $status
     * @return bool
     */
    public function onCmsOrderFinish($sdkId, $cmsId, $payment, $status){
        $id = $this->orderStorage->saveOrder($sdkId, $cmsId, $payment, $status);
        if( !empty($id)  ){
            $result = $this->api->editOrder($sdkId, $cmsId, $payment, $status);
            if( $result['success'] == 1 ){
                return true;
            }
        }
        return false;
    }


    /**
     *
     * Получить заказ по Id
     *
     * @param $sdkId
     * @return array
     */
    public function getOrder($sdkId){
        $order = $this->orderStorage->getOrderBySdkId((int)$sdkId);
        if( count( $order )){
            return $order;
        }
        return [];
    }


    /**
     * Визивается при смене статуса заказа,
     * если статус заказа соответствует статусу указанному в настройках
     * то заказ отправляется на сервер DDelivery.ru
     *
     *
     * @param $sdkId
     * @param $cmsId
     * @param $payment
     * @param $status
     * @return int
     */
    public function onCmsChangeStatus($sdkId, $cmsId, $payment, $status){
        $order = $this->orderStorage->getOrder($cmsId);
        if( count($order) && $order['ddeliveryId'] == 0 ){
            if($this->settingStorage->getParam(Adapter::PARAM_STATUS_LIST) == $status){
                $payment_price = 0;
                if($this->settingStorage->getParam(Adapter::PARAM_PAYMENT_LIST) == $payment)
                    $payment_price = 1;
                $result = $this->api->sendOrder($sdkId, $cmsId, $payment, $status, $payment_price);
                if( isset($result['success']) && $result['success'] == 1 ){
                    $ddelivery_id = $result['data']['ddelivery_id'];
                    $order = $this->orderStorage->saveOrder($sdkId, $cmsId, $payment, $status,
                                                    $ddelivery_id, $order['id']);
                    return $ddelivery_id;
                }
            }
        }
        return 0;
    }

    /**
     *
     * Проверить токен рукопожатия на стороне серверного сдк
     * @param $token
     * @return bool
     */
    public function checkHandshakeToken($token){
        $result = $this->api->checkHandshakeToken($token);
        if( $result['success'] == 1 ){
            return true;
        }
        return false;
    }

    /**
     * Сохранить настройки на стороне цмс
     *
     * @param $settings
     * @return bool
     */
    public function saveSettings($settings){
        if( $this->settingStorage->save($settings) ){
            return true;
        }
        return false;
    }


    /**
     * Получить токен для входа в панель серверного сдк
     * @return null
     */
    public function renderAdmin(){
        $token = $this->generateToken();

        $result = $this->api->accessAdmin($token);
        if( isset($result['success']) && ($result['success'] == 1) ){
            return $result['token'];
        }
        return null;
    }

    /**
     *
     * Получить токен для показа модуля
     *
     * @param $cart
     * @return null
     */
    public function renderModuleToken($cart){
        $result = $this->api->pushCart($cart);
        if( $result['success'] == 1 ){
            return $result['data']['token'];
        }
        return null;
    }


    /**
     * Сгенерировать токен доступа
     * виполнения команд на стороне цмс
     *
     * @return string
     */
    public function generateToken(){
        $token = Utils::generateToken();
        if( $this->tokenStorage->createToken($token, self::TOKEN_LIFE_TIME) ){
            return $token;
        }
        return null;
    }

    /**
     *
     * Проверить токен доступа
     * виполнения команд на стороне цмс
     *
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

    /**
     * @param \DDelivery\Storage\OrderStorageInterface $orderStorage
     */
    public function setOrderStorage($orderStorage)
    {
        $this->orderStorage = $orderStorage;
    }

    /**
     * @return \DDelivery\Storage\OrderStorageInterface
     */
    public function getOrderStorage()
    {
        return $this->orderStorage;
    }

    /**
     * @param \DDelivery\Storage\SettingStorageInterface $settingStorage
     */
    public function setSettingStorage($settingStorage)
    {
        $this->settingStorage = $settingStorage;
    }

    /**
     * @return \DDelivery\Storage\SettingStorageInterface
     */
    public function getSettingStorage()
    {
        return $this->settingStorage;
    }

    /**
     * @param \DDelivery\Storage\TokenStorageInterface $tokenStorage
     */
    public function setTokenStorage($tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return \DDelivery\Storage\TokenStorageInterface
     */
    public function getTokenStorage()
    {
        return $this->tokenStorage;
    }
} 