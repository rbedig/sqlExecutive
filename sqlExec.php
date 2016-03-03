<?php

/* 
* SQL Executive
* Copyright (c) 2014 Ron Bedig <info@rbedig.com>
* GNU General Public License
* You may reproduce, alter and ditribute this software for free
*
* file: sqlExec.php
*
* Processes the SQL query and outputs formatted html to be returned to the client
*/

require_once 'class/class-utility.php';
// grab all data from POST, COOKIE or SESSION
require_once 'grabAll.php';

// integer, number of seconds. if less than 1 or not intger, infinite
ini_set('max_execution_time', $timeout);
// if integer, kb, suffix MB for mb, non-integer set minimum = 256kb.
ini_set('memory_limit', $memory);

if ($driver == 'mysql') {
	$connectString = 'mysql:host=' . $host . ';dbname=' . $database;
}
// MS SQL only other choice for now
else { 
	// alternative driver={SQL server} user and password can go in connection string as uid= and pwd=
	$connectString = 'odbc:driver={SQL native client};server=' . $host . ';database=' . $database;  
}

// PHP generates warning and exception if host not found
$errSave = error_reporting();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
try {
	$dbh = new PDO($connectString, $user, $password);
} catch (PDOException $e) {
	echo '<div class="resultText error">Database connection failed: ' . $e->getMessage() . '</div>';
	return;
}
error_reporting($errSave);

$dbh->query('SET NAMES utf8');
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$stmnt = $dbh->prepare($sql);

if ($stmnt->execute()) {
	// even if there are no rows, column count will be non-zero for SELECT query
	if ($stmnt->columnCount() == 0) {
		echo '<div class="resultText">' . $stmnt->rowCount() . ' row' . ($stmnt->rowCount() == 1 ? '' : 's') . ' affected</div>';
	}
	else {
		resultSet($stmnt);
	}
}
else {
	$aInfo = $stmnt->errorInfo();
	echo '<div class="resultText error">Database server error: ' . $aInfo[0] . '</div>';
	echo '<div class="resultText error">Driver error: ' . $aInfo[1] . ' ' . $aInfo[2] . '</div>';
}

function resultSet(&$stmnt) {
	$rowCount = 0;
	while ($row = $stmnt->fetch(PDO::FETCH_ASSOC)) {
		
		foreach ($row as $col=>$val) {
			$aCol[$col][] = $val;
		}

		$rowCount++;
	}

	if ($rowCount > 0) {
		$str = '<div class="resultSet clearfix">';
		foreach ($aCol as $head=>$aCell) {
			$width = computeWidth($aCell, $rowCount);
			$str .= '<div class="col ' . $width . '"><span class="head">' . $head . '</span><br>';
			foreach ($aCell as $cell) {
				$str .= '<span class="' . ($rowCount > 1 ? 'cell' : 'cellFree') . '">' . ($cell === null ? 'NULL' : htmlspecialchars($cell)) . '</span><br>';
			}
			$str .= '</div>';
		}
		$str .= '</div>';
		
		echo $str;
	}
	echo '<div class="resultText">' . $rowCount . ' row' . ($rowCount == 1 ? '' : 's') . ' returned</div>';
}

function computeWidth(&$aCell, $count) {
	$sum = 0;
	for ($i = 0; $i < $count; $sum += strlen($aCell[$i]), $i++) {}
	$avg = $sum / $count;

	if ($avg < 5)
		return 'xNarrow';
	else if ($avg < 10)
		return 'narrow';
	elseif ($avg < 20)
		return 'medium';
	elseif ($avg < 40)
		return 'wide';
	else
		return 'xWide';
}