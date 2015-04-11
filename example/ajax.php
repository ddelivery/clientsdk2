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
    $container->getUi()->render($_GET);
}catch ( \Exception $e){
    echo $e->getMessage();
    echo '<pre>';
    print_r($e->getTrace());
    echo '</pre>';
}
