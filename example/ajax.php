<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/9/15
 * Time: 11:06 PM
 */
use DDelivery\DDeliveryUI;

require(implode(DIRECTORY_SEPARATOR, array(__DIR__,'application','bootstrap.php')));
//echo implode(DIRECTORY_SEPARATOR, array(__DIR__,'application','bootstrap.php'));


$ui = new DDeliveryUI();
$ui->render($_GET);