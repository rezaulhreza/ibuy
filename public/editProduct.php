<?php
session_start();

    include 'connectPDO.php';  
    date_default_timezone_set('Europe/London');
    $content = '';
    // a boolean that tests for existing problems
    $problem = 0;

//check if user is logged in
    if (isset($_SESSION['userinfo']))
    {
        // if form is submitted
        if (isset($_POST['update']))
        {
            // if the user has chosen to edit name
            if (!empty($_POST['product_name']))
            {
            $stmt = $pdo->prepare('UPDATE products SET product_name = :product_name WHERE product_id = :id;');
                $values =[
                    'product_name' => filter_var(trim($_POST['product_name']), FILTER_SANITIZE_STRING ),
                    'id' => $_GET['id']
                ];
                if(!$stmt->execute($values)) $problem=6; 
            }
           
            // if the user has chosen to edit start date
            if (!empty($_POST['start_date']))
            {
            $stmt = $pdo->prepare('UPDATE products SET start_date = :start_date WHERE product_id = :id;');
                $values =[
                    'start_date'  => $_POST['start_date'],
                    'id' => $_GET['id']
                ];
                if(!$stmt->execute($values)) $problem=6; 
            }

            // if the user has chosen to edit end date
            if (!empty($_POST['end_date']))
            {
            $stmt = $pdo->prepare('UPDATE products SET end_date = :end_date WHERE product_id = :id;');
                $values =[
                    'end_date'  => $_POST['end_date'],
                    'id' => $_GET['id']
                ];
                if(!$stmt->execute($values)) $problem=6; 
            }


             // if the user has chosen to edit description
            if (!empty($_POST['product_description']))
            {
            $stmt = $pdo->prepare('UPDATE products SET product_description = :product_description WHERE product_id = :id;');
                $values =[
                     'product_description'  => filter_var(trim($_POST['product_description']), FILTER_SANITIZE_STRING),
                    'id' => $_GET['id']
                ];
                if(!$stmt->execute($values)) $problem=6; 
            }

            // if the user has chosen to edit price
            if (!empty($_POST['buy_it_now_price']))
            {
            $stmt = $pdo->prepare('UPDATE products SET buy_it_now_price = :buy_it_now_price WHERE product_id = :id;');
                $values =[
                    'buy_it_now_price'  => filter_var(trim($_POST['buy_it_now_price']), FILTER_SANITIZE_NUMBER_FLOAT),
                    'id' => $_GET['id']
                ];

                if(!$stmt->execute($values)) $problem=6; 
            }
    
                // Check if a new image file has been uploaded
                if(!empty($_FILES['img']['tmp_name']) && !empty($_FILES['img']['name']) && !empty($_FILES['img']['size'])) {
                {

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

                    if ($problem==0) {
                        $imgPath = 'uploads/'.basename( $_FILES['img']['name']);
                    
                        $stmt = $pdo->prepare('UPDATE products SET image1 = :img WHERE product_id = :id;');
                        $values =[
                            'img' => $imgPath,
                            'id' => $_GET['id']
                        ];
                        if(!$stmt->execute($values)) $problem=6; 
                    }
                }
       


            //update entry
                $stmt = $pdo->prepare('UPDATE products SET category = :category, pub_date = :pub_date WHERE product_id = :id;');
                $values =[
                    'category'  => $_POST['select'],
                    'pub_date'  => date('Y-m-d H:i:s',time()),
                    'id' => $_GET['id']
                ];
                if(!$stmt->execute($values))  
                    $problem=6; 
                }
                
            //test bolean for errors
            if ($problem == 0) $content = '<p>You have successfully updated the product!</p>';
                else if ($problem == 1) $content='<p> The file you\'re trying to upload is not an image. Please try again!</p>';
                    else if ($problem == 2) $content='<p> The file already exists! Please upload a different file!</p>';
                        else if ($problem == 3) $content='<p>The file you\'re trying to upload is too large! Please try again!</p>';
                            else if ($problem == 4) $content='<p>Please upload a file with .jpeg, .jpg or .png extension! </p>';
                                else if ($problem == 5) $content='<p> Your file could not be uploaded. Please try again!</p>';
                                    else if ($problem == 6) $content='<p>Something went wrong. Please try again!</p>';
        }
        //display form
        else 
        {

            $prod = $pdo->prepare('SELECT * from products WHERE product_id = :id;');
            $values =[
                'id' => $_GET['id']
            ];
            $prod->execute($values);
            $product = $prod->fetch();


        $content = '
                <form action="editProduct.php?id='.$_GET['id'].'" enctype = "multipart/form-data" method="POST">
                <label for="product_name"> Product Name </label>
                <input type="text" name = "product_name" value="'.$product['product_name'].'"/>
                <label for="select"> Category </label>
                <select style ="flex-grow: 1; width: 20vw;  margin-bottom: 1em; margin-right: 2vw;  margin-left: 2vw;" name="select">';
                
                //generate categories select drop down
                $stmt = $pdo->prepare('SELECT category_name from categories;');
                $stmt->execute();
                while ($cat = $stmt->fetch()) {
                    if ($cat['category_name']==$product['category']) $content.= '<option value="' . $cat['category_name'] . '" selected>'.$cat['category_name'].'</option>';
                            else  $content.= '<option value="' . $cat['category_name'] . '">'.$cat['category_name'].'</option>';
                }
                $content.='</select>
                <label for="start_date"> Bidding start date </label>
                <input type="text" name = "start_date" value = "'.$product['start_date'].'"/>
                <label for="end_date"> Bidding end date </label>
                <input type="text" name = "end_date" value = "'.$product['end_date'].'"/>
                <label for="buy_it_now_price"> Buy it now for </label>
                <input type="text" name = "buy_it_now_price" value="'.$product['buy_it_now_price'].'"/>
                <label for="product_description"> Description </label>
                <textarea name = "product_description" placeholder="'.$product['product_description'].'"></textarea>
                <label for="image"> Upload image </label>
                <input type="hidden" name="MAX-FILE-SIZE" value = "50000">
                <input type="file" name = "img" accept="image/png, image/jpeg, image/jpg" multiple/>
                <input type="submit" value="Update" name="update"/>
                </form>';
        }

    }

    else $content = '<p style ="color: red;"> You must be logged in to edit an auction!</p><p style="margin-top: 15px;"><a href="login.php"> Go to log in page</a></p>';

include '../layouts/layout.php';
?>
