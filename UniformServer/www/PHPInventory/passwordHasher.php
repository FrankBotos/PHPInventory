<?php


$password = "user";
$userinput = "password";
$hashedPass = crypt($password);

echo $hashedPass . "<br />";
echo $password . "<br />";

if (hash_equals($hashedPass, crypt($userinput, $hashedPass))){//this is how you compare user input to hashed password, if salt was generated automatically!
	echo "Password verified!";
} else {
	echo "Wrong password!";
}


?>
