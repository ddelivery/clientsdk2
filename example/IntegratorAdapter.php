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
    public function changeStatus(array $orders){
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

    public function getCmsName(){
        // TODO: Implement getCmsName() method.
    }

    public function getCmsVersion(){
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
    public function getOrder($id){
        return array(
            'city' => 'Урюпинск',
            'payment' => 22,
            'status' => 'Статус',
            'sum' => 2200,
            'delivery' => 220,
        );
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
    public function getOrders($from, $to){
        return array(
                array(
                    'city' => 'Урюпинск',
                    'payment' => 22,
                    'status' => 'Статус',
                    'sum' => 2200,
                    'delivery' => 220,
                ),
                array(
                    'city' => 'г. Москва, Московская область',
                    'payment' => 'Пример оплаты',
                    'status' => 'Статус 222',
                    'sum' => 2100,
                    'delivery' => 120,
                ),
                array(
                    'city' => 'Сити Питер',
                    'payment' => 'Пример оплаты 2',
                    'status' => 33,
                    'sum' => 2100,
                    'delivery' => 120,
                )
        );
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

    public function getDiscount(){
        return 0;
    }

    /**
     *
     * Получить содержимое корзини
     *
     * @return array
     */
    public function getProductCart(){
        return array(
                    array(
                        "id"=>12,
                        "name"=>"Веселый клоун",
                        "width"=>10,
                        "height"=>10,
                        "length"=>10,
                        "weight"=>1,
                        "price"=>1110,
                        "quantity"=>2,
                        "sku"=>"app2"
                    )
        );
    }


    /**
     * Получить массив с соответствием статусов DDelivery
     * @return array
     */
    public function getCmsOrderStatusList(){
        return array('10' => 'Завершен', '11' => 'Куплен');
    }

    /**
     * Получить массив со способами оплаты
     * @return array
     */
    public function getCmsPaymentList(){
        return array('14' => 'Наличными', '17' => 'Карточкой');
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
        return '584ac2d4ef3daa60b297a820385e1c70';
        throw new DDeliveryException("переопределить");
    }

    public function getCustomSettingsFields(){
        return array(
                    array(
                        "title" => "Название (Пример кастомного поля)",
                        "type" => self::FIELD_TYPE_TEXT,
                        "name" => "name",
                        //"items" => getStatusList(),
                        "default" => 0,
                        "data_type" => array("string"),
                        "required" => 1
                    ),
                    array(
                         "title" => "Выводить способ доставки(Пример кастомного поля)",
                         "type" => self::FIELD_TYPE_CHECKBOX,
                         "name" => "checker",
                         "default" => true,
                         "data_type" => array("int"),
                         "required" => 1
                    )
        );

    }
}