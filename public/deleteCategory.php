<?php

session_start();

    include 'connectPDO.php';
    $content = '';
    //check if user is logged in
    if (isset($_SESSION['userinfo']))
    {
        // if form has been submitted
        if (isset($_POST['deleteCategory']))
        {
            //prepared statement for deleting category 
            $stmt = $pdo->prepare('DELETE FROM categories WHERE category_name = :selection');
            $values = [
                'selection' => $_POST['select']
            ];
            if ($stmt->execute($values)) $content =  "<p>The category has been successfully deleted! </p>";
                else  $content =  "<p>Something went wrong. Try again!</p>";
        }
        //display form
        else {
        $content ='
        <form action="deleteCategory.php" method="POST">
        <label for="select"> Select category </label>
        <select style ="flex-grow: 1; width: 20vw;  margin-bottom: 1em; margin-right: 2vw;  margin-left: 2vw;" name="select">';
        
        $stmt = $pdo->prepare('SELECT category_name from categories;');
        $stmt->execute();
        //print out select drop down
        while ($cat = $stmt->fetch()) 
        {
            $content.= '<option value="' . $cat['category_name'] . '">'.$cat['category_name'].'</option>';
        }
            
            $content.='
            </select>
            <input type="submit" value="Remove" name="deleteCategory"/>
            </form>';
        }
    }
    else $content = '<p style ="color: red;"> You must be logged in to delete a category!</p><p style="margin-top: 15px;"><a href="login.php"> Go to log in page</a></p>';

include '../layouts/layout.php';
?>