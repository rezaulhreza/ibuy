<?php

session_start();

    include 'connectPDO.php';
    $content = '';
    //check if user is logged in
    if (isset($_SESSION['userinfo']))
    {
    // if select form is submitted
        if (isset($_POST['selectCategory']))
        {
          $x= $_POST['select'];

          $stmt = $pdo->prepare('SELECT * from categories WHERE category_id = :id;');
          $val = [
          'id' => $x
          ];
          $stmt->execute($val);
          $selectedCategory = $stmt ->fetch();
            // display edit form
          $content ='
              <form action="editSelectedCategory.php?id='.$x.'" method="POST">
              <label for="new_category_name"> Edit Category Name </label>
              <input type="text" name = "new_category_name" value="'.$selectedCategory['category_name'].'"/>
              <label for="new_category_description"> Description </label>
              <textarea name = "new_category_description" placeholder="'.$selectedCategory['description'].'"></textarea>
              <input type="submit" value="Edit" name="editCategory"/>
              </form>';
        }
        // display select form
        else {
            $content ='
            <form action="editCategory.php" method="POST">
            <label for="select"> Select category </label>
            <select style ="flex-grow: 1; width: 20vw;  margin-bottom: 1em; margin-right: 2vw;  margin-left: 2vw;" name="select">';
            // generate select drop down
                $stmt = $pdo->prepare('SELECT category_id, category_name from categories;');
                $stmt->execute();
                while ($cat = $stmt->fetch()) 
                {
                    $content.= '<option value="' . $cat['category_id'] . '">'.$cat['category_name'].'</option>';
                }
                $content.='
                </select>
               
                <input type="submit" value="Select" name="selectCategory"/>
                </form>';
            
             }
}
    else $content = '<p style ="color: red;"> You must be logged in to edit a category!</p><p style="margin-top: 15px;"><a href="login.php"> Go to log in page</a></p>';

include '../layouts/layout.php';
?>
