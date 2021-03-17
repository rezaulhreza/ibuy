<?php
session_start();

    include 'connectPDO.php';
    $content = '';

    //check if user is logged in
    if (isset($_SESSION['userinfo']))
    {    
        // if select form is submitted
        if (isset($_POST['selectAdmin'])) 
        {
            $x=$_POST['select'];

            $stmt = $pdo->prepare('SELECT * from users WHERE user_id = :id;');
            $val = [
            'id' => $x
            ];
            $stmt->execute($val);
            $selectedAdmin = $stmt ->fetch();

            //display form for admin details
            $content.='
            <form action="editSelectedAdmin.php?id='.$x.'" method="POST">
            <label for="first_name"> First Name </label>
            <input type="text" name = "first_name" value="'.$selectedAdmin['first_name'].'"/>
            <label for="surname"> Surname </label>
            <input type="text" name = "surname" value="'.$selectedAdmin['surname'].'"/>
            <label for="pass"> Password </label>
            <input type="password" name = "pass" placeholder="********"/>
            <label for="pass_check"> Re-enter password </label>
            <input type="password" name = "pass_check" placeholder="********"/>
            <input type="submit" value="Update" name="update"/>
            </form>';
        }
        //display select form
        else {
            $content.='
            <form action="editAdmin.php" method="POST">
            <label for="select"> Select admin </label>
            <select style ="flex-grow: 1; width: 20vw;  margin-bottom: 1em; margin-right: 2vw;  margin-left: 2vw;" name="select">';
            
            //generate select drop down
            $stmt = $pdo->prepare('SELECT * from users WHERE is_admin = 1;');
            $stmt->execute();
            while ($admin = $stmt->fetch()) 
            {
                $content.= '<option value="' . $admin['user_id'] . '">'.$admin['email_address'].'</option>';
                }
            $content.='
            </select>
            <input type="submit" value="Select" name="selectAdmin"/>
            </form>';
            }
    }
    else $content = '<p style ="color: red;"> You must be logged in to edit admin data!</p><p style="margin-top: 15px;"><a href="login.php"> Go to log in page</a></p>';

include '../layouts/layout.php';

?>