<?php

require_once __DIR__ . '/../php/const.php';

session_start();
if (!empty($_SESSION)) {
    setcookie("PHPSESSID", $_COOKIE["PHPSESSID"], time() + (86400 * COOKIE_TIME), "/~q240078");
}
