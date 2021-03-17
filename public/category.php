<?php
session_start();

    include 'methods.php';
    include 'connectPDO.php';

    // this will select the category name based on the id passed via GET and then call a print method that will display
    //all auctions in that category
    $content ='';

    $stmt = $pdo->prepare('SELECT category_name, description from categories WHERE category_id = :id ;');
    $val = [
    'id' => $_GET['category']
    ];
    $stmt->execute($val);
    if ($stmt -> rowCount() == 1)
            {
                $row = $stmt ->fetch();
                $content.='<h1 style="margin-bottom:30px; margin-top:0px;">'.$row['category_name'].'</h1>';
                $content .= '<section class="description" style="margin-bottom: 50px;">
                <h2  style="margin-bottom: 10px;"> Description</h2>
                <p style="margin-top: 5px;">'.$row['description'].'</p>
                </section>';
                $content .= printCategory($row['category_name']);
            }
    else  $content = "<p> Something went wrong. Please try again! </p>";

include '../layouts/layout.php';
?>