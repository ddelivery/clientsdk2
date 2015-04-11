<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/11/15
 * Time: 12:41 PM
 */

namespace DDelivery\Storage;


abstract class DBStorage {

    public $pdo;

    public $dbType;

    public $tableName;

    public function __construct($pdo, $dbType, $pdoTablePrefix = ''){
        $this->pdo = $pdo;
        $this->dbType = $dbType;
        $this->tableName = $pdoTablePrefix . $this->getTableName() ;
    }

    /**
     * @return string
     */
    abstract public function  getTableName();
} 