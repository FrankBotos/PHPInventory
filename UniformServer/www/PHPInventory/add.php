<?php

session_start();
include('library.php');

//before anything else, protecting against xss attacks
//regular expressions protect most fields, however item name and description need to be protected
if (isset($_POST['itemname'])){
	$_POST['itemname'] = htmlspecialchars($_POST['itemname']);
}
if (isset($_POST['description'])){
	$_POST['description'] = htmlspecialchars($_POST['description']);
}

if (!isset($_SESSION['username'])){//checking if login was successful, if not redirect to login page
	header('Location: login.php');
} else {

$isValid = true;
$nameErr = "";
$descErr = "";
$suppErr = "";
$costErr = "";
$sellErr = "";
$numErr = "";
$reorderErr = "";
$backorderErr = "";

	if ($_POST){//validating form data if form is submitted via post

		if (!preg_match("/[a-z0-9;:'-,]/i", $_POST['itemname'])){//checking to see if item name is valid, allows only characters in square brackets
			$nameErr = " * Error! Item name is not valid! Valid characters: a-z, 0-9, colon, semi-colon, apostrophe, dash, comma! Must not be blank!";
			$isValid = false;
		}

		if(!preg_match("/[a-z0-9.,'-]\r?\n?/i", $_POST['description'])){//checking to see if description is valid, allows only characters in square brackets, followed by new line
			$descErr = " * Error! Item description is not valid! Valid characters: a-z, 0-9, period, comma, apostrophe, dashes, new line delimiter! Must not be blank!";
			$isValid = false;
		}

		if(!preg_match("/^[-a-z0-9\s]+$/i", $_POST['suppliercode'])){//only dashes, a-z,0-9, and spaces
			$suppErr = " * Error! Supplier Code is not valid! Valid characters: a-z, 0-9, dash, space! Must not be blank!";
			$isValid = false;
		}

		if(!preg_match("/^\d{1,}?\.?\d{1,2}?$/", $_POST['cost'])){//only dollar value
			$costErr = " * Error! Cost is not valid! Must be a dollar value! Must not be blank!";
			$isValid = false;
		}

		if(!preg_match("/^\d{1,}?\.?\d{1,2}?$/", $_POST['sellingprice'])){//only dollar value
			$sellErr = " * Error! Selling Price is not valid! Must be a dollar value! Must not be blank!";
			$isValid = false;
		}

		if(!preg_match("/^\d{1,12}$/", $_POST['numonhand'])){//only numeric
			$numErr = " * Error! Number on Hand not valid! Must be a numeric value! Must not be blank!";
			$isValid = false;
		}

		if(!preg_match("/^\d{1,12}$/", $_POST['reorder'])){//only numeric
			$reorderErr = " * Error! Reorder Point value not valid! Must be a numeric value! Must not be blank!";
			$isValid = false;
		}
	}

		if ($_POST && $isValid){//if form is successfully validated, process form
			//form processed here

			$link = connectDB();//connecting to database via externally defined function


			$modify = false;
			if(isset($_POST['id']) || isset($_GET['getID'] )){
				$modify = true;
			}

			if (!$modify){
			//creating sql query
				$iName = "'" . mysqli_real_escape_string($link,$_POST['itemname']) . "'";//taking precautions and protecting two input fields from sql injection that are vulnerable
				$desc = "'" . mysqli_real_escape_string($link,$_POST['description']) . "'";//taking precautions and protecting two input fields from sql injection that are vulnerable
				$suppCode = "'" . $_POST['suppliercode'] . "'";
				$sqlCost = "'" . $_POST['cost'] . "'";
				$sqlSellPrice = "'" . $_POST['sellingprice'] . "'";
				$sqlNumOnHand = "'" . $_POST['numonhand'] . "'";
				$sqlReorder = "'" . $_POST['reorder'] . "'";
				$sqlBackOrder = "";
				if ($_POST['backorder']){
					$sqlBackOrder = "'y'";
				} else {
					$sqlBackOrder = "'n'";
				}

				$sql_query = "INSERT INTO `inventory` (`id`, `itemName`, `description`, `supplierCode`, `cost`, `price`, `onHand`, `reorderPoint`, `backOrder`, `deleted`)
				VALUES (NULL, " . $iName . ", " . $desc . ", " . $suppCode . ", " . $sqlCost . " ," . $sqlSellPrice . "," . $sqlNumOnHand . ", " . $sqlReorder . ", " . $sqlBackOrder . ", 'n');";
			} else {//execute if modifying
				$staticID = "'" . $_POST['id'] . "'";
				$iName = "'" . mysqli_real_escape_string($link,$_POST['itemname']) . "'";//taking precautions and protecting two input fields from sql injection that are vulnerable
				$desc = "'" . mysqli_real_escape_string($link,$_POST['description']) . "'";//taking precautions and protecting two input fields from sql injection that are vulnerable
				$suppCode = "'" . $_POST['suppliercode'] . "'";
				$sqlCost = "'" . $_POST['cost'] . "'";
				$sqlSellPrice = "'" . $_POST['sellingprice'] . "'";
				$sqlNumOnHand = "'" . $_POST['numonhand'] . "'";
				$sqlReorder = "'" . $_POST['reorder'] . "'";
				$sqlBackOrder = "";
				if ($_POST['backorder']){
					$sqlBackOrder = "'y'";
				} else {
					$sqlBackOrder = "'n'";
				}

				$sql_query = "UPDATE `inventory`
				SET `itemName` = " . $iName . ", `description` = " . $desc . ", `supplierCode` = " . $suppCode . ", `cost` = " . $sqlCost . ", `price` = " . $sqlSellPrice . ", `onHand` = " . $sqlNumOnHand . ", `reorderPoint` = " . $sqlReorder . ", `backOrder` = " . $sqlBackOrder . "
				WHERE `id` = " . $staticID . ";";
			}

			//for debugging sql query
			//echo $sql_query;

            //run sql query, update database
            mysqli_query($link, $sql_query)
      		or die ('Oops, query failed!' . mysqli_error($link));
			/********************************************/

			//once database is updated, redirect to view page
			header('Location: view.php');
	    } else {//display and/or repopulate form!
		?>
		<head><title>Add Entry</title></head>
		<?php
			useCss();
			createMenu();
		?>
		<form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post"> <!-- FORM ACTION POINTS TO SELF, so that header can be displayed before any output!!! -->
		<table>

			<?php
			//code block checks if item is current being modified or added, and displays ID only if it is being modified
				if(isset($_GET['getID']) || isset($_POST['id'])){
					?>
						<tr>
						<td>
						ID #: <input name="id" type="text" readonly="readonly" value="<?php if (isset($_GET['getID'])) print($_GET['getID']); if(isset($_POST['id'])) print($_POST['id']); ?>"> <br /><br />
						</td>
						</tr>
					<?php
				}
			?>

			<tr>
			<td>
			Item Name: <input name="itemname" type="text" value="<?php if (isset($_POST['itemname'])) echo $_POST['itemname']; ?>"><?php echo "<font color='#990000'>" . $nameErr . "</font>" ;?> <br /><br />
			</td>
			</tr>

			<tr>
			<td>
			Description: <textarea name="description" rows="4" cols="25" value="<?php if (isset($_POST['description'])) echo $_POST['description']; ?>"></textarea> <?php echo "<font color='#990000'>" . $descErr . "</font>";?> <br /><br />
			</td>
			</tr>

			<tr>
			<td>
			Supplier Code: <input name="suppliercode" type="text" value="<?php if (isset($_POST['suppliercode'])) echo $_POST['suppliercode']; ?>"><?php echo "<font color='#990000'>" .  $suppErr . "</font>";?> <br /><br />
			</td>
			</tr>

			<tr>
			<td>
			Cost: <input name="cost" type="text" value="<?php if (isset($_POST['cost'])) echo $_POST['cost']; ?>"><?php echo "<font color='#990000'>" . $costErr . "</font>";?> <br /><br />
			</td>
			</tr>

			<tr>
			<td>
			Selling Price: <input name="sellingprice" type="text" value="<?php if (isset($_POST['sellingprice'])) echo $_POST['sellingprice']; ?>"><?php echo "<font color='#990000'>" . $sellErr . "</font>";?> <br /><br />
			</td>
			</tr>

			<tr>
			<td>
			Number on Hand: <input name="numonhand" type="text" value="<?php if (isset($_POST['numonhand'])) echo $_POST['numonhand']; ?>"><?php echo "<font color='#990000'>" . $numErr . "</font>" ;?> <br /><br />
			</td>
			</tr>

			<tr>
			<td>
			Reorder Point: <input name="reorder" type="text" value="<?php if (isset($_POST['reorder'])) echo $_POST['reorder']; ?>"><?php echo "<font color='#990000'>" . $reorderErr . "</font>" ;?> <br /><br />
			</td>
			</tr>

			<tr>
			<td>
			On Back Order: <input name="backorder" type="checkbox" <?php if ($_POST['backorder']) echo "CHECKED"; ?> >
			</td>
			</tr>

			<tr>
			<td><br /><br /><input name="submit" type="submit"></td>
			</tr>
		</table>
		</form>
		<?php
	}
?>
<?php createFooter();
}
?>
