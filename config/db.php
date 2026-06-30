<?php
// Suppress error output on production — errors go to server log only
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

define('DB_HOST', 'localhost');
define('DB_USER', 'traceworka_summercamp');        
define('DB_PASS', 'SuperSecretPassword123!');            
define('DB_NAME', 'traceworka_summercamp'); 

function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        error_log('Database connection failed: ' . $conn->connect_error);
        die('A database error occurred. Please try again later.');
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}
