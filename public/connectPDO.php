<?php
$server ='v.je';
$user = 'student';
$pass = 'student';
$schema = 'csy2028';

// connect to the Data base with the credentials above
// this file must be included on al pages

$pdo = new PDO('mysql:dbname=' .$schema. ';host=' . $server, $user, $pass,
 [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

?>