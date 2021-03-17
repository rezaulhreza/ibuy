<?php
//session must be started before being unset
session_start();

// unset existing session
unset($_SESSION['loggedin']);
unset($_SESSION['userinfo']);
include 'methods.php';

// print log in form
$content = '<p style="margin-left:15px; margin-bottom:40px;">You have been logged out... Please log in!</p>
<form action="login.php" method="POST">
<label for="email_address"> Email Address </label>
<input type="email" name = "email_address" placeholder="user@mail.com"/>
<label for="password"> Password  </label>
<input type="password" name = "password" placeholder="Enter password"/>
<input type="submit" value="login" name="login"/>
</form>';
include '../layouts/layout.php';
?>