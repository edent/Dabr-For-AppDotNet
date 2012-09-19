<?php

require_once 'EZAppDotNet.php';

$app = new EZAppDotNet();

// log in user
$token = $app->setSession(1);

// redirect user after logging in
header('Location: index.php');

?>
