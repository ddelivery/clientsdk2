<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/11/15
 * Time: 12:37 PM
 */

namespace DDelivery\Storage;


class OrderStorageDB extends DBStorage implements OrderStorageInterface {


    public function createStorage(){
        // TODO: Implement createStorage() method.
    }

    public function saveOrder($sdkId, $cmsId, $payment, $status, $ddeliveryId = 0, $id = 0){
        // TODO: Implement saveOrder() method.
    }

    public function getOrder($cmsId){
        // TODO: Implement getOrder() method.
    }

    /**
     * @return string
     */
    public function  getTableName(){
        return 'ddelivery_orders';
    }
}