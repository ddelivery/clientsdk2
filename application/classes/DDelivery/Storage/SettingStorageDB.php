<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/11/15
 * Time: 12:48 PM
 */

namespace DDelivery\Storage;


class SettingStorageDB extends DBStorage implements  SettingStorageInterface {

    /**
     * @return string
     */
    public function  getTableName(){
        return 'ddelivery_settings';
    }

    public function createStorage()
    {
        // TODO: Implement createStorage() method.
    }

    public function save($settings)
    {
        // TODO: Implement save() method.
    }

    public function getParam($paramName)
    {
        // TODO: Implement getParam() method.
    }
}