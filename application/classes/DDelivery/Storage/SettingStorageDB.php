<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/11/15
 * Time: 12:48 PM
 */

namespace DDelivery\Storage;


use DDelivery\Adapter\Adapter;

class SettingStorageDB extends DBStorage implements  SettingStorageInterface {

    /**
     * @return string
     */
    public function  getTableName(){
        return 'ddelivery_settings';
    }

    public function createStorage(){
        echo 'xxx';
        if($this->dbType == Adapter::DB_MYSQL) {
            $query = "CREATE TABLE `$this->tableName` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `content` text DEFAULT NULL,
                            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        }elseif($this->dbType == Adapter::DB_SQLITE){
            $query = "CREATE TABLE $this->tableName (
                            id INTEGER PRIMARY KEY AUTOINCREMENT,
                            content TEXT
                      )";
        }
        $this->pdo->exec($query);
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