<?php
session_start();

//Note: the current version allows people to review themselves. Admins are also allowed to review users.

    include 'connectPDO.php';

// Selects the product whose id was passed via the get
    $content= ''; 
    $stmt = $pdo->prepare('SELECT * from products WHERE product_id = :id ;');
    $values = [
    'id'=> $_GET['id']
    ];
    $stmt->execute($values);

    if ($stmt->rowCount() == 1 ) 
    {
    $product = $stmt->fetch();

    $ended = $product ['ended']; // save variables for display 
    $user = $product['user_f_name']; // save user details for review
    $x = $_GET['id']; // used for header url

// display user access buttons - edit / delete
    $access='';
    if (isset($_SESSION['userinfo']['user_id']))
        {
            if ($product['user_id'] == $_SESSION['userinfo']['user_id']) 
                $access = '
                <section style="margin-top: 20px;">
                <a style = "margin-right: 15px; background-color: blue; padding: 10px; color: white; text-decoration: none; "href = "editProduct.php?id='.$product['product_id'].'"> Edit product </a>
                <a style = "margin-right: 15px; background-color: blue; padding: 10px; color: white; text-decoration: none;" href = "deleteSingleProduct.php?id='.$product['product_id'].'"> Delete product </a>
                    </section>
                ';
        }
        else $access = '';

    


//set time left display
    date_default_timezone_set('Europe/London');
    $time_start = new DateTime('NOW');
    $time_end = new DateTime($product['end_date']);
    $diff=$time_end->diff($time_start);
    $interval ='Time left: ';


    if (($time_end>$time_start) && ($diff->y!=0 || $diff->m!=0 || $diff->d!=0 || $diff->h!=0 || $diff->i!=0 || $diff->s!=0))
        {
            if ($diff->y>1) $interval.= $diff->y.' years '; else if ($diff->y == 1) $interval.= $diff->y.' year ';
            if ($diff->m>1) $interval.= $diff->m.' months '; else   if ($diff->m==1) $interval.= $diff->m.' month ';
            if ($diff->d>1) $interval.= $diff->d.' days '; else  if ($diff->d==1) $interval.= $diff->d.' day ';
            if ($diff->h>1) $interval.= $diff->h.' hours '; else if ($diff->h==1) $interval.= $diff->h.' hours ';
            if ($diff->i>1) $interval.= $diff->i.' minutes '; else if ($diff->i==1) $interval.= $diff->i.' minute ';
            if ($diff->s>1) $interval.= $diff->s.' seconds '; else if ($diff->s==1) $interval.= $diff->s.' second ';

        
        }
// if the time interval is 0, auction has ended
        else $product['ended']=1;
    


// If auction has ended, display the following message
    if ($product['ended'] == 1) 
    {
        $interval = 'This auction has ended. ';
        
        if ($product['bidder_id']) $interval .= 'Won by <a href = "profile.php?id='.$product['bidder_id'].'">'.$product['bidder_f_name'].'</a>'; 
        $stmt1 = $pdo->prepare('UPDATE products SET ended = :ended WHERE product_id = :id;');
        $values =[
            'ended' => 1,
            'id' => $_GET['id']
        ];
        $stmt1->execute($values);
        $ended = $product ['ended']; 
    }

  
// The following code could be used to set up an interval in JS that would allow the page to refresh and thus display the time. Unfortunately, the reviews feature on the same page does not allow us to use this. 
//  echo '<script> setTimeout(function(){
//     window.location.reload(1);
//  }, 1000); </script>';
// Aditionally, use header ("Refresh: 1, url = ") or the meta tag http-equiv 

    $content ='<h1> Product page </h1> <article class="product">
    <img style ="max-width: 45vw;  height: 55vh; max-height: 55vh;  object-fit: cover" src="'.$product['image1'].'" alt="product name">
    <section class="details">
    <h2>'.$product['product_name'].'</h2>
    <h3>'.$product['category'].'</h3>
    <p>Auction created by <a href="profile.php?id='.$product['user_id'].'">'.$product['user_f_name'].'</a> on '.$product['pub_date']. '<a href="#"></a></p>';

    
    
// set up buy it now price   
   $y = $product['buy_it_now_price'];
   if ($ended == 0) 
    {
        if ($product['buy_it_now_price']!=0 && $product['current_bid'] < $product['buy_it_now_price'] && $ended == 0) 
                $content.='<p> <a href="buyNow.php?id='.$x.'&price='.$y.'"> Buy it now for: £'.$product['buy_it_now_price'].'</a></p>';
        $content.='<p class="price">Current bid: £'.$product['current_bid'].'</p>';
    }
  
 //set up time left
    $content.='<time>'.$interval.'</time>';

   

// set up bidding
    $bidMessage='';
    if (isset($_SESSION['userinfo'])) 
    {  
            if (isset($_POST['bid']))
            {
                if ($_SESSION['userinfo']['is_admin']==0)
                {

                if (!empty($_POST['bidVal']))
                {
                    if (filter_var(trim($_POST['bidVal']), FILTER_VALIDATE_FLOAT)) 
                    {
                        if ($product['buy_it_now_price']!=0 && $_POST['bidVal'] >= $product['buy_it_now_price'])   
                               {
                                $stmt = $pdo->prepare('UPDATE products SET ended = 1 WHERE product_id = :id;');
                                
                                $values =[
                                    'id' => $_GET['id']
                                ];
                                if($stmt->execute($values)) header("Refresh:0; url=buyNow.php?id=$x&price=$y");
                                    else
                                     $content= '<p style="color: red;"> Something went wrong. Please try again!</p>';
                               }
                                else 
                                
                                    if ( $_POST['bidVal'] <= $product['current_bid']) $bidMessage = '<p style="color: red;"> Please enter a value higher than the current bid.</p>';
                                        
                                        else if (( $product['buy_it_now_price']==0 && $_POST['bidVal'] > $product['current_bid']) ||
                                             ($product['buy_it_now_price']!=0 && $_POST['bidVal'] > $product['current_bid'] 
                                             && $_POST['bidVal'] <= $product['buy_it_now_price']))   
                                            {        
                                                $stmt = $pdo->prepare('UPDATE products SET current_bid = :bid, bidder_id = :bid_id, bidder_f_name = :bid_name WHERE product_id = :id;');
                                            
                                                    $values =[
                                                        'bid' => filter_var(trim($_POST['bidVal']), FILTER_SANITIZE_NUMBER_FLOAT),
                                                        'bid_id' => $_SESSION['userinfo']['user_id'],
                                                        'bid_name' => $_SESSION['userinfo']['first_name'],
                                                        'id' => $_GET['id']
                                                    ];

                                                    if($stmt->execute($values))  header("Refresh:0; url=productPage.php?id=$x");
                                                        else $content= '<p style="color: red;"> Something went wrong. Please try again!</p>';
                                   }   
                                
                         }
                         else $bidMessage = '<p style="color: red;"> You must enter a valid number!</p>';
                    }
                    else $bidMessage = '<p style="color: red;"> You must enter a sum in order to bid!</p>';
            }
             else $bidMessage = '<p style="color: red;"> You must log in as a regular user in order to bid for auctions! </p>';
       }
    }
    else $bidMessage = '<p style="color: red;"> You must be logged in to bid!</p><p style="margin-top: 15px;"><a href="login.php"> Go to log in page</a></p>';
              

// display bid form    
        if ($ended ==0) $content.= $bidMessage.'
            <form action="productPage.php?id='.$_GET['id'].'" method="POST" class="bid">
                <input  style = "width: 15vw !important; flex-grow: 0;" type="text" name = "bidVal" placeholder="Enter bid amount" />
               <input  style = " margin-left: 0; width: 15vw !important; flex-grow: 0;" type="submit" value="Place bid" name = "bid" />
            </form>';
            
//description        
            $content.='
            </section>
            <section class="description">
            <p>'.$product['product_description'].$access.'
            </section>';          
   }

// set up reviews 
    $reviewMessage='';
    $stmt = $pdo->prepare('SELECT * from reviews where target_user_id = :id;');
    $val = [
        'id' => $product['user_id']
    ];
    $stmt->execute($val);

    $content.='<section class="reviews">
    <h2>Reviews of '.$user.' </h2>';
    
    if ($stmt->rowCount()==0) $content.= '<p style="margin-top: 15px;">No reviews yet... </p>';
    while ($review = $stmt->fetch()) 
    {
       $content.='
            <ul>
                <li><strong><a href="profile.php?id='.$product['user_id'].'">'.$review['user_f_name'].'</a> said </strong> '.$review['content'].' on <em>'.$review['post_date'].'</em></li>

            </ul>';

    }
    if (isset($_POST['submitReview']))
    {
        if (isset($_SESSION['userinfo'])) 
            {
                if (!empty($_POST['reviewtext']))
                {
                    $stmt = $pdo->prepare('INSERT INTO reviews (target_user_id, user_id, user_f_name, post_date, content)
                    VALUES ( :target_user_id, :user_id, :user_f_name, :post_date, :content);');
                    $values = [
                        'target_user_id' => $product['user_id'],
                        'user_id' => $_SESSION['userinfo']['user_id'],
                        'user_f_name' => $_SESSION['userinfo']['first_name'],
                        'post_date' => date('Y-m-d H:i:s',time()),
                        'content' => filter_var(trim($_POST['reviewtext']), FILTER_SANITIZE_STRING) 
                        ];
                    
                    if($stmt->execute($values)) header("Refresh:0; url=productPage.php?id=$x");
                        else $content= '<p> Something went wrong. Please try again!</p>';
                }
                else $reviewMessage= '<p style="color: red;  margin-top: 30px;"> Your review has no body. Please try again!</p>'; 
            }
            else $reviewMessage = '<p style="color: red; margin-top: 30px; "> You must be logged in to leave a review!</p><p style="margin-top: 15px;"><a href="login.php"> Go to log in page</a></p>';
    } 
// reviews form 
    $content.=$reviewMessage.'
            <form action="productPage.php?id='.$_GET['id'].'" method="POST">
                <label>Add your review</label> <textarea name="reviewtext" placeholder = "Leave a review"></textarea>
                <input type="submit" value="Add Review" name="submitReview" />
            </form> 
            </section>
            </article>';   


include '../layouts/layout.php';

?>