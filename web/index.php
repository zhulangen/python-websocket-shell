<!DOCTYPE html>

<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <title>Shell</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="./bootstrap/css/bootstrap.css">
    <script src="./bootstrap/js/jquery-2.1.4.js"></script>
    <script src="./bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    var ws;
    function init() {
      // Connect to Web Socket
      ws = new WebSocket("ws://192.168.23.131:8009/");
      // Set event handlers.
      ws.onopen = function() {
        output("onopen\n");
      };
      
      ws.onmessage = function(e) {
        // e.data contains received string.
        output("onmessage: " + e.data);
      };
      
      ws.onclose = function() {
        output("onclose\n");
      };

      ws.onerror = function(e) {
        output("onerror\n");
        console.log(e)
      };
      $(document).ready(function(){
        $("#loginoutbtn").click(function(){

          $.post("login.php",{loginoutbtn:0},function(data,status){
            //window.location.reload();
            location.href="login.html";
          });

        });
      });
    }
    
    function onSubmit() {
      var input = document.getElementById("input");
      // You can send message to the Web Socket using ws.send.
      ws.send(input.value);
      output("send: " + input.value+"\n");
    }
    
    function onCloseClick() {
      ws.close();
    }
    function onClearClick() {
      var log = document.getElementById("log");
      log.value="";
    }
    
    function output(str) {
      var log = document.getElementById("log");
      var escaped = str.replace(/&/, "&amp;").replace(/</, "&lt;").
        replace(/>/, "&gt;").replace(/"/, "&quot;"); // "
      log.value = escaped + log.value ;
    }


  </script>

  <?php
    header("Content-Type: text/html;charset=utf-8");
    require_once('config.php');  
    session_start();

    if(isset($_POST['loginoutbtn']))
    {
      $_SESSION['islogin']=false;
      header("location: login.html");
    }


    if(!isset($_SESSION['islogin'])||$_SESSION['islogin']==false)
    {
      header("location: login.html");
    }
  ?>

  </head>
  <body class="center" onload="init();">
   

<h1>远程脚本</h1>

<form onsubmit="onSubmit(); return false;" name="from1">
    <div class="input-group">
      <select id="input" class="form-control">
      <?php
      $shell=get_my_list();
      foreach ($shell as $key => $value) {
        echo "<option value=$value>$key</option>";
      }
      ?>
        
      </select>
      <span class="input-group-btn">
        <input class="btn btn-default" type="submit" value="执行">
        <button type="button" class="btn btn-warning" onclick="init(); return false;">连接</button>
        <button type="button" class="btn btn-danger" onclick="onCloseClick(); return false;">断开</button>
      </span>
    </div>
</form>

	<textarea id="log" class="form-control" rows="20"></textarea>
	<div  class="text-left">
	    <button type="button" class="btn btn-danger" onclick="onClearClick(); return false;">Clear</button>
	    <button type="button" class="btn btn-danger" id="loginoutbtn">退出登陆</button>
	</div>

  </body>
</html>
