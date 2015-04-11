<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/9/15
 * Time: 11:06 PM
 */
use DDelivery\Adapter\Container;
use DDelivery\DDeliveryUI;

require(implode(DIRECTORY_SEPARATOR, array(__DIR__,'application','bootstrap.php')));
require('IntegratorAdapter.php');
//echo implode(DIRECTORY_SEPARATOR, array(__DIR__,'application','bootstrap.php'));
$adapter = new IntegratorAdapter();
$container = new Container(array('adapter' => $adapter));
$container->getUi()->render($_GET);
