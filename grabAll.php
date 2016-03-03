<?php

/* 
* SQL Executive
* Copyright (c) 2014 Ron Bedig <info@rbedig.com>
* GNU General Public License
* You may reproduce, alter and ditribute this software for free
*
* file: grabAll.php
*/

// retrieve data from POST, COOKIE or SESSION

$driver = Utility::cookieGetSet('driver', 'mysql');
$host = Utility::cookieGetSet('host', 'localhost');
$user = Utility::cookieGetSet('user');
$password = Utility::cookieGetSet('password');
$database = Utility::cookieGetSet('database');

session_start();
// retrieve data from POST or SESSION
$timeout = Utility::sessionGetSet('timeout', ini_get('max_execution_time'));
$memory = Utility::sessionGetSet('memory', ini_get('memory_limit'));

$sql = Utility::setMe($_POST['sql']); // the SQL statement