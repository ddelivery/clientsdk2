<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/22/15
 * Time: 8:16 PM
 */

class TestIntegratorAdapter extends \DDelivery\Adapter\Adapter {

    public function getPathByDB(){
        return __DIR__ . '/db.sqlite';
    }

    /**
     *
     * Получить апи ключ
     *
     * @return string
     * @throws \DDelivery\DDeliveryException
     */
    public function getApiKey(){
        // TODO: Implement getApiKey() method.
    }

    /**
     *
     * При синхронизации статусов заказов необходимо
     * [
     *      'id' => 'status',
     *      'id2' => 'status2',
     * ]
     *
     * @param array $orders
     * @return bool
     */
    public function changeStatus(array $orders)
    {
        // TODO: Implement changeStatus() method.
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
     *
     * Получить скидку
     *
     * @return float
     */
    public function getDiscount()
    {
        // TODO: Implement getDiscount() method.
    }

    /**
     *
     *
     *
     * @return array
     */
    public function getProductCart()
    {
        // TODO: Implement getProductCart() method.
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
    public function isAdmin()
    {
        // TODO: Implement isAdmin() method.
    }
}