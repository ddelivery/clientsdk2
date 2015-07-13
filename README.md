Client SDK DDelivery
================================
clientSDK для быстрой разработки клиентских решений для сервиса доставки DDelivery.

Структура директорий
-------------------

      application/        Исходники сдк
      example/            Пример создания расширения


Запуск рабочего примера
-----------------------
Для запуска примера необходимо загрузить сдк в директорию в которой будет находится модуль.
example/IntegratorAdapter.php - класс в котором необходимо переопрелить указанные методы, является связующим свеном между clientSdk и CMS
example/ajax.php - файл который является точкой входа в CMS и запускает на выполнение clientSDK
example/index.php - пример кнопки для открытия модуля  на странице оформления заказа
example/db.sqlite - хранилище которое использует сдк

Запустите файл index.php в директории example/, для запуска примера достаточно использовать тестовый апи ключ.
Из коробки пример работы модуля должен запустится корректно, в дальнейшем при разработке
интеграции рекомендуется руководствоватся примером из example/

Создание интеграции
-----------------------
clientSDK содержит все механизмы для работы модуля, необходимо только выполнить несколько этапов:

1. Переопределить методы родительского  класса Adapter(пример IntegratorAdapter.php)
2. Определить учитывая особенность CMS точку входа в модуль, подключить нужные системные скрипты для CMS и вызвать все необходимые методы
для работы clientSDK(пример ajax.php)
3. Создать локальное хранилище на стороне CMS вызвав метод initStorage класса Business (Далее продумать вызов этого метода при установке модуля в CMS)
4. Привязать апи ключ магазина с центральной административной панелью (CAP)
5. Вывести способ доставки на стороне CMS(пример index.php), для этого к странице checkout необходимо подключить js файл после чего
используя свойства объекта DDeliveryIntegration
6. При окончании оформления заказа (в момент когда цмс вставляет в БД заказ и выбран способ оплаты клиентом) необходимо вызвать метод
onCmsOrderFinish класса Business
7. При сохранении заказа перепроверить валидность данных доставки через метод getOrder  класса Business (получить заказ по id, выданным через модуль )
8. При изменении статуса заказа или по какомуто другому событию вызвать метод cmsSendOrder класса Business

1.Переопределить методы родительского  класса Adapter
-------------------------------------------------------
В первую очередь необходимо переопределить абстрактные методы, при необходимости можно  переопределить
родительские


Получить апи ключ из настроек. Привязка с CAP происходит при первом вхождении
через точку входа по ссылке ajax.php?action=admin, при этом происходит проверка
наличия прав администратора CMS и переход в CAP где можно проводить гибкую настройку
правил доставки
```
public function getApiKey(){
    return 'api_key';
}
```


Название CMS
```
public function getCmsName(){
    return "Joomla";
    return "Bitrix";
    ...
}
```

Версия CMS
```
public function getCmsVersion(){
    return '1.1';
}
```


Получить содержание корзины. В зависимости от того  где CMS хранит содержимое корзины(сессия, БД, cookies),
ее необходимо преобразовать в массив как в примере IntegratorAdapter.php

```
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
                        ),
                        array(
                            ...
                        );
            );
}
```
Получить скидку для клиента, для того чтобы отнять ее от общей стоимости заказа

```
public function getDiscount(){
    return 50;
}
```

Проверка пользователя на наличия прав администратора CMS

```
public function isAdmin(){
        if($_SESSION['admin'] == 1){
            return true;
        }
        return false;
}
```


clientSDK предусматривает автоматическую синхронизацию  магазина со статусами
DDelivery.ru
При синхронизации статусов заказов необходимо произвести UPDATE полей статуса заказа
в БД заказов. Синхронизация будет проводится 2 раза в сутки

В качестве аргумента передается массив $order
```
array(
           'id' => 'status',
           'id2' => 'status2',
 );
```
, где 'id' - идентификатор заказа CMS, 'status' - значение статуса для CMS(соответствие статусов
выставляется в CAP)
Названия полей и таблиц уникальны для каждой CMS
```
public function changeStatus(array $orders){
        foreach($orders as $key=>$item){
            $query = "UPDATE orders_table_cms SET status_cms=$item WHERE order_id=$key"
        }
}
```
Получить поля заказа из CMS по идентификатору
Значения ключей:
'city' => город назначения,
'payment' => тип оплаты,
'status' => статус заказа,
'sum' => сумма заказа,
'delivery' => стоимость доставки

```
public function getOrder($id){
    return array(
            'city' => 'Урюпинск',
            'payment' => 22,
            'status' => 'Статус',
            'sum' => 2200,
            'delivery' => 220,
        );
}
```

Получить поля списка заказов за период c $from $to заказа из CMS
$from - строка в формате 'Y.m.d'
$to - строка в формате 'Y.m.d'

```
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
                    )
                );
}
```

Получить список полей пользователя в виде массива с предопределенным ключом
Необходимо это для того чтобы при окончании оформления заказа в модуле поля заполнялись автоматически

Если это зарегистрированный пользователь то возможно CMS хранитв сессии эти данные,
можно взять их оттудова, или передать через URL при инициализации модуля

```
public function getUserParams($request){
    return array(
                self::USER_FIELD_STREET => 'Цветаевой',
                self::USER_FIELD_COMMENT => 'Комментарий',
                self::USER_FIELD_HOUSE => '2а',
                self::USER_FIELD_FLAT => '123',
                self::USER_FIELD_ZIP => '10101'
            );
}
```


Получить список статусов заказов из CMS - в дальнейшем они
атоматически подтягиваются в CAP  и можно настраивать соответствие  статусов

```
public function getCmsOrderStatusList(){
        return array('10' => 'Завершен', '11' => 'Куплен');
    }
```


Получить список способовопланы для настройки  в CAP  способа оплаты соответствующему
наложенному платежу
```
public function getCmsPaymentList(){
        return array('14' => 'Наличными', '17' => 'Карточкой');
}
```

Получить список способовопланы для настройки  в CAP  способа оплаты соответствующему
наложенному платежу
```
public function getCmsPaymentList(){
        return array('14' => 'Наличными', '17' => 'Карточкой');
}
```


Есть возможность добавлять свои кастомные поля в CAP и потом получать их локально
self::FIELD_TYPE_TEXT - текстовое поле
self::FIELD_TYPE_CHECKBOX - чекбокс
self::FIELD_TYPE_LIST - список
```
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
```

Получить настройки БД
```
    public function getDbConfig(){
        return array(
                    'pdo' => new \PDO('mysql:host=localhost;dbname=ddelivery', 'root', '0', array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")),
                    'prefix' => '',
         );
    }
```

Для редактирования заказа необходимо переопределить методы получения товаров заказа
 
```

```

2.Определить учитывая особенность CMS точку входа в модуль
-------------------------------------------------------
Чтобы запустить обработчик clientSDK, необходимо его получить из контейнера.
Объект DDelivery\Adapter\Container - позволяет получать объекты,
на вход параметром получает Adapter

```
$adapter = new IntegratorAdapter();
$container = new Container(array('adapter' => $adapter));
```

Например точку входа в clientSDK можно получить вызвав метод,
$_REQUEST - массив с GET и POST параметрами запроса
```
$container->getUi()->render($_REQUEST);
```
С помощью контейнера можно  получать и другие объекты при необходимости
например чтобы выполнить этап 3 или 4, необходимо получить объект Business из контейнера, для этого
создаем контейнер и вызываем соответствующий метод.

```
$adapter = new IntegratorAdapter();
$container = new Container(array('adapter' => $adapter));
$business = $container->getBusiness();
```
Если неоходима более гибкаая настройка объектов, хранилищ, есть возможность 
самостоятельно переопределить Container и сконфигурировать все объекты 
согласно интерфейсов и clientSDK будет работать с ними

3.Создание хранилищ
--------------------
Для работы модуля на стороне CMS необходимо создать хранилища.
Для начала необходимо сконфигурировать в адаптере настройки БД и вызвать метод
initStorage() класса Business, возпользовавшись контейнером:
```
$adapter = new IntegratorAdapter();
$container = new Container(array('adapter' => $adapter));
$business = $container->getBusiness();
// Создаем хранилища
$business->initStorage();
```
4.Привязка апи ключа магазина с центральной административной панелью (CAP)
------------------------------------------------------------------------
Для того чтобы осуществить привязку необходимо перейти по ссылке(при условии что привязки
по этому апи ключу ранее не было, иначе нужно осуществить сброс привязки - 
через личный кабинет cabinet.ddelivery.ru  ), у которой url http://точка входа/?action=admin  .
Например http://site/ddelivery/ajax.php?action=admin

5. Вывести способ доставки на стороне CMS
-----------------------------------------
Для этого на страницу оформления заказа нужно подключить скрипт
```
<script src="http://sdk.ddelivery.ru/assets/js/ddelivery_v2.js"></script>
```
Вставить элемент в котором инициализируется модуль
```
<div id="ddelivery_container_place"></div>
```
После подключения будет в js доступен объект DDeliveryModule, он позволяет открывать 
окно выбора пункта доставки.
Для начала нужно определить js  методы в виде объекта и передать их параметром

```
params{
   url: 'ajax.php?action=module', 
   width: 550,
   height: 440,
}
callbacks = {
   open: function(){
             //alert("Хук на открытие окна");
             console.log("Хук на открытие окна");
             // если false окно  не откроется
   return true;
   },
   change: function(data){
           console.log(data);
           console.log("Хук на окончание оформления заказа и обработка результата");
   },
   close_map: function(data){
           console.log('xxxx');
           console.log("Хук на закрытие карты");
   },
   price: function(data){
           console.log("Хук изменение цены, и получение возможности НПП текущей компании");
           console.log(data);
   }
}
// 'ddelivery_container_place' - идентификатор div-a в котором инициализируется модуль
DDeliveryModule.init(params, callbacks, 'ddelivery_container_place');
```
это позволит получать результат оформления доставки и обрабатывать его

При окончании оформления доставки во время вызова метода change(см. параметр callbacks) присылаются данные
с информацией про доставку в виде js объекта

```
city: "151184"  - id города доставки
city_name: "г. Москва" - Город доставки
client_price: 281.49 - Цена доставки
company: "20" - id компании доставки
company_name: "DPD Parcel" - Название компании доставки
id: 1198 - id заказа в сдк (не путать с заявкой на ddelivery.ru)
info: "Курьерская доставка, Кунили  10, xxxxxx, xxxxxxxxxxx, ID компании:20, г. Москва" - описание в виде строки
payment_availability: 1 - возможность наложенного платежа
point: 0 - id точки
to_flat: "122" - квартира
to_house: "15" - дом
to_street: "Цветаева" - улица
type: 2 - тип доставки
```
Важно  средствами CMS запомнить id, так как в дальнейшем при вызове метода можно получить информацию о заказе
по этому id(для проверки цены например). Тоесть до вызова метода в  пункте 6, необходимо хранить 
значение id, например в сессии или передавать между страницами офрмления заказа в полях формы.

6. При окончании оформления заказа
-----------------------------------------
В момент когда цмс вставляет в БД заказ и выбран способ оплаты клиентом
необходимо вызвать метод  onCmsOrderFinish класса Business для того чтобы осуществить
привязку заказа CMS к id заказа в сдк
```
$adapter = new IntegratorAdapter();
$container = new Container(array('adapter' => $adapter));
$business = $container->getBusiness();
//$payment - id способа оплаты
//$status - id статуса заказа
//$id - id заказа в сдк 
//$cmsId - id заказа в CMS
//$to_name - имя клиента
//$to_phone - номер телефона
//$to_email - email
$business->onCmsOrderFinish($id, $cmsId, $payment, $status, $to_name, $to_phone, $to_email)
```

7.При сохранении заказа перепроверить валидность данных доставки
----------------------------------------------------------------
через метод viewOrder  класса Business, можно повторно получить информацию 
о цене и других полях доставки(те поля что приходят в методе change клиенту)
```
$adapter = new IntegratorAdapter();
$container = new Container(array('adapter' => $adapter));
$business = $container->getBusiness();
// $id - id заказа в сдк 
$business->viewOrder($id)
```

8. Отправка заявки на доставку на сервис DDelivery.ru при изменении статуса заказа
----------------------------------------------------------------------------------
При праильной настройке адаптера в CAP, в разделе "Настройки CMS" доступны настройки
статуса заказа для отправки заявки. В данном случае при смене статуса необходимо
вызвать метод onCmsChangeStatus  класса Business.
```
$adapter = new IntegratorAdapter();
$container = new Container(array('adapter' => $adapter));
$business = $container->getBusiness();
//$id - id заказа в сдк 
//$cmsId - id заказа в CMS
//$payment - id способа оплаты
//$status - id статуса заказа
//$to_name - имя клиента
//$to_phone - номер телефона
//$to_email - email
$business->onCmsChangeStatus($id, $cmsId, $payment, $status, $to_name, $to_phone, $to_email);
```
при этом метод сравнит значение $status со значением в настройках CAP и в зависимости 
от этого отправит заявку

9. Отправка заявки вручную
---------------------------
При вызве метода cmsSendOrder класса Business, отсылается заявка на сервер DDelivery.ru.

```
$adapter = new IntegratorAdapter();
$container = new Container(array('adapter' => $adapter));
$business = $container->getBusiness();
//$id - id заказа в сдк 
//$cmsId - id заказа в CMS
//$payment - id способа оплаты
//$status - id статуса заказа
//$to_name - имя клиента
//$to_phone - номер телефона
//$to_email - email
$business->cmsSendOrder($id, $cmsId, $payment, $status, $to_name, $to_phone, $to_email);
```

9. Редактирование заказа
-------------------------
Для реализации функционала редактирования заказа в административной панели CMS необходимо
переопределить методы getAdminProductCart и getAdminDiscount в адаптере для получения корзины 
уже из административной панели CMS. Встраивание формы редактирования пункта доставки аналогично 
встраиванию модуля выбора доставки, с разницей в js параметрах.
params{
    url: 'ajax.php?action=edit', 
    width: 550,
    height: 440,
}
DDeliveryModule.init(params, callbacks, 'ddelivery_container_place');


Что может  пригодится
--------------------------
Можно сохранять дополнительные параметры для  настроек и использовать в контексте работы модуля,
главное настроить поля в getCustomSettingsFields дочернего  класа Adapter. Эти поля будут
показыватся в CAP, в разделе "Настройки CMS"
После нажатия кнопки Сохранить в CAP, в модуле будет доступен метод

```
$adapter = new IntegratorAdapter();
$container = new Container(array('adapter' => $adapter));
$container->getSettingStorage()->getParam('param_name');
```



