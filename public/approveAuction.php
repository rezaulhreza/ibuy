
<?php
session_start();

    include 'connectPDO.php';
    $content = '';
// check if user is logged in
    if (isset($_SESSION['userinfo']))
    {
// id is passed via GET global
        if (isset($_GET['id']))
        {
            // the product of id passed via GET gets approved status
            $stmt = $pdo->prepare('UPDATE products SET status = :s where product_id = :id;');
            $values = [
            's' => 1,
            'id' => $_GET['id']
            ];

            if ($stmt->execute($values)) $content = '<p>The action has been successfully released for public bidding.</p>';
                else $content = '<p> Something went wrong. Please try again!';
        }
        else $content = '<p> We were not able to process your request.</p>';
    }
    else $content = '<p style ="color: red;"> You must be logged in to approve an auction!</p><p style="margin-top: 15px;"><a href="login.php"> Go to log in page</a></p>';

include '../layouts/layout.php';

?>