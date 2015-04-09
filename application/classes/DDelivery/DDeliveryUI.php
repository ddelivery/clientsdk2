<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/9/15
 * Time: 11:08 PM
 */

namespace DDelivery;


class DDeliveryUI {

    public $request;

    public function actionDefault(){
        return 1;
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
        return true;
    }

    /**
     * @return array
     */
    public function getTokenMethod(){
        return [];
    }

    public function preRender(){

    }

    public function postRender(){

    }
}