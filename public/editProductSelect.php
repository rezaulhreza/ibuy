<?php
session_start();

    include 'connectPDO.php';
    $content ='';
//check if user is logged in
    if (isset($_SESSION['userinfo']))
    {
//prepared statement selecting all products for a user 
        $stmt = $pdo->prepare('SELECT * from products WHERE  user_id = :id ORDER BY pub_date DESC;');
        $vals =  [
            'id' => $_SESSION['userinfo']['user_id']   
        ];
        $stmt->execute($vals);
        $content ='<h1>Latest Listings</h1>';
// if user has no products that they can edit
        if ($stmt->rowCount() == 0) $content.='You can only edit your own published auctions. You seem to have none...';
            else {
                $content.= '<ul class="productList">';
                
//display list of edittable products
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
                    <p style="margin: 20px 0 20px 0 ;"> <a style = "margin-right: 15px; background-color: blue; padding: 10px; color: white; text-decoration: none;" 
                    href = "editProduct.php?id='.$products['product_id'].'"> Edit product </a></p>
                    
                    </article>
                    </li>';
                
                    }
                $content.='</ul>';
            }
    }
    else  $content = '<p style ="color: red;"> You must be logged in to edit auctions!</p><p style="margin-top: 15px;"><a href="login.php"> Go to log in page</a></p>';
    include '../layouts/layout.php';
?>