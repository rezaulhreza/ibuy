<?php

session_start();
//includes the method file for generating the 10 listings as content. 
//includes layout for header and footer.
include 'methods.php';
$content = print10listings();
include '../layouts/layout.php';
?>