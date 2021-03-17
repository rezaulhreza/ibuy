<?php
session_start();

    include 'connectPDO.php';
    $content = '';
    //check if suer is logged in
    if (isset($_SESSION['userinfo']))
    {
        //prepared statement selects all products for that respective user
    $stmt = $pdo->prepare('SELECT * from products WHERE  user_id = :id ORDER BY pub_date DESC;');
    $vals =  [
        'id' => $_SESSION['userinfo']['user_id']   
    ];
    $stmt->execute($vals);
    $content.='<h1>Latest Listings</h1>';

    // if user has no auctions to delete
    if ($stmt->rowCount() == 0) $content.='You can only delete your own published auctions. You seem to have none...';
        // display auctions     
         else {
            $content.= '<ul class="productList">';
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
                    <p style =" margin: 20px 0 20px 0;"> <a style = "margin-right: 15px; background-color: blue; padding: 10px; color: white; text-decoration: none;" 
                    href = "deleteProduct.php?id='.$products['product_id'].'"> Delete product </a></p>
                </article>
                </li>';
                }
            $content.='</ul>';
        }
    }
    else  $content = '<p style ="color: red;"> You must be logged in to delete auctions!</p><p style="margin-top: 15px;"><a href="login.php"> Go to log in page</a></p>';
include '../layouts/layout.php';
?>