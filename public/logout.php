<?php
require_once '../classes/user.php';
$user = new User($pdo);
$user->logout();
exit;
