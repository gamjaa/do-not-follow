<?php include_once("analyticstracking.php") ?>
<?php
session_start();
require 'twitteroauth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;
require 'config.php';

if(isset($_SESSION['access_token'])) {
  header("Location: setting.php");
} else {
  define('SITE', 'https://gamjaa.github.io/do-not-follow/');
  define('DIR', 'https://www.gamjaa.com/do-not-follow/');
  define('OAUTH_CALLBACK', DIR.'callback.php?url='.$_GET['url']?$_GET['url']:SITE);

  $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);

  $request_token = $connection->oauth('oauth/request_token');

  $_SESSION['oauth_token'] = $request_token['oauth_token'];
  $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

  echo $_SESSION['oauth_token'].'<br>'.$_SESSION['oauth_token_secret'];

  $url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

  header("Location: {$url}");
}
?>
