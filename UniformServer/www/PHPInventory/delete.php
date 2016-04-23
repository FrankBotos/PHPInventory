<?php

session_start();
include("library.php");

if (!isset($_SESSION['username'])){
	header('Location: login.php');
} else {

$link = connectDB();//connection to database via external function

//query to retreive all records in database
$sql_query = "select * from inventory";
//run sql query, save result in variable
$sqlResult = mysqli_query($link, $sql_query)
or die ('Oops, query failed!' . mysqli_error($link));

while($row = mysqli_fetch_assoc($sqlResult)){//cycle through table
	if ($row['id'] == $_GET['id']){//if the row id matches the id passed to program, execute statement
		$sql_query = "";

		//check if item is already deleted, update sql_query accordingly
		if ($row['deleted'] == 'y'){
			$sql_query = "UPDATE `inventory` SET `deleted` = 'n' WHERE `inventory`.`id` =" . $_GET['id'] . ";";
		} else {
			$sql_query = "UPDATE `inventory` SET `deleted` = 'y' WHERE `inventory`.`id` =" . $_GET['id'] . ";";
		}

		//for debugging sql query
		//echo $sql_query;

		//run mysql query, and update database
		mysqli_query($link, $sql_query)
      	or die ('Oops, query failed!' . mysqli_error($link));
	}
}
/************End Datbase Update***********************/

//once database is updated, redirect to view page
header('Location: view.php');
}

?>
