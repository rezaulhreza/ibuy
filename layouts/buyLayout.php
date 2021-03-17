<!DOCTYPE html>
<html>
	<head>
		<title>ibuy Auctions</title>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Ensures optimal rendering on mobile devices. -->
  		<meta http-equiv="X-UA-Compatible" content="IE=edge" /> <!-- Optimal Internet Explorer compatibility -->
		<link rel="stylesheet" href="css/ibuy.css" />
	</head>
	<body>

    <script src="https://www.paypal.com/sdk/js?client-id=AX0K2Uadzq2u75RCEcrDMoUuxeagiqASCjJ-xjZIXBCU7JRdJP2HbBDS4Sq7r93lQq4L6MdsFW6jAaPm"></script>
  
    <script>
// Code from https://developer.paypal.com/docs/checkout/
    paypal.Buttons({
        createOrder: function(data, actions) {
        return actions.order.create({
            purchase_units: [{
            amount: {
                value: '0.01'
            }
            }]
        });
        },
        onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
            alert('You have successfully purchased this item! Transaction completed by ' + details.payer.name.given_name);
            // Call your server to save the transaction
            return fetch('/paypal-transaction-complete', {
            method: 'post',
            headers: {
                'content-type': 'application/json'
            },
            body: JSON.stringify({
                orderID: data.orderID
            })
            });
        });
        }
    }).render('#pay');
    </script>

	<!-- Set up log in/out/register/home bar -->
	<div class="login" style="height: 2vh;">
	<?php	
	// display user name
	if (isset($_SESSION['userinfo']['first_name'])) echo '<span style="margin-right: 15px !important;" > Logged in as '.  $_SESSION['userinfo']['first_name'].'</span>';
	?>
	<a href="index.php"> Home </a>
	<?php
	//if user is logged in, generate admin and user menu, pending and profile, respectively.
	
	if (isset($_SESSION['loggedin']))
	{
		if (isset($_SESSION['userinfo']['is_admin'])) 
		{
			if ($_SESSION['userinfo']['is_admin'] == 1) echo '<a href="pending.php"> Pending</a>';
				else echo '<a href="profile.php?id='.$_SESSION['userinfo']['user_id'].'"> Profile </a>';
		}
// if user is logged in, print log out link
// if user is logged out, print log in and register links 
			
		echo '<a style="margin-right: 5px !important;"  href ="logout.php">Log out</a>';
	}
	else echo '<a style="margin-right: 15px !important;"  href="login.php">Log in</a> <a style="margin-right: 15px !important;" href ="register.php">Register</a>';
			
			
	?> 	
	</div>

		<header style="height: 12vh;">
		<h1><span class="i">i</span><span class="b">b</span><span class="u">u</span><span class="y">y</span></h1>
		<form action="search.php" method="POST">
				<input type="text" name="search" placeholder="Search for anything" />
				<input type="submit" name="submitSearch" value="Search" />
			</form>
		</header>

		<nav style="height: 6vh;">
		<?php
//generate categories menu
 include 'connectPDO.php';
 try {
 $stmt = $pdo->prepare('SELECT category_id, category_name from categories;');
 $stmt->execute();
 echo '<ul>';
 foreach ($stmt as $row)
 echo '<li><a href="category.php?category='.$row['category_id'].'">'.$row['category_name'].'</a></li>'; 
 echo '</ul>';    
 }
 catch (Exception $e) {
echo 'Caught exception: ',  $e->getMessage(), "\n";
}

	?>
		</nav>
	
		<img src="images/randombanner.php" alt="Banner" style="height: 30vh; object-fit: cover;"/>
	<?php
// generate admin and user menus 
		if (isset($_SESSION['userinfo']['is_admin']))
		{ 
			if ($_SESSION['userinfo']['is_admin'] == 1) 
				echo 
				'<nav style="height: 6vh;">
				<ul>
					<li><a href="addCategory.php">Add Category</a></li>
					<li><a href="editCategory.php">Edit Category</a></li>
					<li><a href="deleteCategory.php">Remove Category</a></li>
					<li><a href="addAdmin.php">Add Admin</a></li>
					<li><a href="editAdmin.php">Edit Admin</a></li>
					<li><a href="deleteAdmin.php">Remove Admin</a></li>
				</ul>
				</nav>';
			
			else echo '
				<nav style="height: 6vh;">
					<ul>
						<li><a href="addProduct.php">Add product</a></li>
						<li><a href="editProductSelect.php">Edit product</a></li>
						<li><a href="deleteProductSelect.php">Remove product</a></li>
					</ul>
				</nav>';
		}

 	?>
    <main style=" min-height: 20vh; margin-top: 5vh; ">

	
<?php
echo $content;
?>
			
		</main>
	</body>
	<footer style="padding: 0.55em; display: flex; justify-content: center; align-items: center; height: 1.5vh;">
			<small>	&copy; ibuy 2019</small>
			</footer>
</html>
