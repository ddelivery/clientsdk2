<?php
use DDelivery\Adapter\Adapter;
use DDelivery\DDeliveryException;

/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/11/15
 * Time: 2:53 PM
 */

class IntegratorAdapter extends Adapter  {

    /**
     *
     * При синхронизации статусов заказов необходимо
     * [
     *      'id' => 'status',
     *      'id2' => 'status2',
     * ]
     *
     * @param array $orders
     * @return mixed
     */
    public function changeStatus(array $orders)
    {
        // TODO: Implement changeStatus() method.
    }

    /**
     * Получить урл апи сервера
     *
     * @return string
     */
    public function getSdkServer(){
        return self::SDK_SERVER_DEV_SDK;
    }

    public function getCmsName()
    {
        // TODO: Implement getCmsName() method.
    }

    public function getCmsVersion()
    {
        // TODO: Implement getCmsVersion() method.
    }

    /**
     * Получить  заказ по id
     * ['city' => город назначения, 'payment' => тип оплаты, 'status' => статус заказа,
     * 'sum' => сумма заказа, 'delivery' => стоимость доставки]
     *
     * город назначения, тип оплаты, сумма заказа, стоимость доставки
     *
     * @param $id
     * @return array
     */
    public function getOrder($id)
    {
        // TODO: Implement getOrder() method.
    }

    /**
     * Получить список заказов за период
     * ['city' => город назначения, 'payment' => тип оплаты, 'status' => 'статус заказа'
     * 'sum' => сумма заказа, 'delivery' => стоимость доставки]
     *
     * город назначения, тип оплаты, сумма заказа, стоимость доставки
     *
     * @param $from
     * @param $to
     * @return array
     */
    public function getOrders($from, $to)
    {
        // TODO: Implement getOrders() method.
    }

    /**
     *
     * Получить поля пользователя для отправки на серверное сдк
     *
     * @param $request
     * @return array
     */
    public function getUserParams($request)
    {
        // TODO: Implement getUserParams() method.
    }

    /**
     * @return array
     */
    public function getProductCart()
    {
        // TODO: Implement getProductCart() method.
    }

    /**
     * URL до скрипта где вызывается DDelivery::render
     * @return string
     */
    public function getPhpScriptURL()
    {
        // TODO: Implement getPhpScriptURL() method.
    }

    /**
     * Получить массив с соответствием статусов DDelivery
     * @return array
     */
    public function getCmsOrderStatusList()
    {
        // TODO: Implement getCmsOrderStatusList() method.
    }

    /**
     * Получить массив со способами оплаты
     * @return array
     */
    public function getCmsPaymentList()
    {
        // TODO: Implement getCmsPaymentList() method.
    }

    /***
     *
     * В этом участке средствами Cms проверить права доступа текущего пользователя,
     * это важно так как на базе этого  метода происходит вход
     * на серверние настройки
     *
     * @return bool
     */
    public function isAdmin(){
        return true;
    }

    /**
     *
     * Получить апи ключ
     *
     * @throws DDeliveryException
     * @return string
     */
    public function getApiKey(){
        return '852af44bafef22e96d8277f3227f0998';
        throw new DDeliveryException("переопределить");
    }

    public function getCustomSettingsFields(){
        return array(
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
                    )
        );

    }
}