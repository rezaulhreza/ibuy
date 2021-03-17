
<?php
 //starts session
session_start();
//includes file in order to acces register method below
include 'methods.php';

$form = '
<form action="addAdmin.php" method="POST">
<label for="first_name"> First Name </label>
<input type="text" name = "first_name" placeholder="First name"/>
<label for="surname"> Surname </label>
<input type="text" name = "surname" placeholder="Surname"/>
<label for="email_address"> Email Address </label>
<input type="email" name = "email_address" placeholder="Email"/>
<label for="pass"> Password </label>
<input type="password" name = "pass" placeholder="Password"/>
<label for="pass_check"> Re-enter password </label>
<input type="password" name = "pass_check" placeholder="Password"/>
<input type="submit" value="Add admin" name="register"/>
</form>';

if (isset($_SESSION['userinfo'])){
        
//calls register method
//the method is called with 0 for a regular user and 1 for admins
        $content=registerUser(1, $form);
        }
else {
//error message to be displayed if page is accessed without loggin in, e.g. via bookmark.
        $content = '<p style ="color: red;"> You must be logged in to add a user!</p><p style="margin-top: 15px;"><a href="login.php"> Go to log in page</a></p>';
    }
include '../layouts/layout.php';
?>
