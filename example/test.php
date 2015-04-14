<?php
header('Content-Type: text/html; charset=utf-8');
/**
* Created by PhpStorm.
* User: mrozk
* Date: 4/9/15
* Time: 11:06 PM
*/
use DDelivery\Adapter\Container;

error_reporting(E_ALL);

require(implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'application','bootstrap.php')));
try{
    require('IntegratorAdapter.php');
    //echo implode(DIRECTORY_SEPARATOR, array(__DIR__,'application','bootstrap.php'));
    $adapter = new IntegratorAdapter();
    $container = new Container(array('adapter' => $adapter));
    //$container->getTokenStorage()->createStorage();
    //print_r($container->getBusiness()->generateToken());
    //print_r($container->getTokenStorage()->checkToken('b6e81e845567eb5aec764c84d58e066c'));
    //echo '<pre>';
    //print_r($container->getTokenStorage()->deleteExpired());
    //echo '</pre>';
    //echo '<pre>';payment_list
    print_r($container->getSettingStorage()->getParam('payment_list'));
    //echo '</pre>';
    //generateToken
    //$container->getTokenStorage()->cre
    //$container->getUi()->render($_GET);
}catch ( \Exception $e){
    echo $e->getMessage();
    echo '<pre>';
    print_r($e->getTrace());
    echo '</pre>';
}