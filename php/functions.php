<?php

function isPOST(): bool
{
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

function isGET(): bool
{
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

function PHP_SELF(): string
{
    return htmlspecialchars($_SERVER['PHP_SELF']);
}

function createPassword($length = 12): string
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    $maxIndex = strlen($chars) - 1;

    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, $maxIndex)];
    }

    return $password;
}

function checkHoneyPot($post): void
{
    if (!empty($_POST['info'])) {
        die("Attaque détectée. Accès refusé.");
    }
}