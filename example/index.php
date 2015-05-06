<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<script src="http://devsdk.ddelivery.ru/js/ddelivery.js?10"></script>

<a href="javascript:void(0)" id="select_way" class="trigger">Выбрать точку доставки</a>

<script>

    var select_way = document.getElementById("select_way");
    DDeliveryIntegration.onOpen = function(){
        alert("Хук на открытие окна");
        return true;
    };
    DDeliveryIntegration.onChange = function(data){
        alert("Хук на окончание оформления заказа и обработка результата");
        console.log(data);
    };
    DDeliveryIntegration.onClose = function(data){
        alert("Хук на закрытие окна");
        console.log(data);
    };
    //var select_way = document.getElementById("select_way");
    var params = {
        url: 'ajax.php?action=shop',
        width: 1000,
        height: 650
    };

    select_way.onclick = function(){
        DDeliveryIntegration.openPopup(params);
    };
</script>

</body>
</html>