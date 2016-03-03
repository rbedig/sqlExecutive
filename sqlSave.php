<?php

/* 
* SQL Executive
* Copyright (c) 2014 Ron Bedig <info@rbedig.com>
* GNU General Public License
* You may reproduce, alter and ditribute this software for free
*
* file: sqlSave.php
*
* Throw the SQL query back at the client with headers that will cause browser to save it as a file
*/

// some old broswsers don't recognize content-disposition
// so use content-type that cannot be displayed
header ('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="query.sql"');
echo $_POST['sql'];

?>