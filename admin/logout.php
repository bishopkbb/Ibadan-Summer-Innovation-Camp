<?php
session_start();
require_once('config.php');
$_SESSION[ADMIN_SESSION_KEY] = false;
session_destroy();
header('Location: index.php');
exit;
