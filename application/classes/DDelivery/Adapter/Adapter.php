<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/9/15
 * Time: 11:55 PM
 */

namespace DDelivery\Adapter;


use DDelivery\DDeliveryException;

abstract class Adapter {

    public $params;

    const SDK_SERVER_SDK = 'http://sdk.ddelivery.ru/api/v1/';

    const SDK_SERVER_STAGE_SDK = 'http://stagesdk.ddelivery.ru/api/v1/';

    const SDK_SERVER_DEV_SDK = 'http://devsdk.ddelivery.ru/api/v1/';

    const SDK_SERVER_DEV_SDK1 = 'http://devsdk1.ddelivery.ru/api/v1/';

    const SDK_SERVER_DEV_SDK2 = 'http://devsdk2.ddelivery.ru/api/v1/';

    const FIELD_TYPE_LIST = 'list';

    const FIELD_TYPE_TEXT = 'text';

    const FIELD_TYPE_CHECKBOX = 'checkbox';



    public function __construct($params = []){
        $this->params = $params;
    }


    /**
     *
     * Получить апи ключ
     *
     * @return string
     * @throws \DDelivery\DDeliveryException
     */
    public function getApiKey(){
        return '852af44bafef22e96d8277f3227f0998';
        throw new DDeliveryException("переопределить");
    }


    /**
     * @return array
     */
    abstract  public function getProductCart();

    /**
     * URL до скрипта где вызывается DDelivery::render
     * @return string
     */
    public abstract function getPhpScriptURL();

    /**
     * Получить урл апи сервера
     *
     * @return string
     */
    public function getSdkServer(){
        return self::SDK_SERVER_SDK;
    }

    /**
     * Получить массив с соответствием статусов DDelivery
     * @return array
     */
    abstract public function getCmsOrderStatusList();


    /**
     * Получить массив со способами оплаты
     * @return array
     */
    abstract public function getCmsPaymentList();


    /***
     *
     * В этом участке средствами Cms проверить права доступа текущего пользователя,
     * это важно так как на базе этого  метода происходит вход
     * на серверние настройки
     *
     * @return bool
     */
    abstract public function isAdmin();


        /***
     *
     * Для формирование настроек на стороне серверного сдк,
     * описание в виде массива
     *
     * @return array
     */
    function getFieldList(){
        return array(
            array(
                "title" => "Способы оплаты",
                "type" => self::FIELD_TYPE_LIST,
                "name" => "payment_list",
                "items" => $this->getCmsPaymentList(),
                "default" => 0,
                "data_type" => array("int", "string", "email"),
                "required" => 1
            ),
            array(
                "title" => "Статусы заказов",
                "type" => self::FIELD_TYPE_LIST,
                "name" => "status_list",
                "items" => $this->getCmsOrderStatusList(),
                "default" => 0,
                "data_type" => array("int", "string", "email"),
                "required" => 1
            ),
            array(
                "title" => "Имячко",
                "type" => self::FIELD_TYPE_TEXT,
                "name" => "name",
                //"items" => getStatusList(),
                "default" => 0,
                "data_type" => array("string"),
                "required" => 1
            ),
            array(
                "title" => "Боксик",
                "type" => self::FIELD_TYPE_CHECKBOX,
                "name" => "checker",
                "default" => true,
                "data_type" => array("int"),
                "required" => 1
            ),
        );
    }
} 