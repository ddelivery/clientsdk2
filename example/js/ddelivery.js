if(typeof(DDeliveryIntegration) == 'undefined')
    var DDeliveryIntegration = (function(){

        var style = document.createElement('STYLE');
        style.innerHTML = // Скрываем ненужную кнопку
            " #delivery_info_ddelivery_all a{display: none;} " +
                "#ddelivery_container { overflow:hidden;background: #eee;width: 1000px; margin: 0px auto;padding: 0px; }"+
                "#ddelivery_cover > * {-webkit-transform: translateZ(0px);}"+
                "#ddelivery_cover {zoom: 1;z-index:9999;position: fixed;bottom: 0;left: 0;top: 0;right: 0; overflow: auto;-webkit-overflow-scrolling: touch;background-color: #000; background: rgba(0, 0, 0, 0.5); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr = #7F000000, endColorstr = #7F000000); "
        var body = document.getElementsByTagName('body')[0];
        body.appendChild(style);
        var div = document.createElement('div');
        //div.innerHTML = '<div id="ddelivery_popup"></div>';
        div.id = 'ddelivery_container';
        body.appendChild(div);

        function delivery(objectId, componentUrl, params, callbacks){
            var iframe = document.createElement('iframe');

            iframe.style.width = params.width + 'px';
            iframe.style.height = params.height + 'px';
            iframe.style.overflow = 'hidden';
            iframe.scrolling = 'no';
            iframe.frameBorder = 0;
            iframe.style.borderWidth = 0;

            iframe.src = componentUrl;
            var object = document.getElementById(objectId);
            object.style.height = '630px';
            object.innerHTML = '';
            object.appendChild(iframe);


            if(typeof(callbacks)!='object'){
                callbacks = false;
            }
            var message = function (event) {
                // Не наше окно, мы его не слушаем
                if(iframe.contentWindow != event.source) {
                    return;
                }
                var data;
                eval('data = '+event.data);
                var result;
                if (typeof(callbacks[data.action]) == 'function') {
                    result = callbacks[data.action](data.data);

                }
                if( result !== false ) {
                    if (data.action == 'close') {
                        //iframe.parentNode.removeChild(iframe);
                    }
                }
                //hideCover();
            };
            if (typeof (window.addEventListener) != 'undefined') { //код для всех браузеров
                window.addEventListener("message", message, false);
            } else { //код для IE
                window.attachEvent("onmessage", message);
            }

            iframe.contentWindow.params = params;
        }

        function showPrompt() {
            var cover = document.createElement('div');
            cover.id = 'ddelivery_cover';
            cover.appendChild(div);
            document.body.appendChild(cover);
            document.getElementById('ddelivery_container').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

       function  hideCover() {
            document.body.removeChild(document.getElementById('ddelivery_cover'));
            document.getElementsByTagName('body')[0].style.overflow = "";
        }

        return {
            onOpen:function(){
                alert("он опен");
            },
            onClose:function(data){
               alert("Переопределите событие onClose объекта DDeliveryIntegration");
               console.log(data);
            },
            onChange:function(data){
                alert("Переопределите событие onClose объекта DDeliveryIntegration");
                console.log(data);
            },
            openPopup:function(data){
                var th = this;
                var params = {};
                th.onOpen();
                showPrompt();

                if(typeof(data) == 'undefined' || typeof( data.width) == 'undefined')
                    params.width = 1000;
                if(typeof(data) == 'undefined' || typeof( data.height) == 'undefined')
                    params.height = 630;

                delivery('ddelivery_container', 'ajax.php?action=shop', params, {
                    close: th.onClose,
                    change:th.onChange
                });
            }
        }
})();

