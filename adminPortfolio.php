<!DOCTYPE html>
<html>
<head>
<style>
#header {
    background-color:#0099cc;
    color:white;
    text-align:center;
	width:100%;
}
#section {
	background-color:#e6e6e6;
    width:100%;
    float:left;
    padding:10px;	 	 
}
#footer {
    background-color:#0099cc;
    color:white;
    clear:both;
    text-align:center;
    padding:5px;	
    width:100%; 	 
}
body {
	background-color:#0099cc
}
title {
    font-size:60px;
	color:white;
    background-color:#0099cc;   
	text-align: right;   
}
ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
    background-color: #0099cc;
}

li {
    float: left;
}

li a {
    display: block;
    color: white;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
}

a:hover:not(.active) {
    background-color: #111;
}

.active {
background-color:#4CAF50;
}
</style>
</head>
<body>

<title>Blueprint</title>

<ul>
	<li><a href="index.php">Home</a></li>
  	<li><a href="marketplace.php">Marketplace</a></li>
  	<li><a class="active" href="portfolio.php">Portfolio</a></li>
    <li><a href="signIn.php">Sign In</a></li>
    <li><a href="signUp.php">Sign Up</a></li>
    <li><a href="logOut.php">Log Out</a></li>
    </ul>
    


<div id="section">

<?php
	session_start();
	$adminStatus = $_SESSION['admin'];
	$playerName1 = "p";
	$playerName2 = "q";
	$same = false;
	$userID = $_SESSION['uuid'];
	//Don't forget to check for admin privleges first!
	if($_POST["playerName1"] != ""){
		$playerName1 = addSlashes($_POST["playerName1"]);
	}
	if($_POST["playerName2"] != ""){
		$playerName2 = addSlashes($_POST["playerName2"]);
	}
	if($playerName1==$playerName2){
		$same = true;
	}
	
	if($adminStatus == '1'){
		$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
		mysql_select_db("jgavin", $linkID);
		$SQL = "SELECT Username, First_Name, Last_Name, Email_Address FROM Players;";
		$allValues = mysql_query($SQL, $linkID);
		if (!$allValues) {
			echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
			exit;
		}
		echo "<TABLE BORDER=1 CELLPADDING=8>";
		echo "<TR><TD><B>Username</B></TD><TD><B>First_Name</B></TD><TD><B>Last_Name</B></TD>	<TD><B>Email_Address</B></TD>";
		$totalrows = mysql_num_rows($allValues);
		for ($i=1; $i <= $totalrows; $i++){
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
			echo "<TR>";
			echo "<TD>$Username</TD>";
			echo "<TD>$First_Name</TD>";
			echo "<TD>$Last_Name</TD>";
			echo "<TD>$Email_Address</TD>";
			echo "</TR>";
		}
		echo "</TABLE>";
	
		mysql_close($linkID);
		if($same){
			$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
			mysql_select_db("jgavin", $linkID);
			$SQL = "SELECT Player_ID FROM Players WHERE Username = '".$playerName1."'";
			$allValues = mysql_query($SQL, $linkID);
			if (!$allValues) {
				echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
				exit;
			}
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
			$removeID = $Player_ID;
			$SQL = "DELETE FROM Players WHERE Player_ID = '".$removeID."'";
		
			$allValues = mysql_query($SQL, $linkID);
			if (!$allValues) {
				echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
				exit;
			}
			mysql_close($linkID);
		}
	}
	
?>
<h3><font color=#0099cc>Remove a Player:</font></h3>
    <form action="adminPortfolio.php" method="post">
	<font color=#0099cc>Player to Remove:</font>
    <input type="text" name="playerName1" maxlength="35" size="35">
    <font color=#0099cc>Confirm Player:</font>
    <input type="text" name="playerName2" maxlength="10" size="10">
      <input type="submit" value="Remove Player">
    </form>
</div>



<div id="footer">
2016, Charles Fiedler, Brooks Musangu, Jake Gavin
</div>


</body>
</html>
