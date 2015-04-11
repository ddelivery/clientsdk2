<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/9/15
 * Time: 11:08 PM
 */

namespace DDelivery;


use DateTime;
use DDelivery\Adapter\Adapter;
use DDelivery\Business\Business;

class DDeliveryUI {

    public $request;

    /**
     * @var Adapter
     */
    public $adapter;

    /**
     * @var Business
     */
    public $business;

    /**
     * @param $adapter
     */
    public function setAdapter(Adapter $adapter){
        $this->adapter = $adapter;
    }

    /**
     * @param Business $business
     */
    public function setBusiness(Business $business){
        $this->business = $business;
    }

    public function actionDefault(){
        return 1;
    }

    function validateDate($date, $format = 'Y.m.d'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }


    public function actionOrder(){
        if( !empty( $this->request['order_id']) ){
            $order = $this->adapter->getOrder($this->request['order_id']);
            return $order;
        }
        throw new DDeliveryException("Ошибка получения заказа");
    }

    public function actionOrders(){
        if( $this->validateDate( $this->request['from'] ) &&
                $this->validateDate( $this->request['to'])){
            $orders = $this->adapter->getOrders($this->request['from'], $this->request['to']);
            if( count($orders) ){
                return $orders;
            }
        }
        throw new DDeliveryException("Ошибка получения списка заказов");
    }


    /**
     * Получить список полей настроек
     *
     * @return array
     */
    public function actionFields(){
        return $this->adapter->getFieldList();
    }

    /**
     * Сохранить настройки
     *
     * @return int
     * @throws DDeliveryException
     */
    public function actionSave(){
        if(!empty($this->request['cms'])){
            $result = $this->business->saveSettings($this->request['cms']);
            if($result)
                return 1;
        }
        throw new DDeliveryException("Ошибка сохранения настроек");
    }


    /**
     * Обработка пуша статусов
     */
    public function actionPush(){
        if(!empty($this->request['orders'])){

        }
    }


    public function actionShop(){
        $cart = $this->adapter->getProductCart();
        $token = $this->business->renderModuleToken($cart);
        if($token){
            $url = $this->adapter->getSdkServer() . 'ui/' . $token . '/module.json';
            $params = http_build_query($this->adapter->getUserParams($this->request));
            $this->setRedirect($url . '?' . $params);
        }
        throw new DDeliveryException("Ошибка входа в магазин");
    }



    public function actionAdmin(){
        if( $this->adapter->isAdmin() ){
            $token = $this->business->renderAdmin();
            if($token){
                $url = $this->adapter->getSdkServer() . 'passport/' .
                                $this->adapter->getApiKey() . '/admin.json?token=' . $token;
                $this->setRedirect($url);
            }
        }
        throw new DDeliveryException("Ошибка входа в админ панель");
    }

    public function actionHandshake(){
        if(isset($this->request['api_key']) && isset($this->request['token'])){
            if($this->request['api_key'] == $this->adapter->getApiKey()){
                if( $this->business->checkHandshakeToken($this->request['token'])){
                    return array('token' => $this->business->generateToken());
                }
            }
        }
        throw new DDeliveryException("Ошибка инициализации токена");
    }

    public function render(array $request){
        $this->request = $request;
        $this->preRender();
        $success = 1;
        try{
            if(!isset($request['action'])){
                $request['action'] = 'default';
            }
            $request['action'] = strtolower($request['action']);
            if( in_array( $request['action'], $this->getTokenMethod() ) ){
                 if( !$this->checkToken() ){
                     throw new DDeliveryException("Ошибка доступа в раздел");
                 }
            }
            $action = 'action'. ucfirst(strtolower($request['action']));
            // If the action doesn't exist, it's a 404
            if ( ! method_exists($this, $action)){
                $action = 'actionDefault';
            }
            $data = $this->{$action}();
        }catch (\Exception $e){
            $success = 0;
            $data = $e->getMessage();
            $data = array(['error' => $data]);
            echo $e->getMessage();
            return;
        }
        $this->postRender();
        echo  json_encode(array( 'success' => $success, 'data' => $data ));
    }

    /**
     * Проверка существования токена для совершения
     * закритого метода
     *
     * @return bool
     */
    public function checkToken(){
        if(isset($this->request['api_key']) && isset($this->request['token'])){
            if($this->business->checkToken($this->request['token'])
                        && $this->request['api_key'] == $this->adapter->getApiKey())
                return true;
        }
        return false;
    }

    /**
     *
     * Методи которие доступни по токену
     *
     * @return array
     */
    public function getTokenMethod(){
        return ['orders', 'push', 'fields', 'save', 'order'];
    }

    public function preRender(){

    }

    public function setRedirect($url){
        header('Location: '. $url);
    }

    public function postRender(){

    }
}