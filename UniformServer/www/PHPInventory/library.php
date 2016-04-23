<?php

session_start();
function createMenu(){//creates a menu without a search bar
	?>
	<h1 class="mainTitle">Frank's PC Parts Repository</h1>
	<h3><a href="add.php" class="menu">Add</a> --- <a href="view.php" class="menu">View All</a> --- <a href="logout.php" class="menu">Logout</a>
	<br />
	<br />

	<span class="userinfo">
	User: <?php echo $_SESSION['username'] ?>, Role: <?php echo ("<span class=\"role\">" . $_SESSION['role']) . "</span>"; ?>
	</span>

	</h3>
	<?php
}

function createMenu2(){//creates a menu with a search bar
	?>
	<h1 class="mainTitle">Frank's PC Parts Repository</h1>
	<h3><a href="add.php" class="menu">Add</a> --- <a href="view.php" class="menu">View All</a> --- <a href="logout.php" class="menu">Logout</a>
		 <form method="post">Search in description: <input type="text" name="search" value="<?php if (isset($_POST['search'])) echo $_POST['search']; ?>"> <input type="submit" value="Search">
	 </form>

	 <span class="userinfo">
	 User: <?php echo $_SESSION['username'] ?>, Role: <?php echo ("<span class=\"role\">" . $_SESSION['role']) . "</span>"; ?>
 	 </span>

 </h3>
	<?php
}

function createFooter(){//creates footer
	?>
	<br />
	<table class="footerWrapper">
	<tr><td class="foot"><footer>Copyright © Frank Botos, 2015</footer></td></tr>
	</table>
	<?php
}

function useCss(){//enables css use
	?>

	<head>
		<link rel="stylesheet" type="text/css" href="sitestyle.css">
    </head>

	<?php
}

function connectDB(){//establishes a connection to the database
	$dblog = file('secret/topsecret.txt');//retrieve database login info

	//save login info in variables
	$localhost = trim($dblog[0]);
	$user = trim($dblog[1]);
	$pass = trim($dblog[2]);
	$db_name = trim($dblog[3]);

	//connect to mysql server, retrieve link identifier OR display error
	$link = mysqli_connect($localhost, $user, $pass, $db_name)
	or die('Could not connect: ' . mysqli_error($link));

	return $link;
}

?>
