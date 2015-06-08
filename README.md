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
                self::USER_FIELD_NAME => 'Сидоров Сережа',
                self::USER_FIELD_EMAIL => 'syd@email.com',
                self::USER_FIELD_PHONE => '79225551234',
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
2.Определить учитывая особенность CMS точку входа в модуль
-------------------------------------------------------
Чтобы запустить обработчик clientSDK, необходимо его получить из контейнера.
Объект DDelivery\Adapter\Container - позволяет получать объекты,
на вход параметром получает Adapter

```
$adapter = new IntegratorAdapter();
$container = new Container(array('adapter' => $adapter));
```

Например точку входа в clientSDK можно получить вызвав метод
```
$container->getUi()->render($_REQUEST);
```
С помощью контейнера можно  получать и другие объекты при необходимости
например чтобы выполнить этап 3 или 4 необходимо получить объект Business, для этого
создаем контейнер


Если неоходима более гибкаая настройка объектов, то можно самостоятельно переопределить
Container и сконфигурировать все объекты, главное чтобы они соответствовали интерфейсу и
clientSDK будет работать с ними

3.Привязать апи ключ магазина с центральной административной панелью (CAP)
-------------------------------------------------------
```
$adapter = new IntegratorAdapter();
$container = new Container(array('adapter' => $adapter));
```
получаем необходимый объект

```
$business = $container->getBusiness();
// Создаем хранилища
$business->initStorage();
```
4.Привязка апи ключа магазина с CAP
------------------------------------
Для того чтобы осуществить привязку необходимо перейти по ссылке(при условии что привязки
по этому апи ключу ранее не было, иначе нужно осуществить сброс привязки - через личный кабинет DDelivery.ru  )
точка входа ajax.php?action=admin

5. Вывести способ доставки на стороне CMS
-----------------------------------------
Для этого на страницу оформления заказа нужно подключить скрипт
```
<script src="http://devsdk.ddelivery.ru/js/ddelivery.js?10"></script>
```
После подключения будет в js доступен объект DDeliveryIntegration,
он позволяет открывать окно выбора точки доставки
сначала нужно переопределить хуки на закрытие модуля и окончание оформление доставки
```
    DDeliveryIntegration.onOpen = function(){
        alert("Хук на открытие окна");
    };
    DDeliveryIntegration.onChange = function(data){
        alert("Хук на окончание оформления заказа и обработка результата");
        console.log(data);
    };
    DDeliveryIntegration.onClose = function(data){
        alert("Хук на закрытие окна");
        console.log(data);
    };
```
это позволит получать результат оформления доставки и прорабатывать его

Далее необходимо определить событие для открытия окна выбора доставки:
```
<a href="javascript:void(0)" id="select_way" class="trigger">Выбрать точку доставки</a>
<script>
    var select_way = document.getElementById("select_way");
    var params = {
            url: 'ajax.php?action=shop',
            width: 1000,
            height: 650
    };
    select_way.onclick = function(){
            DDeliveryIntegration.openPopup(params);
    };
</script>
```
Главное правильно сконфигурировать объект params:
url - в url скрипта, можно добавлять дополнительные поля, например поля пользователя, для  того чтобы
 можно было автоматически заполнять поля в окне выбора доставки
width - ширина окна выбора доставки
height - высота окна выбора доставки

При окончании оформления доставки в метод DDeliveryIntegration.onChange присылаются данные
с информацией про доставку в виде js объекта
```
city: "г. Москва" - Город доставки
clientPrice: 23.85 - Цена доставки
company: "44" - ID компании доставки
js: "change" -
orderId: 9 - sdkId
payment: true - возможность наложенного платежа
type: 2 - тип доставки (1-ПВЗ, 2-курьер)
userInfo:
    comment: "xxxxxx" - комментарий
    email: "dddddddddd@xxxxxx.xxx" - email
    flat: "2" - квартира
    house: "211"
    name: "Червяк Анатолий"
    phone: "+7(333)333-33-33"
    street: "Рижская"
    zip: "1100"
```
Важно  средствами CMS запомнить orderId(sdkId) так как в дальнейшем при вызове метода можно получить информацию о заказе
по этому Id. Тоесть до вызова метода в следующем пункте необходимо хранить значение sdkId, например в сессии или передавать между страницами офрмления заказа.

6. При окончании оформления заказа
-----------------------------------------
В момент когда цмс вставляет в БД заказ и выбран способ оплаты клиентом  необходимо вызвать метод onCmsOrderFinish класса Business
```
$adapter = new IntegratorAdapter();
$container = new Container(array('adapter' => $adapter));
$business = $container->getBusiness();
//$payment - id способа оплаты
//$status - id статуса заказа
//$cmsId - id заказа в CMS
$business->onCmsOrderFinish($sdkId, $cmsId, $payment, $status)
```

7.При сохранении заказа перепроверить валидность данных доставки
----------------------------------------------------------------
через метод getOrder  класса Business, можно получить информацию о цене и других полях доставки
```
$adapter = new IntegratorAdapter();
$container = new Container(array('adapter' => $adapter));
$business = $container->getBusiness();
$business->getOrder($sdkId)
```
8. При изменении статуса заказа или по какомуто другому
-------------------------------------------------------
событию вызвать метод cmsSendOrder класса Business, вызов этого метода отсылает заявку на сервер ddelivery.ru,
для организации забора товара из магазина
```
$adapter = new IntegratorAdapter();
$container = new Container(array('adapter' => $adapter));
$business = $container->getBusiness();
$business->cmsSendOrder($sdkId, $cmsId, $payment, $status);
```

Что еще может  пригодится
--------------------------
Можно сохранять дополнительные параметры для  настроек и использовать в контексте работы модуля,
главное настроить поля в getCustomSettingsFields дочернего  класа Adapter. Эти поля будут показыватся в CAP, в разделе Настройки CMS
После нажатия кнопки Сохранить в CAP, в модуле будет доступен метод
```
$adapter = new IntegratorAdapter();
$container = new Container(array('adapter' => $adapter));
$container->getSettingStorage()->getParam('param_name');
```



