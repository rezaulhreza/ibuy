<?php

session_start();
    include 'connectPDO.php';
    include 'methods.php';

//if form is submitted
    if (isset($_POST['login'])) 
    {
//if both fields have been filled in
        if (!empty($_POST['email_address']) && !empty($_POST['password']))
        {

            $stmt = $pdo->prepare('SELECT *  FROM users WHERE email_address = :email_address;');
            $values = [
// email is trimmed of white space and read in lowercase before being compared to DB entry
            'email_address' => filter_var(strtolower(trim($_POST['email_address'])), FILTER_SANITIZE_EMAIL)
            ];

            $stmt->execute($values);
            $result = $stmt -> fetch();

            if (password_verify($_POST['password'], $result['password'])) 
            {
 // A SESSION should ideally store only ID. For ease of access I hace used an array.   
                $_SESSION['loggedin'] = true;
                $_SESSION['userinfo']= $result;
                $content = '<p>You are now logged in! Have a look at the latest listings!</p>'.print10listings();
            } 
            else  $content= 'Sorry, your account could not be found.';
        }
        else   $content= 'Please fill in all fields in order to log in!';
    }   
// print the form  
    else 
        $content = '
        <form action="login.php" method="POST">
        <label for="email_address"> Email Address </label>
        <input type="email" name = "email_address" value="user@gmail.com"/>
        <label for="password"> Password  </label>
        <input type="password" name = "password" value="user"/>
        <input type="submit" value="login" name="login"/>
        </form>';
        
 include "../layouts/layout.php";

?>