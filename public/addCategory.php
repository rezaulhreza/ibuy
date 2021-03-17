<?php
session_start();

    include 'connectPDO.php';  
    include 'methods.php';

    //form to be displayed
    $form =  '<form action="addCategory.php" method="POST">
    <label for="category"> Category Name </label>
    <input type="text" name = "category" placeholder = "Enter name"/>
    <label for="description"> Description </label>
    <textarea name = "description" placeholder = "Relevant details about the category..."></textarea>
    <input type="submit" value="Add" name="addCategory"/>
    </form>';
    $content = '';
    //check if user is logged in
    if (isset($_SESSION['userinfo']))
    {
    //check if form has been submitted
        if (isset($_POST['addCategory']))
        {
            //in order to add a category, all fields must be filled in
            if (!empty($_POST['category']) && !empty($_POST['description'])) 
        {

            //prepared statement - checks if the category name already exists
            $stmt = $pdo->prepare ('SELECT category_name FROM categories WHERE category_name = :c ;');
            $values = [
                'c' => filter_var(trim($_POST['category']), FILTER_SANITIZE_STRING)
            ];
            $stmt -> execute($values);

            //if the category alredy exists
            if ($stmt->rowCount() > 0) 
                $content = 
                '<p style ="margin-left: 13px; margin-bottom: 30px; margin-top: 50px;">Add a different category, this one already exists!!</p>'.$form;

                //the category doesn't exist
                else {
                    $stmt1 = $pdo->prepare('INSERT INTO categories (category_name, description) VALUES (:category, :description)');
                    $values1 = [
                    'category' => filter_var(trim($_POST['category']),FILTER_SANITIZE_STRING),
                    'description' => filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING)
                    ];
                    //if query is executed with no errors
                    if ($stmt1->execute($values1))  $content = '<p>You have successfully added the category!</p>';
                    //if there are errors in the execution
                        else $content = '<p> Something went wrong. Try again!</p>';
                    }
                        }
                        // if some of the fields are left empty
                        else $content = 'Some of the required fields are missing! Please try again!';

        }
        else $content .= $form;
        }
      //error message to be displayed if page is accessed without loggin in, e.g. via bookmark.
    else $content = '<p style ="color: red;"> You must be logged in to add a category!</p><p style="margin-top: 15px;"><a href="login.php"> Go to log in page</a></p>';

include '../layouts/layout.php';
?>