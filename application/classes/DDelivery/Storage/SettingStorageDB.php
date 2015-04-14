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
     * @var array
     */
    public $settings = null;

    /**
     * @return string
     */
    public function  getTableName(){
        return 'ddelivery_settings';
    }

    public function createStorage(){
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

    /**
     * Проверка на существование настроек
     *
     * @return bool
     */
    public function isRecExist(){
        $query = 'SELECT id FROM ' . $this->tableName . ' WHERE id = 1';
        $sth = $this->pdo->prepare( $query );
        $sth->execute();
        $result = $sth->fetchAll(\PDO::FETCH_OBJ);
        if( count($result) > 0 ){
            return true;
        }
        return false;
    }

    public function getParams(){
        $query = 'SELECT content FROM ' . $this->tableName . ' WHERE id = 1';
        $sth = $this->pdo->prepare( $query );
        $sth->execute();
        $result = $sth->fetchAll(\PDO::FETCH_OBJ);
        if( count($result) > 0 ){
            return json_decode( (array)$result[0]->content );
        }
        return array();
    }

    public function save($settings){
        if($this->isRecExist()){
            $query = 'UPDATE '.$this->tableName.' SET content=:content WHERE id=1';
        }else{
            $query = 'INSERT INTO ' . $this->tableName . ' (content) VALUES (:content)';
        }
        $sth = $this->pdo->prepare( $query );
        $sth->bindParam( ':content', json_encode( $settings ) );
        return $sth->execute();
    }

    public function getParam($paramName){
        if(null === $this->settings){
            $this->settings = $this->getParams();
        }
        if( isset($this->settings[$paramName]) ){
            return $this->settings[$paramName];
        }
        return null;
    }
}