<?php

include_once 'Authorization.php';

$conn = new PDO('mysql:host=localhost;dbname=authorization', 'root', '');

$auth = new Authorization($conn);

$result = $auth->login('mathues', 'test');

if ($result !== true) {
    var_dump($result);
    exit;
}

$result = $auth->logout();

var_dump($result);