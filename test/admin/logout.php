<?php
require_once __DIR__ . '/../config/functions.php';

session_destroy();
header('Location: /admin/login.php');
exit;
