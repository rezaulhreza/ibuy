<?php
session_start();

    include 'connectPDO.php';
    $content = '';
    //check if user is logged in
    if (isset($_SESSION['userinfo'])) 
    {
        $content = '
        <h1> Buy the item for: '.$_GET['price'].'</h1>
        <div id="pay"></div>';
        // the product of ID passed via GET has its entry updated as to reflect who has bought it and for how much
        $stmt = $pdo->prepare('UPDATE products SET current_bid = :bid, bidder_id = :bid_id, bidder_f_name = :bid_name, ended = 1 WHERE product_id = :id;');
        $values =[
            
            'bid' => $_GET['price'],
            'bid_id' => $_SESSION['userinfo']['user_id'],
            'bid_name' => $_SESSION['userinfo']['first_name'],
            'id' => $_GET['id']
        ];
        if (!$stmt->execute($values)) $content= '<p style="margin-bottom: 30px; margin-left: 10px;">Something went wrong. Please try again!</p>' ;

        } 
    else  $content= '<p style="margin-bottom: 30px; margin-left: 10px; color: red;"> You must log in to purchase this item! </p><p style="margin-top: 15px;"><a href="login.php"> Go to log in page</a></p>' ;

  
include '../layouts/buyLayout.php';
?>
