<?php

session_start();
include('library.php');


if (isset($_SESSION['username'])){
	header('Location: view.php');
} else {

$matched = false;
$errMessage = "";
$emailSent = "";
$accountCreated = "";

if ($_POST){//validating input data

	$link = connectDB();//connection to database via external function

	//query to retreive all records in database
	$sql_query = "select * from users";
	//run sql query, save result in variable
	$sqlResult = mysqli_query($link, $sql_query)
	or die ('Oops, query failed!' . mysqli_error($link));

	while($row = mysqli_fetch_assoc($sqlResult)){//cycling through database
		//THIS VERSION IS USED TO CHECK FOR HASHED PASSWORDS
		if (($row['username'] == $_POST['username']) && hash_equals($row['password'], crypt($_POST['password'], $row['password']))){//if username and password both match
			$matched = true;
			$_SESSION['role'] = $row['role'];//saving role into session variable while still in scope
		}

	}

	if (!$matched){//if not matched after cycling database, update error message
		$errMessage = "<font color='red'>Invalid login credentials!</font>";
	}

}

if ($_POST && isset($_GET['forgot'])){

//connect to mysql server, retrieve link identifier OR display error
$link = connectDB();

//query to retreive all records in database
$sql_query = "select * from users";
//run sql query, save result in variable
$sqlResult = mysqli_query($link, $sql_query)
or die ('Oops, query failed!' . mysqli_error($link));

$found = false;//flag to determine whether "email has been sent" message is displayed

	while($row = mysqli_fetch_assoc($sqlResult)){//cycling through database
		//THIS VERSION IS USED TO CHECK FOR HASHED PASSWORDS
		if (($row['username'] == $_POST['email']) ){//if username and email match

			//if a match is found in the database, an appropriate email is sent to the user!
			$emailAddress = $_POST['email'];
			$subject = "Password hint for user: " . $emailAddress;
			$hint = $row['passwordHint'];
			mail($emailAddress,$subject,"Your password hint is: " . $hint);
			$emailSent = "If the user exists, an email has been sent with instructions to retrieve password!<br />Return to <a href='login.php'>login</a>?";
			$found = true;
		}

	}

	if (!$found){//if no match was found after cycling through entire database
		$emailSent = "If the user exists, an email has been sent with instructions to retrieve password!<br />Return to <a href='login.php'>login</a>?";
	}
}

if ($_POST && $matched) {//if match found, redirect and process
	session_start();
	$uname = $_POST['username'];
	$_SESSION['username'] = $uname;
	header('Location: view.php');
}
else {//else repopulate form
	useCss();
	?>

	<?php

	if (!isset($_GET['forgot'])){//if user has not clicked forgot password link, execute regular login
	?>
	<form method="post" action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> >
	Username: <input type="text" name="username"><br /><br />
	Password: <input type="password" name="password"><br /><br />
	<?php echo $errMessage; ?><br /><br />
	<input type="submit" value="login">
	</form>

	<a href="login.php?forgot=1">Forgot your password?</a>

	<?php
} else {//otherwise, initiate email form
	?>
		<form method="post">
		Email address: <input type="text" name="email"><br /><br />
		<?php echo $emailSent ?> <br />
		<input type="submit" value="Retrieve Password">
		</form>
	<?php
}

}
}
?>
