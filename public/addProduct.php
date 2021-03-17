<?php
session_start();


    include 'connectPDO.php';  
    date_default_timezone_set('Europe/London');
    $content = '';
    $problem = 0;
    //check if user is logged in
    if (isset($_SESSION['userinfo']))
    {
        // if form has been submitted
        if (isset($_POST['addProduct']))
        {
            //no fields must be left empty
                if (!empty($_POST['product_name']) && !empty($_POST['start_date']) && !empty($_POST['end_date']) && !empty($_POST['product_description']))
            {

                // Check if file has been uploaded
                if(!empty($_FILES['img']['tmp_name']) && !empty($_FILES['img']['name']) && !empty($_FILES['img']['size'])) {

                    $target_dir = 'uploads/';
                    $target_file = $target_dir . basename($_FILES['img']['name']);
                    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                    // Check if image file is an image
                        $isImage = getimagesize($_FILES['img']['tmp_name']);
                        if($isImage == false) $problem=1; //file is not an image
                    
                    // Check if file already exists
                    if (file_exists($target_file)) $problem=2; //file already exists

                    // Check file size
                    if ($_FILES['img']['size'] > 500000) $problem=3;// file is too large
                
                    // Only allow certain file formats
                    if($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg') $problem=4;

                    // upload errror
                    if (!move_uploaded_file($_FILES['img']['tmp_name'], $target_file)) $problem=5;

                    if ($problem==0) $imgPath = 'uploads/'.basename( $_FILES['img']['name']);
                }
                else  $imgPath = 'images/product.png';


                if ($problem==0){

                    $stmt = $pdo->prepare('INSERT INTO products (product_name, category, user_id, user_f_name, pub_date, start_date, end_date, product_description, buy_it_now_price, image1)
                    VALUES (:product_name, :category, :user_id, :user_f_name, :pub_date, :start_date, :end_date, :product_description, :buy_it_now_price, :img)');

                    $values =[
                        'product_name' => filter_var(trim($_POST['product_name']),FILTER_SANITIZE_STRING) ,
                        'category'  => $_POST['select'],
                        'user_id'  => $_SESSION['userinfo']['user_id'],
                        'user_f_name' => $_SESSION['userinfo']['first_name'],
                        'pub_date'  => date('Y-m-d H:i:s',time()),
                        'start_date'  => $_POST['start_date'],
                        'end_date'  => $_POST['end_date'],
                        'product_description'  => filter_var(trim($_POST['product_description']),FILTER_SANITIZE_STRING),
                        'buy_it_now_price'  => filter_var(trim($_POST['buy_it_now_price']), FILTER_SANITIZE_NUMBER_FLOAT),
                        'img' => $imgPath
                    ];
                    if (!$stmt->execute($values)) $problem=6;
                }
              
                if ($problem==0)  $content = '<p>Your auction is now pending approval and will be published soon...</p>';
                    else if ($problem == 1) $content='<p> The file you\'re trying to upload is not an image. Please try again!</p>';
                        else if ($problem == 2) $content='<p> The file already exists! Please upload a different file!</p>';
                            else if ($problem == 3) $content='<p>The file you\'re trying to upload is too large! Please try again!</p>';
                                else if ($problem == 4) $content='<p>Please upload a file with .jpeg, .jpg or .png extension! </p>';
                                    else if ($problem == 5) $content='<p> Your file could not be uploaded. Please try again!</p>';
                                        else if ($problem == 6) $content='<p>Something went wrong. Please try again!</p>';
            }
            else  $content = '<p>Please fill in all fields!</p>';
        }
        // if the form isn't being processed, display form.
            else 
                {
                $content = '
                        <form action="addProduct.php" enctype = "multipart/form-data" method="POST">
                        <label for="product_name"> Product Name </label>
                        <input type="text" name = "product_name" placeholder="Name"/>
                        <label for="select"> Category </label>
                        <select style ="flex-grow: 1; width: 20vw;  margin-bottom: 1em; margin-right: 2vw;  margin-left: 2vw;" name="select">';

                        $stmt = $pdo->prepare('SELECT category_name from categories;');
                        $c=$stmt->execute();
                        
                    // fetch values to produce the select drop down
                        while ($cat = $stmt->fetch()) {
                        $content.= '<option value="' . $cat['category_name'] . '">'.$cat['category_name'].'</option>';
                        }
                        $content.='</select>
                        <label for="start_date"> Bidding start date </label>
                        <input type="text" name = "start_date" placeholder = "YYYY-MM-DD hh:mm:ss"/>
                        <label for="end_date"> Bidding end date </label>
                        <input type="text" name = "end_date" placeholder = "YYYY-MM-DD hh:mm:ss"/>
                        <label for="buy_it_now_price"> Buy it now for </label>
                        <input type="text" name = "buy_it_now_price" placeholder="Please enter the value in £"/>
                        <label for="product_description"> Description </label>
                        <textarea name = "product_description" placeholder="Enter relevant details for your product."></textarea>
                        <label for="image"> Upload image </label>
                        <input type="hidden" name="MAX-FILE-SIZE" value = "500000">
                        <input type="file" name= "img" accept="image/png, image/jpeg, image/jpg" multiple/>
                        <input type="submit" value="Add" name="addProduct"/>
                        </form>';
                }
    }
    else  $content = '<p style ="color: red;"> You must be logged in to add auctions!</p><p style="margin-top: 15px;"><a href="login.php"> Go to log in page</a></p>';

include '../layouts/layout.php';
?>
