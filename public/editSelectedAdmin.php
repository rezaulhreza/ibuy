
<?php

session_start();

    include 'connectPDO.php';
    $content = '';
//boolean to be used for error messages
    $problem = false;

//check if user is logged in
    if (isset($_SESSION['userinfo']))
    {
//if user wants to edit first name
            if (!empty($_POST['first_name'])) 
                {

                $stmt = $pdo->prepare('UPDATE users SET  first_name = :fn  WHERE user_id = :id');
                $values = [
                    'fn' => filter_var(trim($_POST['first_name']), FILTER_SANITIZE_STRING),
                    'id' => $_GET['id']
                ];
                if (!$stmt->execute($values)) $problem = true;
            }

//if user wants to edit surname
            if (!empty($_POST['surname'])) 
            {
                $stmt = $pdo->prepare('UPDATE users SET surname = :sur WHERE user_id = :id');
                $values = [
                    'sur' => filter_var(trim($_POST['surname']), FILTER_SANITIZE_STRING),
                    'id' => $_GET['id']
                ];
                if (!$stmt->execute($values)) $problem = true;
            }

//if user wants to edit password
            if (!empty($_POST['pass'])) 
            {
                $stmt = $pdo->prepare('UPDATE users SET password = :pass WHERE user_id = :id');
                $values = [
                    'pass' => password_hash(trim($_POST['pass']), PASSWORD_DEFAULT),
                    'id' => $_GET['id']
                ];
                if (!$stmt->execute($values)) $problem = true;
            }

//test boolean for errors
            if ($problem==true) $content = '<p> There was a problem. Please try again!</p>';
                else {
                        $content = 'The admin has been successfully updated!';
                        if ($_SESSION['userinfo']['user_id']==$_GET['id'])
                    {
                        $content.='<p>For security purposes you are now being logged out...</p>';
                        header("Refresh:5; url=logout.php");
                        }
                    }

    }
    else $content = '<p style ="color: red;"> You must be logged in to edit a category!</p><p style="margin-top: 15px;"><a href="login.php"> Go to log in page</a></p>';

include '../layouts/layout.php';
?>
