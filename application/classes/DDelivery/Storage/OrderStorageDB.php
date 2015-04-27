<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/11/15
 * Time: 12:37 PM
 */

namespace DDelivery\Storage;


use DDelivery\Adapter\Adapter;

class OrderStorageDB extends DBStorage implements OrderStorageInterface {


    /**
     * Создаем хранилище
     *
     * @return bool
     */
    public function createStorage(){
        if($this->dbType == Adapter::DB_MYSQL) {
            $query = "CREATE TABLE IF NOT EXISTS `$this->tableName` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `sdk_id` int(11) NOT NULL,
                            `ddelivery_id` int(11)  DEFAULT NULL,
                            `cms_id` varchar(60) NOT NULL,
                            `payment` varchar(60) DEFAULT NULL,
                            `created` DATETIME NOT NULL,
                            `status` varchar(60) DEFAULT NULL,
                            `payment_price` int(11) DEFAULT NULL,
                            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        }elseif($this->dbType == Adapter::DB_SQLITE){
            $query = "CREATE TABLE IF NOT EXISTS $this->tableName (
                            id INTEGER PRIMARY KEY AUTOINCREMENT,
                            sdk_id INTEGER,
                            ddelivery_id INTEGER,
                            cms_id TEXT,
                            created TEXT,
                            payment TEXT,
                            status TEXT,
                            payment_price INTEGER
                      )";
        }
        $sth = $this->pdo->prepare( $query );
        return $sth->execute();
    }

    /**
     *
     * Проверка существования записи
     *
     * @param $cmsId
     * @return bool
     */
    public function isRecordExists($cmsId){
        $query = 'SELECT id FROM ' . $this->tableName . ' WHERE cms_id = :cms_id';
        $sth = $this->pdo->prepare( $query );
        $sth->bindParam( ':cms_id', $cmsId );
        $sth->execute();
        $result = $sth->fetchAll(\PDO::FETCH_OBJ);
        if( count($result) > 0 ){
            return true;
        }
        return false;
    }


    public function getAllOrders(){
        $query = 'SELECT * FROM ' . $this->tableName;
        $sth = $this->pdo->prepare( $query );
        $sth->execute();
        $result = $sth->fetchAll(\PDO::FETCH_OBJ);
        return $result;
    }

    /**
     *
     * Сохранить заказ
     *
     * @param $sdkId
     * @param $cmsId
     * @param $payment
     * @param $status
     * @param int $ddeliveryId
     * @param int $id
     * @return bool
     */
    public function saveOrder($sdkId, $cmsId, $payment, $status, $ddeliveryId = 0, $id = 0){
        if( $this->isRecordExists($cmsId) ){
            $query = 'UPDATE '.$this->tableName.' SET sdk_id=:sdk_id, payment=:payment,
                    status=:status, ddelivery_id=:ddelivery_id  WHERE cms_id=:cms_id';
        }else{
            $query = 'INSERT INTO ' . $this->tableName . ' (sdk_id, payment, status, ddelivery_id, cms_id )
                    VALUES (:sdk_id, :payment, :status, :ddelivery_id, :cms_id)';
        }
        $sth = $this->pdo->prepare( $query );
        $sth->bindParam( ':sdk_id', $sdkId );
        $sth->bindParam( ':payment', $payment );
        $sth->bindParam( ':status', $status );
        $sth->bindParam( ':ddelivery_id', $ddeliveryId );
        $sth->bindParam( ':cms_id', $cmsId );
        return $sth->execute();
    }


    /**
     *
     * Получить заказ
     *
     * @param $cmsId
     * @return null
     */
    public function getOrder($cmsId){
        $query = 'SELECT * FROM ' . $this->tableName . ' WHERE cms_id = :cms_id';
        $sth = $this->pdo->prepare( $query );
        $sth->bindParam( ':cms_id', $cmsId );
        $sth->execute();
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
        if( count($result) > 0 ){
            return $result[0];
        }
        return null;
    }

    /**
     * @return string
     */
    public function  getTableName(){
        return 'ddelivery_orders';
    }

    public function getOrderBySdkId($sdkId){
        $query = 'SELECT * FROM ' . $this->tableName . ' WHERE sdk_id =:sdk_id';
        $sth = $this->pdo->prepare( $query );
        
        $sth->bindParam( ':sdk_id', $sdkId );
        $sth->execute();
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
        if( count($result) > 0 ){
            return $result[0];
        }
        return null;
    }
}