<?php include_once("analyticstracking.php") ?>
<?php
session_start();
session_destroy();
define('SITE', 'https://gamjaa.github.io/do-not-follow/');
header('Location: '.SITE);
?>
