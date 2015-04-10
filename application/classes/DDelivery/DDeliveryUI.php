<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/9/15
 * Time: 11:08 PM
 */

namespace DDelivery;


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
    public function setAdapter($adapter){
        $this->adapter = $adapter;
    }

    public function actionDefault(){
        return 1;
    }



    public function actionOrders(){

    }

    public function actionFields(){

    }

    public function actionSave(){

    }

    public function actionPush(){

    }


    public function actionShop(){
        $cart = $this->adapter->getProductCart();
        $token = $this->business->renderModuleToken($cart);
        if($token){
            $url = $this->adapter->getSdkServer() . 'passport/' . $token . '/shop.json';
            $this->setRedirect($url);
        }
        throw new DDeliveryException("Ошибка входа в магазин");
    }



    public function actionAdmin(){
        if( $this->adapter->isAdmin() ){
            $token = $this->business->renderAdmin();
            if($token){
                $url = $this->adapter->getSdkServer() . 'passport/' .
                                $this->adapter->getApiKey() . '/auth.json?token=' . $token;
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
        }
        $this->postRender();
        echo  json_encode(array( 'success' => $success, 'data' => $data ));
    }


    public function checkToken(){
        if(isset($this->request['api_key']) && isset($this->request['token'])){
            if($this->business->checkToken($this->request['token']))
                return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getTokenMethod(){
        return [];
    }

    public function preRender(){

    }

    public function setRedirect($url){
        header('Location: '. $url);
    }

    public function postRender(){

    }
}