<?php
session_start();
    include_once 'connectPDO.php';
    $content='';

// check if user is logged in
    if (isset($_SESSION['userinfo']))
    {
        // prepared statement selects all products that have a status of unapproved (0)
        $stmt = $pdo->prepare('SELECT * from products WHERE  status = 0 AND ended = 0 ORDER BY pub_date DESC;');
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) $content = 'You have no auctions pending approval.';
        else  
// prints all the auctions returned by the query
        {
            $content.='<h1> Pending auctions </h1>';
            while ($product = $stmt->fetch()) 
            {
                $content.= '
                <article style = "padding: 20px; margin-bottom: 40px;" class="product">
                <img style = " max-width: 35vw; object-fit: cover; height: 38vh; max-height: 38vh;" src="'.$product['image1'].'" alt="product name">
                <section class="details">
                    <h2>'.$product['product_name'].'</h2>
                    <h3>'.$product['category'].'</h3>
                    <p>Auction created by <a href="userProfile.php?id='.$product['user_id'].'">'.$product['user_f_name'].'</a> on '.$product['pub_date']. '<a href="#"></a></p>
                    <p> Buy it now for: £'.$product['buy_it_now_price'].'</p>
                    <p class="price">Current bid: £'.$product['current_bid'].'</p>
                        <time>Time left: '.$product['end_date'].'</time>
                    </section>
                    <section class="description">
                    <p>'.$product['product_description'].'</p>
                    </section>
                    <section>
                    <a style = "margin-right: 15px; background-color: blue; padding: 10px; color: white; text-decoration: none; "href = "approveAuction.php?id='.$product['product_id'].'"> Approve auction </a>
                    <a style = "margin-right: 15px; background-color: blue; padding: 10px; color: white; text-decoration: none;" href = "deleteAuction.php?id='.$product['product_id'].'"> Delete auction </a>
                    </section>   
                    </article>';
            
                }
        }
    }
    else $content = '<p style ="color: red;"> You must be logged in as admininstrator to view pending auctions!</p><p style="margin-top: 15px;"><a href="login.php"> Go to log in page</a></p>';
include '../layouts/layout.php';
?>