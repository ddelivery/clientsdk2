<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/11/15
 * Time: 12:51 PM
 */

namespace DDelivery\Storage;


class TokenStorageDB extends DBStorage implements TokenStorageInterface {

    /**
     * @return string
     */
    public function  getTableName(){
        return 'ddelivery_tokens';
    }

    public function createStorage()
    {
        // TODO: Implement createStorage() method.
    }

    public function deleteExpired()
    {
        // TODO: Implement deleteExpired() method.
    }

    public function checkToken($token)
    {
        // TODO: Implement checkToken() method.
    }

    public function createToken($token, $expired)
    {
        // TODO: Implement createToken() method.
    }
}