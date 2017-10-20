<!DOCTYPE html>
<html lang='ko'>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?=$page_title ?></title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/bootstrap-switch.css" rel="stylesheet">
  <style>#block_id{width:70px;}</style>
</head>
<body>
  <?php include_once("analyticstracking.php"); ?>
  <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">팔로하지마 beta</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="<?=SITE?>" target="_blank">소개</a></li>
            <!--<li <?php if($php_filename == 'list.php') echo 'class=active'; ?>><a href="list.php">블락 리스트</a></li>-->
            <li <?php if($php_filename == 'setting.php') echo 'class=active'; ?>><a href="setting.php">설정</a></li>
            <li><a href="https://twitter.com/_gamjaa" target="_blank">@_gamjaa</a></li>
            <li><a href="logout.php">로그아웃</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
