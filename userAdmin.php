<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="main.css">
</head>
<body>

<title>Blueprint</title>

<ul>
	<li><a href="index.php">Home</a></li>
  	<li><a href="marketplace.php">Marketplace</a></li>
  	<li><a href="portfolio.php">Portfolio</a></li>
    <li><a href="signIn.php">Sign In</a></li>
    <li><a href="signUp.php">Sign Up</a></li>
    <li><a href="logOut.php">Log Out</a></li>
    <li><a href="league.php">League Admin</a></li>
    <li><a class="active" href="userAdmin.php">User Administration</a></li>
    </ul>



<div id="section">

<?php
	session_start();
	$adminStatus = $_SESSION['admin'];
	$removePlayer = "";
	$confirmPlayer = "";
	$adminName = "";
	$removing = false;
	$confirming = false;
	$admin = false;
	$userID = $_SESSION['uuid'];
	//Don't forget to check for admin privleges first!
	if($_POST["removeName"] != ""){
		$removePlayer = $_POST["removeName"];
		$removing = true;
	}
	if($_POST["confirmName"] != ""){
		$confirmPlayer = $_POST["confirmName"];
		$confirming = true;
	}
	if($_POST["adminName"] != ""){
		$adminName = $_POST["adminName"];
		$admin = true;
	}

	if($adminStatus == '1'){
		phpForms();
		displayTable();

		if($removing){
			$SQL = "SELECT Player_ID FROM Players WHERE Username = '".$removePlayer."'";
			$thisValue = sqlQuery($SQL);
			extract($thisValue);
			$removeID = $Player_ID;
			$SQL = "DELETE FROM BlueprintsToBuy WHERE buyerID = ".$removeID;
			sqlAction($SQL);
			$SQL = "DELETE FROM Blueprints_ForSale WHERE Seller_ID = ".$removeID;
			sqlAction($SQL);
			$SQL = "DELETE FROM Players_Team WHERE Player_ID = ".$removeID;
			sqlAction($SQL);
			$SQL = "DELETE FROM Transaction WHERE Buyer_ID = ".$removeID." OR Seller_ID = ".$removeID;
			sqlAction($SQL);
			$SQL = "DELETE FROM Players WHERE Player_ID = ".$removeID;
			sqlAction($SQL);
		}
		if($confirming){
			$SQL = "SELECT Player_ID FROM Players WHERE Username = '".$confirmPlayer."'";
			$thisValue = sqlQuery($SQL);
			extract($thisValue);
			$confirmID = $Player_ID;

			$SQL = "UPDATE Players SET Confirmed = 1 WHERE Player_ID = '".$confirmID."'";
			sqlAction($SQL);
		}
		if($admin){
			$SQL = "SELECT Player_ID FROM Players WHERE Username = '".$adminName."'";
			$thisValue = sqlQuery($SQL);
			extract($thisValue);
			$confirmID = $Player_ID;

			$SQL = "UPDATE Players SET Admin = 1 WHERE Player_ID = '".$confirmID."'";
			sqlAction($SQL);
		}
	}
	else{
		echo "You are not an admin, and do not have administrator privileges.";
	}
	function sqlQuery($SQL){
		$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
		mysql_select_db("jgavin", $linkID);
		$allValues = mysql_query($SQL, $linkID);
		if (!$allValues) {
			echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
			exit;
		}
		$thisValue = mysql_fetch_assoc($allValues);
		mysql_close($linkID);
		return $thisValue;
	}
	function sqlAction($SQL){
		$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
		mysql_select_db("jgavin", $linkID);
		$allValues = mysql_query($SQL, $linkID);
		if (!$allValues) {
			echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
			exit;
		}
		mysql_close($linkID);
	}
	function displayTable(){
		$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
		mysql_select_db("jgavin", $linkID);
		$SQL = "SELECT Username, First_Name, Last_Name, Email_Address, Confirmed, Admin FROM Players;";
		$allValues = mysql_query($SQL, $linkID);
		if (!$allValues) {
			echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
			exit;
		}
		echo "<TABLE BORDER=1 CELLPADDING=8>";
		echo "<TR><TD><B>Username</B></TD><TD><B>First Name</B></TD><TD><B>Last Name</B></TD><TD><B>Email Address</B></TD><TD><B>Confirmed</B></TD><TD><B>Admin</B></TD>";
		$totalrows = mysql_num_rows($allValues);
		for ($i=1; $i <= $totalrows; $i++){
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
			echo "<TR>";
			echo "<TD>$Username</TD>";
			echo "<TD>$First_Name</TD>";
			echo "<TD>$Last_Name</TD>";
			echo "<TD>$Email_Address</TD>";
			if($Confirmed==1){
				echo "<TD>Confirmed</TD>";
			}
			else{
				echo "<TD>Pending Confirmation</TD>";
			}
			if($Admin==1){
				echo "<TD>Admin</TD>";
			}
			else{
				echo "<TD>Player</TD>";
			}
			echo "</TR>";
		}
		echo "</TABLE>";
		mysql_close($linkID);
	}
	function phpForms(){
		$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
		mysql_select_db("jgavin", $linkID);
		//player remove form
		$SQL = "SELECT Username FROM Players ORDER BY Player_ID";
		$allValues = mysql_query($SQL, $linkID);
		if (!$allValues) {
			echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
			exit;
		}
		$totalrowsOverall = mysql_num_rows($allValues);
		$select= '<form action="userAdmin.php" method="post">
		<label for="removeName"><font color=#0099cc>Player to Remove:</font></label>
		<select name="removeName" id="removeName" title="removeName">';
		for($i = 0;$i<$totalrowsOverall;$i++){
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
      		$select.='<option value="'.$Username.'">'.$Username.'
			</option>';
 		}
		$select.='</select>
      	<input type="submit" value="Remove Player">
    	</form>';
		echo $select;

		//player confirmation form
		$SQL = "SELECT Username FROM Players ORDER BY Player_ID";
		$allValues = mysql_query($SQL, $linkID);
		if (!$allValues) {
			echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
			exit;
		}
		$totalrowsOverall = mysql_num_rows($allValues);
		$select= '<form action="userAdmin.php" method="post">
		<label for="confirmName"><font color=#0099cc>Player to Confirm:</font></label>
		<select name="confirmName" id="confirmName" title="confirmName">';
		for($i = 0;$i<$totalrowsOverall;$i++){
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
      		$select.='<option value="'.$Username.'">'.$Username.'
			</option>';
 		}
		$select.='</select>
      	<input type="submit" value="Confirm Player">
    	</form>';
		echo $select;

		//player admin update form
		$SQL = "SELECT Username FROM Players ORDER BY Player_ID";
		$allValues = mysql_query($SQL, $linkID);
		if (!$allValues) {
			echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
			exit;
		}
		$totalrowsOverall = mysql_num_rows($allValues);
		$select= '<form action="userAdmin.php" method="post">
		<label for="adminName"><font color=#0099cc>Player to Make Admin:</font></label>
		<select name="adminName" id="adminName" title="adminName">';
		for($i = 0;$i<$totalrowsOverall;$i++){
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
      		$select.='<option value="'.$Username.'">'.$Username.'
			</option>';
 		}
		$select.='</select>
      	<input type="submit" value="Confirm Admin">
    	</form>';
		echo $select;
		mysql_close($linkID);
	}
?>
</div>



<div id="footer">
2016, Charles Fiedler, Brooks Musangu, Jake Gavin
</div>


</body>
</html>
