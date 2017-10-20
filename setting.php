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
$query = "SELECT * FROM users WHERE user_id=".$access_token['user_id'];

if ($result = $mysqli->query($query)) {
  $data = $result->fetch_array(MYSQLI_ASSOC);
  $result->free();
}

$mysqli->close();

$php_filename = basename(__FILE__);
$page_title = '팔로하지마 beta 설정';

include_once("header.php");
?>

    <div class="container" style="margin-top: 60px;">
      <div class="page-header">
        <h1>
          <?='<a href="https://twitter.com/'.$access_token['screen_name'].'" target="_blank">@'.$access_token['screen_name'].'</a> 님의 설정 페이지' ?>
          <small><span class="glyphicon glyphicon-cog" aria-hidden="true"></span></small>
        </h1>
      </div>

      <form class="form-horizontal" id="form">
        <div class="form-group">
          <label class="col-sm-2 control-label">작동 여부</label>
          <div class="col-sm-10">
            <input type="checkbox" name="switch" data-handle-width="50" <?=$data['switch']?'checked':''?>>
            <span id="helpBlock" class="help-block">'팔로하지마'의 작동 여부를 선택합니다. OFF 시에는 다른 항목을 변경할 수 없습니다. 변경 후 저장 버튼을 클릭해야 적용됩니다.</span>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label">작동 방식</label>
          <div class="col-sm-10">
            <input type="checkbox" name="isBlock" data-on-color="danger" data-off-color="warning" data-on-text="블락" data-off-text="블언블" data-handle-width="50" <?=$data['isBlock']?'checked':''?> <?=$data['switch']?'':'disabled'?>>
            <span id="helpBlock" class="help-block">'팔로하지마'의 작동 방식을 선택합니다. 블락/블언블을 선택할 수 있습니다. 변경 후 저장 버튼을 클릭해야 적용됩니다.</span>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label">단어 목록</label>
          <div class="col-sm-10">
            <!--<label class="checkbox-inline <?=$data['switch']?'':'disabled'?>">
              <input type="checkbox" id="words_pre" class="words" <?=$data['switch']?'':'disabled'?>
               value="맞팔100"> 맞팔100
            </label>
            <label class="checkbox-inline <?=$data['switch']?'':'disabled'?>">
              <input type="checkbox" id="words_pre" class="words" <?=$data['switch']?'':'disabled'?>
               value="암캐"> 암캐
            </label>
            <label class="checkbox-inline <?=$data['switch']?'':'disabled'?>">
              <input type="checkbox" id="words_pre" class="words" <?=$data['switch']?'':'disabled'?>
               value="일탈"> 일탈
            </label>
            <span id="helpBlock" class="help-block">A block of help text that breaks onto a new line and may extend beyond one line.</span>-->
            <textarea class="form-control words" rows="3" name="words" <?=$data['switch']?'':'disabled'?> placeholder="예시) 맞팔100,암캐,일탈"><?=$data['words']?></textarea>
            <span id="helpBlock" class="help-block">영문 대소문자, 한글, 숫자와 ,(반점)만 입력 가능합니다.</span>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label">홍보 부탁드려요</label>
          <div class="col-sm-10">
            <a href="https://twitter.com/share" class="twitter-share-button" data-url="https://gamjaa.github.io/do-not-follow/" data-text="팔로하지마 beta로 원치 않는 사람의 팔로우를 막아보세요!" data-lang="ko" data-hashtags="팔로하지마" data-dnt="true" data-size="large">트윗하기</a>
            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label">문의/건의사항 등</label>
          <div class="col-sm-10">
            <a href="https://twitter.com/intent/tweet?screen_name=potato_jkw&text=%23%ED%8C%94%EB%A1%9C%ED%95%98%EC%A7%80%EB%A7%88%20" class="twitter-mention-button" data-lang="ko" data-size="large" data-related="potato_jkw" data-dnt="true">Tweet to @potato_jkw</a>
            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-10">
            <button class="btn btn-primary btn-lg" type="button"  data-loading-text="저장 중..." autocomplete="off" id="submit">저장</button>
          </div>
        </div>
    </form>
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
      $('input[name="isBlock"]').bootstrapSwitch('toggleDisabled');
      $('.checkbox-inline').removeClass('disabled');
    } else {
      $('.words').attr("disabled", true);
      $('input[name="isBlock"]').bootstrapSwitch('toggleDisabled');
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
  });
  function onSuccess(json, status){alert('저장 완료!');}
	function onError(data, status){alert('저장 실패!');}
</script>
</body>
