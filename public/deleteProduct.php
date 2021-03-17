<?php
session_start();

    include 'connectPDO.php';  
    $content = '';
    //check if user is logged in
    if (isset($_SESSION['userinfo']))
    {
        //if user has chosen to delete
        if (isset($_POST['delete']))
        {
            $stmt = $pdo->prepare('DELETE FROM products WHERE product_id = :id;');
            $values =[
                'id' => $_GET['id']
            ];
            if ($stmt->execute($values)) $content = '<p>You have successfully deleted the product!</p>';
                else $content = '<p>You have successfully deleted the product!</p>';
        }
        // if user has chosen to cancel
        else if (isset($_POST['cancel']))
            {
            $content = '';
            //go back to list of products to delete 
            header("Refresh:0; url=deleteProductSelect.php");

            }
            // display form
        else $content = 
                '<p style= "margin: 20px;"> Are you sure you want to delete this product? </p>
                <form action="deleteProduct.php?id='.$_GET['id'].'" method="POST">
                <input type="submit" value="Yes, delete" name="delete"/>
                <input type="submit" value="No, cancel" name="cancel"/>
                </form>';
    }
    else $content = '<p style ="color: red;"> You must be logged in to delete a product!</p> <p style="margin-top: 15px;"><a href="login.php"> Go to log in page</a></p>';

include '../layouts/layout.php';
?>
