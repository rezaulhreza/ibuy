<?php

session_start();
    include 'connectPDO.php';
    $content = '';

//boolean to be used for error messages
    $problem = false;
//check if user is logged in
    if (isset($_SESSION['userinfo']))
    {
// if user wants to edit category name
                if (!empty($_POST['new_category_name'])) 
                {

                $check = $pdo->prepare('SELECT category_id FROM categories WHERE category_name = :name');
                $val = [
                    'name' => $_POST['new_category_name']
                ];
                $check -> execute($val);

                if ($check->fetch() >= 1) {$content = '<p> Category already exists. Try again!</p>'; $problem=true;}
                        
                    else {

                        $stmt = $pdo->prepare('UPDATE categories SET category_name = :c WHERE category_id = :id');
                        $values = [
                            'c' => trim(strip_tags($_POST['new_category_name'])),
                            'id' => $_GET['id']
                        ];
                        if (!$stmt->execute($values)) $problem = true;
                        else $content = '<p> Category name successfully updated!</p>';
                    }
                }

// if user has used a valid category name or has not changed the category name, then proceed to edit description
                if ($problem==false)
                {
                if (!empty($_POST['new_category_description'])) 
                    {
                        $stmt = $pdo->prepare('UPDATE categories SET description = :d WHERE category_id = :id');
                        $values = [
                                'd' => trim(strip_tags($_POST['new_category_description'])),
                                'id' => $_GET['id']
                            ];
                            if (!$stmt->execute($values)) {$problem = true; $content = '<p> There was a problem. Please try again!'; } 
                            else  $content= "The selected category has been successfully updated!";
                        }
                }

            }
                      
                        
                     
             
    
    else $content.= '<p style ="color: red;"> You must be logged in to edit a category!</p><p style="margin-top: 15px;"><a href="login.php"> Go to log in page</a></p>';

include '../layouts/layout.php';
?>
