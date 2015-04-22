<?php

/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/22/15
 * Time: 8:26 PM
 */
use DDelivery\Adapter\Container;
use DDelivery\Business\Business;

require '__data/TestIntegratorAdapter.php';
class BusinessTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Business
     */
    public $business;

    protected function setUp(){
        if(!file_exists( __DIR__ . '/__data/db.sqlite')){
            $fp = fopen(__DIR__ . '/__data/db.sqlite', "w");
            fclose($fp);
        }
        $container = new Container(array('adapter' => new TestIntegratorAdapter()));
        $this->business = $container->getBusiness();
    }

    public function testInitStorage(){
        $this->business->initStorage();
        //$this->assertTrue($this->business->initStorage());
    }

    protected function tearDown(){
        //unlink( __DIR__ . '/__data/db.sqlite' );
    }
} 