<?php include_once("analyticstracking.php") ?>
<?php
session_start();
if(isset($_SESSION['access_token']) && isset($_POST['switch']) && isset($_POST['words']) && isset($_POST['isBlock'])/* && isset($_POST['words_pre'])*/) {
  $access_token = $_SESSION['access_token'];

  // MySQL 데이터베이스 연결
  $mysqli = new mysqli('localhost', DB_ID, DB_PW, 'do-not-follow');

  // 연결 오류 발생 시 스크립트 종료
  if ($mysqli->connect_errno) {
      die('Connect Error: '.$mysqli->connect_error);
  }

  $pattern = '/[^A-Za-z0-9ㄱ-ㅎㅏ-ㅣ가-힣,]+/';
  $words_pre = ""; //preg_replace($pattern, "", $_POST['words_pre']);
  $words = preg_replace($pattern, "", $_POST['words']);

  // 쿼리문 전송
  // user_id, screen_name, oauth_token, oauth_token_secret, isBlock, words
  $query = "UPDATE users set switch=".($_POST['switch']=='true'?1:0).", isBlock=".($_POST['isBlock']=='true'?1:0).", words='".$words_pre.$words."' WHERE user_id=".$access_token['user_id'];
  //echo $query;
  $mysqli->query($query);

  $mysqli->close();
} else {
  header("HTTP/1.0 400");
}
?>
