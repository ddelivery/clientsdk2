<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<script src="js/ddelivery.js"></script>

<a href="javascript:void(0)" id="select_way" class="trigger">Выбрать точку доставки</a>

<script>
    DDeliveryIntegration.onOpen = function(){
        alert("Мой вариант");
    }
    DDeliveryIntegration.onChange = function(data){
        alert("Цемики");
        console.log(data);
    }
    select_way = document.getElementById('select_way');
    select_way.onclick = function(){
        DDeliveryIntegration.openPopup({});
    };
</script>

</body>
</html>