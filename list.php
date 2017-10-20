<?php
session_start();
require 'config.php';

define('SITE', 'https://gamjaa.github.io/do-not-follow/');
if(isset($_SESSION['access_token'])) {
  $access_token = $_SESSION['access_token'];
} else {
  header("Location: sign-in.php");
}

// MySQL 데이터베이스 연결
$mysqli = new mysqli('localhost', DB_ID, DB_PW, 'do-not-follow');

// 연결 오류 발생 시 스크립트 종료
if ($mysqli->connect_errno) {
    die('Connect Error: '.$mysqli->connect_error);
}

// 쿼리문 전송
// user_id, screen_name, oauth_token, oauth_token_secret, words
$query = "SELECT * FROM block WHERE user_id=".$access_token['user_id'];

if ($result = $mysqli->query($query)) {
  $data = $result->fetch_array(MYSQLI_ASSOC);
  $result->free();
}

$mysqli->close();

$php_filename = basename(__FILE__);
$page_title = '팔로하지마 beta 블락 리스트';

include_once("header.php");
?>

    <div class="container" style="margin-top: 60px;">
      <div class="page-header">
        <h1>
          <?='<a href="https://twitter.com/'.$access_token['screen_name'].'" target="_blank">@'.$access_token['screen_name'].'</a> 님의 블락 리스트' ?>
          <small><span class="glyphicon glyphicon-cog" aria-hidden="true"></span></small>
        </h1>
      </div>

      <table class="table table-hover">
        <tr><th>블락한 시각</th><th>블락한 유저</th><th>사용 단어</th><th width="70px">처리</th></tr>
        <tr><td>2017-06-04 20:14:07</td><td>@_gamjaa</td><td>맞팔100</td><td><button id="block_id" class="btn btn-primary" value="1">언블락</button></td></tr>
        <tr><td>2017-06-04 20:14:07</td><td>@_gamjaa</td><td>맞팔100</td><td><button id="block_id" class="btn btn-danger" value="1">블락</button></td></tr>
      </table>
    </div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-switch.min.js"></script>
<script>
  $("[name='switch']").bootstrapSwitch();
  $("[name='isBlock']").bootstrapSwitch();

  $('input[name="switch"]').on('switchChange.bootstrapSwitch', function(event, state) {
    if(state) {
      $('.words').attr("disabled", false);
      $("input[name='isBlock']").attr("disabled", false);
      $('.checkbox-inline').removeClass('disabled');
    } else {
      $('.words').attr("disabled", true);
      $("input[name='isBlock']").attr("disabled", true);
      $('.checkbox-inline').addClass('disabled');
    }
  });

  $('textarea[name="words"]').keydown(function(){
    var words = $(this).val();
    $(this).val(words.replace(/[^A-Za-z0-9\u3131-\u314e\u314f-\u3163\uac00-\ud7a3,]/g, ""));
    var words = $(this).val();
    $(this).val(words.replace(/,,/g, ","));
  });
  $('textarea[name="words"]').keyup(function(){
    var words = $(this).val();
    $(this).val(words.replace(/[^A-Za-z0-9\u3131-\u314e\u314f-\u3163\uac00-\ud7a3,]/g, ""));
    var words = $(this).val();
    $(this).val(words.replace(/,,/g, ","));
  });

  $('#submit').on('click', function () {
    var $btn = $(this).button('loading');
    var formData = $("#form").serialize();
    console.log("switch="+$('input[name="switch"]').is(':checked')+"&words="+$('textarea[name="words"]').val());
		$.ajax({
 					type : "POST",
 					url : "settingSubmit.php",
 					cache : false,
 					data : "switch="+$('input[name="switch"]').is(':checked')+"&isBlock="+$('input[name="isBlock"]').is(':checked')+"&words="+$('textarea[name="words"]').val(),
 					success : onSuccess,
 					error : onError
		});
    $btn.button('reset');
  })
  function onSuccess(json, status){alert('저장 완료!');}
	function onError(data, status){alert('저장 실패!');}
</script>
</body>
