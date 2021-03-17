<?php
session_start();


    include 'connectPDO.php';
    $content = '';
    //check if user is logged in
    if (isset($_SESSION['userinfo']))
    {
     // if form has been submitted
        if (isset($_POST['deleteAdmin'])){
            // prepared statement for deleting admin entry 
            $stmt = $pdo->prepare('DELETE FROM users WHERE user_id = :selection');
            $values = [
            'selection' => $_POST['select']
            ];
           
            if($stmt->execute($values)) 
            {
                $content =  "<p>The selected admin has been successfully deleted! </p>";
                if ($_SESSION['userinfo']['user_id'] == $_POST['select']) 
                {
                    $content.='<p> For security purposes you are now being logged out...</p>';
                    header("Refresh:5; url=logout.php");
                }
            }
                else $content =  "<p>Something went wrong. Please try again! </p>";
        }
        //if form has not been submitted, display form
        else {
            $content ='
            <form action="deleteAdmin.php" method="POST">
            <label for="select"> Select admin </label>
            <select style ="flex-grow: 1; width: 20vw;  margin-bottom: 1em; margin-right: 2vw;  margin-left: 2vw;" name="select">';
            
            //print out a select drop down of all users that are admins
            $stmt = $pdo->prepare('SELECT user_id, email_address from users WHERE is_admin = 1;');
            $stmt->execute();
            while ($admin = $stmt->fetch()) {
                $content.= '<option value="' . $admin['user_id'] . '">'.$admin['email_address'].'</option>';
            }
            $content.='
            </select>
            <input type="submit" value="Remove" name="deleteAdmin"/>
            </form>';
            }
    }
    else $content = '<p style ="color: red;"> You must be logged in to delete an administrator!</p><p style="margin-top: 15px;"><a href="login.php"> Go to log in page</a></p>';

include '../layouts/layout.php';
?>