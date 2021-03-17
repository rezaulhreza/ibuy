<?php
session_start();

    include 'connectPDO.php';
    $content='';

    //if form is submitted
    if (isset($_POST['submitSearch'])) {
        //prepared statement uses a wild card to check for similar words in categories name, product name, user names
        $stmt = $pdo->prepare('SELECT * from products WHERE product_name LIKE :search OR product_name LIKE :search  OR category LIKE :search OR user_f_name LIKE :search ORDER BY pub_date DESC;');
        $val = [
            'search' => '%'.strval($_POST['search']).'%'
        ];

        $stmt->execute($val);
        if ($stmt->rowCount()>0)
        {
            //display search results in listings format
            $content='<h1>Your search results </h1> <ul class="productList">';
            while ($products = $stmt->fetch()) {
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
        }
        else $content='<p> We are sorry. No results to display. </p>';
    }


include '../layouts/layout.php';

?>
