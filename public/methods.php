<?php 

function registerUser($is_admin, $form){
    include 'connectPDO.php';
    $content = '';
    $problem = false;

// any user can register
        if (isset($_POST['register'])) {

//fields must not be empty
        if (!empty($_POST['first_name']) && !empty($_POST['surname']) && !empty($_POST['email_address']) && !empty($_POST['pass']) && !empty($_POST['pass_check']))
        {
            $stmt = $pdo->prepare ('SELECT user_id FROM users WHERE email_address = :email ;');
            $values = [
                'email' => $_POST['email_address']
            ];
            $stmt -> execute($values);
            if ($stmt->rowCount() > 0) $content .= '<p style ="margin-left: 13px; margin-bottom: 30px; margin-top: 50px;">This user already exists! Please try again!</p>';
                else 
                {
                    $stmt1 = $pdo->prepare('INSERT INTO users (first_name, surname, email_address, password, is_admin)
                    VALUES (:first_name, :surname, :email_address, :pass, :is_admin)');
//email addresses are stored in lowercase
//strip tags or filter_var(input,FILTER_SANITIZE_STRIPPED )
//passwords are not sanitized as they are being hashed 
// password checks are also done front-end via pass type in form input - some characters are not allowed
                    $val = [
                        'first_name'=>filter_var(trim($_POST['first_name']), FILTER_SANITIZE_STRING),
                        'surname' => filter_var(trim($_POST['surname']),FILTER_SANITIZE_STRING),
                        'email_address' => filter_var(strtolower(trim($_POST['email_address'])), FILTER_SANITIZE_EMAIL),
                        'pass' => password_hash(trim($_POST['pass']), PASSWORD_DEFAULT),
                        'is_admin' => $is_admin
                    ];
//validate all input
                    while ($problem == false)
                    {
                        if (trim($_POST['pass']) != trim($_POST['pass_check'])) 
                        {
                                $content = '<p style="margin-bottom: 30px; margin-left: 10px;">Passwords do not match! Please try again!</p>';
                                $problem = true;
                            }
// Alternatively, one could manually check if substr_count($_POST['email_address'], '@')==1 and same for other characters. The filter_var checks for both @ and .
                        if (!ctype_alpha($_POST['first_name'])) 
                        { 
                            $content = '<p style="margin-bottom: 30px; margin-left: 10px;"> Please enter a valid first name! </p>';
                            $problem = true;
                            }

                        if (!ctype_alpha($_POST['surname'])) 
                        {
                                $content = '<p style="margin-bottom: 30px; margin-left: 10px;"> Please enter a valid surname! </p>';
                                $problem = true;
                                }        
                        if (!filter_var(strtolower(trim($_POST['email_address'])), FILTER_VALIDATE_EMAIL)) 
                        {
                                $content = '<p style="margin-bottom: 30px; margin-left: 10px;"> Please enter a valid email address! </p>';
                                $problem = true;
                                }

                        if (!filter_var(trim($val['first_name']),FILTER_SANITIZE_STRING) && filter_var(trim($val['surname']), FILTER_SANITIZE_STRING)) 
                        {
                                $content = '<p style="margin-bottom: 30px; margin-left: 10px;"> Please enter valid names! </p>';
                                $problem = true;
                                } 
                        if ($problem ==false) break;
                    }
                    if ($problem ==false) if ($stmt1->execute($val))  $content.= '<p>Congratualations! You have been successfully registered!</p>';
                        else if ($problem == true) $content= '<p style="margin-bottom: 30px; margin-left: 10px;">Something went wrong. Please try again!</p>' ;
                }
        }
         else {
                $content = '<p style ="color: red;">  Please fill in all fields to complete registration!</p>';
                }
        }
        else 
            {
                $content .= $form;
            }
// Return message and print form
    return $content;
}



function printCategory($category){

    include 'connectPDO.php';
    date_default_timezone_set("Europe/London");
    $content = '';
//Prepared statement selects all products under a category
    $stmt = $pdo->prepare('SELECT * from products WHERE category = :c AND status = 1 AND :now >= start_date  ORDER BY pub_date DESC;');
    $val = [
        'c' => $category,
        'now' => date('Y-m-d H:i:s',time()),
    ];
    $stmt->execute($val);
    if ($stmt -> rowCount() !=0)
    {
        $content.='<ul class="productList">';

// print out all product entries
        foreach ($stmt as $products)
        {
            $content.= '
            <li>
            <img src="'.$products['image1'].'" alt="product name">
            <article>
                <h2>'.$products['product_name'].'</h2>
                <p>'.$products['product_description'].'</p>
                <p class="price">Current bid: £'.$products['current_bid'].'</p>
                <a href="productPage.php?id='.$products['product_id'].'" class="more">More &gt;&gt;</a>
            </article>
            </li>';
        }

        $content.='</ul>';
    }
    else $content = '<p style = " margin-top: 20px; height: 20vh;"> There are no current auctions in this category. Try again later!';
    return $content;

}




function print10listings() {
    include 'connectPDO.php';
    $content = '';

//Select all products that were added, in descending order, provided they are approved (status =1)

    $stmt = $pdo->prepare('SELECT * from products WHERE status = 1 AND  :now >= start_date  ORDER BY pub_date DESC;');
    $val = [
        'now' => date('Y-m-d H:i:s',time()),
        ];
    $stmt->execute($val);
   
    $content.='<h1>Latest Listings</h1>';
    if ($stmt->rowCount() ==0) $content.= 'There are no auctions to display at the minute.';
    else {
        $counter=1;
        $content.= '<ul class="productList">';
//print the first 10 of the selected auctions; alternatively, a LIMIT 10 could have been added the query.
        while ($products = $stmt->fetch()) 
            if ($counter<=10)
            {
                $counter++;
                $content.= '
                <li>
                <img src="'.$products['image1'].'" alt="product name">
                <article>
                    <h2>'.$products['product_name'].'</h2>
                    <h3>'.$products['category'].'</h3>
                    <p>'.$products['product_description'].'</p>
                    <p class="price">Current bid: £'.$products['current_bid'].'</p>
                    <a href="productPage.php?id='.$products['product_id'].'" class="more">More &gt;&gt;</a>
                </article>
                </li>';
            }
        $content.='</ul>';
    }
        return $content;
}

?>