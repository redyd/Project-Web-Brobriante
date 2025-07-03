<?php
require __DIR__ . '/inc/session.inc.php';

$_SESSION = array();
setcookie("PHPSESSID", "", time()-3600, "/");
session_destroy();
header('Location: index.php');
exit();