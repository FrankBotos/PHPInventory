<?php

session_start();
include('library.php');

if (!isset($_SESSION['username'])){
	header('Location: login.php');
} else {

//protecting search term from xss attacks by ensuring all html special characters are treated as normal characters by browser
if (isset($_POST['search'])) {
	$_POST['search'] = htmlspecialchars($_POST['search']);
}

//protecting against sql injections by limiting search to only characters and numbers!
if (!preg_match('/^[a-zA-Z0-9]+$/', $_POST['search'])){
	$_POST['search'] = NULL;
}

//setting up post and session variables so that if user does an empty search all items are displayed
if ($_POST['search']){//if user entered characters in search bar
	$_SESSION['search'] = $_POST['search'];
} else if ($_POST['search'] == NULL && $_GET['sort'] == NULL){//if user did not enter characters in search bar
	unset($_SESSION['search']);
}

$link = connectDB();//connection to database via external function

//retreive all records in database
$sql_query = "select * from inventory";


//IF A SEARCH TERM IS DEFINED, ADD IT TO QUERY SO ONLY SEARCHED ITEMS ARE DISPLAYED!
//in case user figures out a way to bypass regular expression, mysqli_real_escape_string function is used to protect against SQL injection attacks!
//echo $_GET['search']; //for search term debugging purposes
if (isset($_SESSION['search'])){
	$sql_query = $sql_query . " where description like '%" . mysqli_real_escape_string($link,$_SESSION['search']) . "%'";
}

//appending sort values
//here, the columnName is typed manually so that a particularly savy user may not alter the GET value url and alter the table through SQL injection
if (isset($_GET['sort'])){
	if ($_GET['sort'] == 'id'){
		$sql_query = $sql_query . " order by id";
		setcookie('lastSorted', 'id', time() + 60 * 60 * 24 * 30);
	} else if ($_GET['sort'] == 'name'){
		$sql_query = $sql_query . " order by itemName";
		setcookie('lastSorted', 'name', time() + 60 * 60 * 24 * 30);
	} else if ($_GET['sort'] == 'description'){
		$sql_query = $sql_query . " order by description";
		setcookie('lastSorted', 'description', time() + 60 * 60 * 24 * 30);
	} else if ($_GET['sort'] == 'supplier'){
		$sql_query = $sql_query . " order by supplierCode";
		setcookie('lastSorted', 'supplier', time() + 60 * 60 * 24 * 30);
	} else if ($_GET['sort'] == 'cost'){
		$sql_query = $sql_query . " order by cost";
		setcookie('lastSorted', 'cost', time() + 60 * 60 * 24 * 30);
	} else if ($_GET['sort'] == 'price'){
		$sql_query = $sql_query . " order by price";
		setcookie('lastSorted', 'price', time() + 60 * 60 * 24 * 30);
	} else if ($_GET['sort'] == 'num'){
		$sql_query = $sql_query . " order by onHand";
		setcookie('lastSorted', 'num', time() + 60 * 60 * 24 * 30);
	} else if ($_GET['sort'] == 'reorder'){
		$sql_query = $sql_query . " order by reorderPoint";
		setcookie('lastSorted', 'reorder', time() + 60 * 60 * 24 * 30);
	} else if ($_GET['sort'] == 'backorder'){
		$sql_query = $sql_query . " order by backOrder";
		setcookie('lastSorted', 'backorder', time() + 60 * 60 * 24 * 30);
	} else if ($_GET['sort'] == 'del'){
		$sql_query = $sql_query . " order by deleted";
		setcookie('lastSorted', 'del', time() + 60 * 60 * 24 * 30);
	}
} else {
	if (isset($_COOKIE['lastSorted'])){
		header('Location: view.php' . '?sort=' . $_COOKIE['lastSorted']);
	} else {
		$sql_query = $sql_query . " order by id";//default order by id if no sort preference is set
	}
}


//run sql query, save result in variable
$sqlResult = mysqli_query($link, $sql_query)
or die ('Oops, query failed!' . mysqli_error($link));
?>


<!-- PRINTING DATABASE -->
<head><title>View Database</title></head>
<?php
	useCss();
	createMenu2();

	//ensuring that table is created only if search returned results and there are rows to output!
	if (mysqli_num_rows($sqlResult) > 0){
?>

<table border = "1">
 <tr>
 	<th><a href="view.php?sort=id" class="sort">ID</th>
 	<th><a href="view.php?sort=name" class="sort">Item Name</th>
 	<th><a href="view.php?sort=description" class="sort">Description</th>
 	<th><a href="view.php?sort=supplier" class="sort">Supplier</th>
 	<th><a href="view.php?sort=cost" class="sort">Cost</th>
 	<th><a href="view.php?sort=price" class="sort">Price</th>
 	<th><a href="view.php?sort=num" class="sort">Number on hand</th>
 	<th><a href="view.php?sort=reorder" class="sort">Reorder Level</th>
 	<th><a href="view.php?sort=backorder" class="sort">On Back Order?</th>
 	<th><a href="view.php?sort=del" class="sort">Delete/Restore</th>
 </tr>
 <?php
 	while($row = mysqli_fetch_assoc($sqlResult)){//creating row variable, which stores data as associative array
 ?>
 		<tr><!-- must use database column names when printing! -->
 			<td class="menu"><a href="add.php?getID=<?php print $row['id']; ?>"><?php print $row['id'] ?></td><!-- PRINTING ID AS A LINK, SENDING ID TO ADD.PHP PAGE VIA "A" TAG -->
 			<td class="menu"><?php print $row['itemName'] ?></td>
 			<td class="menu"><?php print $row['description'] ?></td>
 			<td class="menu"><?php print $row['supplierCode'] ?></td>
 			<td class="menu"><?php print $row['cost'] ?></td>
 			<td class="menu"><?php print $row['price'] ?></td>
 			<td class="menu"><?php print $row['onHand'] ?></td>
 			<td class="menu"><?php print $row['reorderPoint'] ?></td>
 			<td class="menu"><?php print $row['backOrder'] ?></td>

 			<!-- A dynamic link which says either DELETE or RESTORE. Sends ID number to delete.php page, stored in $_GET global array -->
 			<td class="menu"><a href="delete.php?id=<?php print $row['id']; ?>"> <?php if ($row['deleted'] == 'y') print 'Restore'; else print 'Delete'; ?> </td>

 		</tr>

 <?php
   }//end of while statement which prints ou table
  } else {//end of if statement which tests if there wre rows to print
  	//if no results found in search, output error message!
  	?>
  		There were no records found! Please try again!
  	<?php
  }
 ?>
</table>
<?php createFooter();
}//ending if statement which only executed program if user was logged in
?>
<!-- ***************** -->
