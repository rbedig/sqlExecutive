<?php

/* 
* SQL Executive
* Copyright (c) 2014 Ron Bedig <info@rbedig.com>
* GNU General Public License
* You may reproduce, alter and ditribute this software for free
*
* file: index.php
*/

$request_uri = $_SERVER['REQUEST_URI'];
if (strpos(strtolower($request_uri), 'index.php') !== false) {
	$request_uri = substr($request_uri, 0, strpos(strtolower($request_uri), 'index.php'));
}
$baseURL = 'http://' . $_SERVER['SERVER_NAME'] . $request_uri;

require_once 'class/class-utility.php';
// grab all data from POST, COOKIE
require_once 'grabAll.php';

?>

<!doctype html>
<html lang="en">
<head>

<meta charset="utf-8">
<meta name="author" content="Ron Bedig">

<base href="<?php echo $baseURL; ?>" />

<link rel="stylesheet" href="css/reset.css">
<link rel="stylesheet" href="css/main.css">

<script src="script/sqlExec.js"></script>

<!--[if lt ie 9]>
	<script src="script/html5shiv.js"></script>
<![endif]-->

<title>SQL Executive</title>

</head>

<body>

	<form id="entry-form" method="post" action="sqlSave.php">
		<div id="control">
			<button id="execute" type="submit">execute</button>
			<button id="back" type="submit" value="-1" class="disabled" disabled>&lt;</button>
			<button id="forward" type="submit" value="1" class="disabled" disabled>&gt;</button>			
			<button id="open" type="submit" class="hidden">open</button>
			<input type="file" id="file" name="files[]" class="hidden">
			<button id="save" type="submit" class="hidden">save</button>

			<label>DBMS
				<select id="driver" name="driver">
					<option id="mysql" <?php echo ($driver == 'mysql' ? 'selected' : '')?> value="mysql">MySQL</option>
					<option id="mssql" <?php echo ($driver == 'mssql' ? 'selected' : '')?> value="mssql">MS SQL Server</option>
				</select>
			</label>
			
			<label>Host<input type="text" id="host" name="host"	value="<?php echo $host ?>" size="15"></label> 
			<label>User<input type="text" id="user" name="user" value="<?php echo $user ?>" size="15"></label> 
			<label>Pass<input type="password" id="password" name="password" value="<?php echo $password ?>" size="8"></label> 
			<label>Database<input type="text" id="database"	name="database" value="<?php echo $database ?>" size="15"></label>
			<label>Time<input type="text" id="timeout" name="timeout" value="<?php echo $timeout ?>" size="4"></label>
			<label>Mem<input type="text" id="memory" name="memory" value="<?php echo $memory ?>" size="5"></label>
		</div>

		<div id="query">
			<textarea id="sql" name="sql" autofocus spellcheck="false"><?php echo $sql ?></textarea>
		</div>
	</form>

	<div id="result">
		<!-- the query result goes here -->
	</div>

	<div id="modalLightBox">
		<div id="modalMessage">
			<p id="modalText">
				<!-- message goes here -->
			</p>
			<button id="modalCancel">x</button>
			<button id="modalOk">ok</button>
		</div>
	</div>

</body>
</html>