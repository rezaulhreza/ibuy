<?php
session_start();

    include 'connectPDO.php';  
    //check if user is logged in
    if (isset($_SESSION['userinfo']))
    {
        // if delete is submitted
        if (isset($_POST['delete']))
        {
        
            $stmt = $pdo->prepare('DELETE FROM products WHERE product_id = :id;');
            $values =[
                'id' => $_GET['id']
            ];
            $stmt->execute($values);
            $content = 'You have successfully deleted the product!';
        }
        else if (isset($_POST['cancel']))
        {
        $content = '';
        $x = $_GET['id'];
        // if cancel is submitted, go back to product page
        header("Refresh:0; url=productPage.php?id=$x");
        }
        //display form
        else $content = '<p style= "margin: 20px;"> Are you sure you want to delete this product? </p>
            <form action="deleteSingleProduct.php?id='.$_GET['id'].'" method="POST">
            <input type="submit" value="Yes, delete" name="delete"/>
            <input type="submit" value="No, cancel" name="cancel"/>
            </form>';
    }
    else $content = '<p style ="color: red;"> You must be logged in to delete a product!</p><p style="margin-top: 15px;"><a href="login.php"> Go to log in page</a></p>';

include '../layouts/layout.php';
?>
