<?php
session_start();

    include 'connectPDO.php';  
//check if user is logged in
    if (isset($_SESSION['userinfo']))
    {
// user's won auctions, where he was the highest bidder and have expired
        $stmt = $pdo->prepare('SELECT * from products WHERE user_id = :id AND status = 1 AND :now >= start_date AND bidder_id = :bid_id AND ended = 1 ORDER BY pub_date DESC;');
        $val = [
            'bid_id' => $_SESSION['userinfo']['user_id'],
            'now' =>  date('Y-m-d H:i:s',time()),
            'id' => $_GET['id']
        ];
        $stmt->execute($val);


        $stmt1 = $pdo->prepare('SELECT first_name from users WHERE user_id = :id;');
        $val1 = [
            'id' => $_GET['id']
        ];
        $stmt1->execute($val1);
        $user = $stmt1->fetch();

        if ($user['first_name'] == $_SESSION['userinfo']['first_name']) $name = 'Your';
            else $name = $user['first_name'].'\'s';


        $content='<h1>'.$name.' won  auctions</h1> <ul class="productList">';
        if ($stmt -> rowCount() ==0) $content.='<p>No auctions to display.</p>'; 
        else
                while ($products = $stmt->fetch()) 
                {        
                    $content.= '
                    <li>
                    <img src="'.$products['image1'].'" alt="product name">
                    <article>
                        <h2>'.$products['product_name'].'</h2>
                        <h3>'.$products['category'].'</h3>
                        <p>'.$products['product_description']
                        .'</p>
            
                        <p class="price">Current bid: £'.$products['current_bid'].'</p>
                        <a href="productPage.php?id='.$products['product_id'].'" class="more">More &gt;&gt;</a>
                    </article>
                    </li>';
                    }
                    $content.='</ul>';
       


// user's on going auctions - where he is the highest bidder but they haven't finished 

            $stmt = $pdo->prepare('SELECT * from products WHERE user_id = :id AND status = 1 AND :now >= start_date AND bidder_id = :bid_id AND ended = 0 ORDER BY pub_date DESC;');
            $val = [
                'bid_id' => $_SESSION['userinfo']['user_id'],
                'now' =>  date('Y-m-d H:i:s',time()),
                'id' => $_SESSION['userinfo']['user_id']
            ];
            $stmt->execute($val);

            $stmt1 = $pdo->prepare('SELECT first_name from users WHERE user_id = :id;');
            $val1 = [
                'id' => $_GET['id']
            ];
            $stmt1->execute($val1);
            $user = $stmt1->fetch();
    
            if ($user['first_name'] == $_SESSION['userinfo']['first_name']) $name = 'Your';
                else $name = $user['first_name'].'\'s';


            $content.='<h1>'.$name.' on-going  auctions</h1> <ul class="productList">';
            if ($stmt -> rowCount() ==0) $content.='<p>No auctions to display.</p>'; 
                else
                    while ($products = $stmt->fetch()) 
                    {        
                        $content.= '
                        <li>
                        <img src="'.$products['image1'].'" alt="product name">
                        <article>
                            <h2>'.$products['product_name'].'</h2>
                            <h3>'.$products['category'].'</h3>
                            <p>'.$products['product_description']
                            .'</p>
                
                            <p class="price">Current bid: £'.$products['current_bid'].'</p>
                            <a href="productPage.php?id='.$products['product_id'].'" class="more">More &gt;&gt;</a>
                        </article></li>';
                        }
                        $content.='</ul>';



// User's posted listings
    $stmt = $pdo->prepare('SELECT * from products WHERE user_id = :id AND :now >= start_date ORDER BY pub_date DESC;');
    $val = [
        'now' =>  date('Y-m-d H:i:s',time()),
        'id' => $_SESSION['userinfo']['user_id']
    ];
    $stmt->execute($val);

    $stmt1 = $pdo->prepare('SELECT first_name from users WHERE user_id = :id;');
    $val1 = [
        'id' => $_GET['id']
    ];
    $stmt1->execute($val1);
    $user = $stmt1->fetch();

    if ($user['first_name'] == $_SESSION['userinfo']['first_name']) $name = 'Your';
        else $name = $user['first_name'].'\'s';

    $content .='<h1>'.$name.' Listings</h1> <ul class="productList">';
    if ($stmt -> rowCount() ==0) $content.='<p>No auctions to display.</p>'; 
    else
            while ($products = $stmt->fetch()) 
            {        
                $content.= '
                <li>
                <img src="'.$products['image1'].'" alt="product name">
                <article>
                    <h2>'.$products['product_name'].'</h2>
                    <h3>'.$products['category'].'</h3>
                    <p>'.$products['product_description']
                    .'</p>

                    <p class="price">Current bid: £'.$products['current_bid'].'</p>
                    <a href="productPage.php?id='.$products['product_id'].'" class="more">More &gt;&gt;</a>
                </article></li>';
            }
                $content.='</ul>';
    }
    else  $content = '<p style ="color: red;"> You must be logged in to view this profile!</p><p style="margin-top: 15px;"><a href="login.php"> Go to log in page</a></p>';
    
include '../layouts/layout.php';
?>