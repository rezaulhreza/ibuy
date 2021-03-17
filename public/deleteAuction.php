<?php

session_start();

    include 'connectPDO.php';  
    //check if user is logged in
    if (isset($_SESSION['userinfo']))
    {
        //if delete has been submitted
        if (isset($_POST['delete']))
        {
            //prepared statement for deleting auction
            $stmt = $pdo->prepare('DELETE FROM products WHERE product_id = :id;');
            $values =[
                'id' => $_GET['id']
            ];
            if ($stmt->execute($values)) $content = '<p>You have successfully deleted the auction!</p>';
                else $content = '<p>Something went wrong. Please try again!</p>';
        }
        //if cancel has been submitted
            else if (isset($_POST['cancel']))
            {
            $content = '';
            $x = $_GET['id'];
            // go back to list of pending auctions
            header("Refresh:0; url=pending.php?id=$x");
            }
            //display form
            else $content = 
                '<p style= "margin: 20px;"> Are you sure you want to delete this auction? </p>
                <form action="deleteAuction.php?id='.$_GET['id'].'" method="POST">
                <input type="submit" value="Yes, delete" name="delete" id="delete"/>
                <input type="submit" value="No, cancel" name="cancel" id="cancel"/>
                </form>';
            }
    else $content = '<p style ="color: red;"> You must be logged in to delete a product!</p> <p style="margin-top: 15px;"><a href="login.php"> Go to log in page</a></p>';

include '../layouts/layout.php';
?>
