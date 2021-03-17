<?php
session_start();

//must include methods.php in order to acess registerUser() method
include 'methods.php';

//store form in a variable to be passed to the registerUser method
$form =
'<form action="register.php" method="POST">
<label for="first_name"> First Name </label>
<input type="text" name = "first_name" placeholder="Alphabetical values only!"/>
<label for="surname"> Surname </label>
<input type="text" name = "surname" placeholder="Alphabetical values only!"/>
<label for="email_address"> Email Address </label>
<input type="email" name = "email_address" placeholder="user@mail.com"/>
<label for="pass"> Password </label>
<input type="password" name = "pass" placeholder="Enter password" />
<label for="pass_check"> Re-enter password </label>
<input type="password" name = "pass_check" placeholder="Same password as above"/>
<input type="submit" value="Register" name="register"/>
</form>';

// the method is called with 0 for a regular user and 1 for admins
$content = registerUser(0, $form); 
include '../layouts/layout.php';
?>
