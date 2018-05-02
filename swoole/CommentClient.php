<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<?php
$key = '^manks.top&swoole$';
$uid = isset($_GET['uid']) ? intval($_GET['uid']) : 0;
$token = md5(md5($uid) . $key);
?>

<div>
    发送内容：<textarea name="content" id="content" cols="30" rows="10"></textarea><br>
    发送给谁：<input type="text" name="toUid" value="" id="toUid"><br>
    <button onclick="send();">发送</button>
</div>
<script src="https://cdn.bootcss.com/blueimp-md5/2.10.0/js/md5.js"></script>
<script>
   /* var ws = new WebSocket("ws://192.168.153.128:9501?uid=<?php //echo $uid ?>&token=<?php //echo $token; ?>");
    ws.onopen = function(event) {
    };
    ws.onmessage = function(event) {
        var data = event.data;
        data = eval("("+data+")");
        alert(data.msg);
        /!*if (data.event == 'alertTip') {
            alert(data.msg);
        }*!/
    };
    ws.onclose = function(event) {
        console.log('Client has closed.\n');
    };
    function send() {
        var obj = document.getElementById('content');
        var content = obj.value;
        var toUid = document.getElementById('toUid').value;
        var params = {
            "content":content,
            "event":"alertTip",
            "toUid": toUid
        };
        params = JSON.stringify(params);
        //ws.send('{"event":"alertTip", "toUid": '+toUid+'}');
        ws.send(params);
    }*/
</script>
<script>

    var ws;//websocket实例
    var key = '^manks.top&swoole$';
    var uid = "<?php echo $uid ?>";
    var token = md5(md5(uid)+key);
    var lockReconnect = false;//避免重复连接
    //var url = 'ws://192.168.153.128:9501';
    var url = 'wss://swoole.example.com'; //配域名需要 反向代理NGINX 配websocket 协议 wss 属于加密协议需要安装ca证书 ws不用
    var wsUrl = url+"?uid="+uid+"&token="+token;
    console.log(wsUrl);

    function createWebSocket(url) {
        try {
            ws = new WebSocket(url);
            initEventHandle();
        } catch (e) {
            reconnect(url);
        }
    }

    function initEventHandle() {
        ws.onclose = function () {
            reconnect(wsUrl);
        };
        ws.onerror = function () {
            reconnect(wsUrl);
        };
        ws.onopen = function () {
            //心跳检测重置

            heartCheck.reset().start();
        };
        ws.onmessage = function (event) {
            //如果获取到消息，心跳检测重置
            var data = event.data;
            data = eval("("+data+")");
            alert(data.msg);
            if (data.event == 'alertTip') {
            alert(data.msg);
            }
            console.log(event);
            //拿到任何消息都说明当前连接是正常的
            heartCheck.reset().start();
        }
    }

    function reconnect(url) {
        if(lockReconnect) return;
        lockReconnect = true;
        //没连接上会一直重连，设置延迟避免请求过多
        setTimeout(function () {
            createWebSocket(url);
            lockReconnect = false;
        }, 2000);
    }

    //心跳检测
    var heartCheck = {
        timeout: 60000,//60秒 60000
        timeoutObj: null,
        serverTimeoutObj: null,
        reset: function(){
            clearTimeout(this.timeoutObj);
            clearTimeout(this.serverTimeoutObj);
            return this;
        },
        start: function(){
            var self = this;
            this.timeoutObj = setTimeout(function(){
                //这里发送一个心跳，后端收到后，返回一个心跳消息，
                //onmessage拿到返回的心跳就说明连接正常
                ws.send("2");//ws.send(""); 发送内容不可为空不然心跳测试会没效果
                self.serverTimeoutObj = setTimeout(function(){//如果超过一定时间还没重置，说明后端主动断开了
                    ws.close();//如果onclose会执行reconnect，我们执行ws.close()就行了.如果直接执行reconnect 会触发onclose导致重连两次
                }, self.timeout);
            }, this.timeout);
        }
    }

    function send() {
        heartCheck.reset();
        var obj = document.getElementById('content');
        var content = obj.value;
        var toUid = document.getElementById('toUid').value;
        var params = {
            "content":content,
            "event":"alertTip",
            "toUid": toUid
        };
        params = JSON.stringify(params);
        //ws.send('{"event":"alertTip", "toUid": '+toUid+'}');
        ws.send(params);
        heartCheck.start();
    }
    createWebSocket(wsUrl);

</script>
</body>
</html>
