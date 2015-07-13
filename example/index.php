<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<script src="http://sdk.ddelivery.ru/assets/js/ddelivery_v2.js"></script>
<a href="javascript:void(0)" id="select_way">Выбрать</a>
<a href="javascript:void(0)" id="send_order">Кнопка отправки заказа</a>
<div id="ddelivery_container_place"></div>
<script>
    var
        params = {
            url: 'ajax.php?action=module',
            width: 550,
            height: 440
        },
        send_order = document.getElementById('send_order'),
        select_way = document.getElementById('select_way');
    callbacks = {
        open: function(){
            //alert("Хук на открытие окна");
            console.log("Хук на открытие окна");
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
            console.log(data);
        }
    };
    /**
     * Перед отправкой инициализируем модуль
     */
    select_way.onclick = function() {
        DDeliveryModule.init(params, callbacks, 'ddelivery_container_place');
    }
    /**
     * Перед отправкой скрипт проводит валидацию
     */
    send_order.onclick = function(){
        DDeliveryModule.sendForm({
            success:function(){
                alert("Функция отправки формы");
            },
            error:function(){
                alert(DDeliveryModule.getErrorMsg());
            }
        });
    };
</script>

</body>
</html>