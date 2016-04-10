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
  	<li><a class="active" href="marketplace.php">Marketplace</a></li>
  	<li><a href="portfolio.php">Portfolio</a></li>
    <li><a href="signIn.php">Sign In</a></li>
    <li><a href="signUp.php">Sign Up</a></li>
    <li><a href="logOut.php">Log Out</a></li>
</ul>


<div id="section">
<h2><font color=#0099cc>Blueprints for Sale:</font></h2><br />
<form action="marketplace.php" method="post">
	<font color=#0099cc>Search:</font>
    <input type="text" name="searchTerm" maxlength="35" size="35">
    <label for="sort"><font color=#0099cc>Sort By:</font></label>
    <select name="sort" id="sort" title="sort">
      <option value="Team_Name">Team_Name</option>
      <option value="price">Price</option>
      <option value="sellerID">Seller_ID</option>
    </select>
      <input type="submit" value="Update"><br>
    </form>
    
    <h3><font color=#0099cc>Blueprints to purchase:</font></h3>
    <form action="marketplace.php" method="post">
	<font color=#0099cc>Team to Buy:</font>
    <input type="text" name="teamBuy" maxlength="35" size="35">
    <font color=#0099cc>Seller to Buy From:</font>
    <input type="text" name="seller" maxlength="35" size="35">
    <font color=#0099cc>Blueprints to Buy:</font>
    <input type="text" name="numBuy" maxlength="10" size="10">
      <input type="submit" value="Buy Blueprints">
    </form>
<?php
	session_start();
	$inputPart;
	$searchTerm;
	$teamBuy;
	$numBuy;
	$seller;
	$buyAll = false;
	$checks = 0;
	$userID = $_SESSION['uuid'];
	if($_POST["searchTerm"] == ""){
		$searchTerm = "";
	}
	else{
		$searchTerm=addSlashes($_POST["searchTerm"]);
	}
	$inputSort=$_POST["sort"];
	$sort;
	if($inputSort == "Team_Name"){
		$sort = "te.Team_Name";
	}
	else if($inputSort == "sellerID"){
		$sort = "p.username";
	}
	else{
		$sort = "Price";
	}
	if($_POST["teamBuy"] == ""){
		$teamBuy = "";
	}
	else{
		$teamBuy=addSlashes($_POST["teamBuy"]);
		$checks++;
	}
	if($_POST["numBuy"] == ""){
		$numBuy = "";
	}
	else{
		$numBuy=addSlashes($_POST["numBuy"]);
		$checks++;
	}
	if($_POST["seller"] == ""){
		$seller = "";
	}
	else{
		$seller=addSlashes($_POST["seller"]);
		$checks++;
	}
	
	$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
	mysql_select_db("jgavin", $linkID);
	
	if($checks==3 && $userID!=null){
		echo "I made it in!";
		$SQL = "SELECT SUM(bs.Amount_Selling) as Amount_Available,
			SUM(Price*Amount_Selling) as capital
			FROM Blueprints_ForSale bs WHERE Team_ID = 
			(SELECT Team_ID FROM Team WHERE Team_Name = '".$teamBuy."');";
		$allValues = mysql_query($SQL, $linkID);
		if (!$allValues) {
			echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
			exit;
		}
		$thisValue = mysql_fetch_assoc($allValues);
		extract($thisValue);
		$available = $Amount_Available;
		$necessaryCapital = $capital;
		
		$SQL = "SELECT Account_Balance FROM Players 
		WHERE Player_ID = '".$userID."';";
		$allValues = mysql_query($SQL, $linkID);
		if (!$allValues) {
			echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
			exit;
		}
		$thisValue = mysql_fetch_assoc($allValues);
		extract($thisValue);
		$yourCapital = $Account_Balance;
		
		if($numBuy >= $available){
			$buyAll = true;
			$numBuy = $available;
		}
		if($buyAll && ($yourCapital >= $necessaryCapital)){
			$newBalance = $yourCapital - $necessaryCapital;
			$SQL = "DELETE FROM Blueprints_ForSale 
			WHERE Team_ID =(SELECT Team_ID FROM Team WHERE Team_Name = '".$teamBuy."') 
			AND Seller_ID = (SELECT Player_ID FROM Players WHERE Username = '".$seller."');";
			$allValues = mysql_query($SQL, $linkID);
			if (!$allValues) {
				echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
				exit;
			}
			$SQL = "DELETE FROM Players_Team 
			WHERE Team_ID =(SELECT Team_ID FROM Team WHERE Team_Name = '".$teamBuy."')";
			$allValues = mysql_query($SQL, $linkID);
			if (!$allValues) {
				echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
				exit;
			}
			
			$SQL = "INSERT INTO Players_Team 
			(Player_ID,Team_ID,NumOfBlueprints) 
			VALUES(".$userID.",(SELECT Team_ID FROM Team WHERE Team_Name = '".$teamBuy."'),".$numBuy.");";
			$allValues = mysql_query($SQL, $linkID);
			if (!$allValues) {
				echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
				exit;
			}
			
			$SQL = "UPDATE Players SET Account_Balance = ".$newBalance." 
			WHERE Player_ID = '".$userID."'";
			$allValues = mysql_query($SQL, $linkID);
			if (!$allValues) {
				echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
				exit;
			}
			
			$SQL = "SELECT Account_Balance FROM Players WHERE Username = '".$seller."'";
			$allValues = mysql_query($SQL, $linkID);
			if (!$allValues) {
				echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
				exit;
			}
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
			echo $newBalance;
			$newBalance = $Account_Balance + $necessaryCapital;
			echo $newBalance;
			
			$SQL = "SELECT Player_ID FROM Players WHERE Username = '".$seller."'";
			$allValues = mysql_query($SQL, $linkID);
			if (!$allValues) {
				echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
				exit;
			}
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
			$sellerID = $Player_ID;
			
			$SQL = "UPDATE Players SET Account_Balance = ".$newBalance." 
			WHERE Player_ID = '".$sellerID."'";
			$allValues = mysql_query($SQL, $linkID);
			echo $SQL;
			if (!$allValues) {
				echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
				exit;
			}
			/*
			$price = $necessaryCapital/$numBuy;
			$SQL = "INSERT INTO Transaction (Transaction_ID,Value,QuantitySold,Team_ID,Buyer_ID,Seller_ID)
			VALUES(null,".$price.",".$numBuy.",(SELECT Team_ID FROM Team WHERE Team_Name = '".$teamBuy."'),'".$userID."','".$sellerID."');";
			$allValues = mysql_query($SQL, $linkID);
			echo $SQL;
			if (!$allValues) {
				echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
				exit;
			}*/
		}/*
		THIS PLACE RESERVED FOR BUYING LESS THAN ALL OF THE BLUEPRINTS BEING SOLD!
		else{
		$SQL = "UPDATE";
		}*/
		
	}
	
	
	if($searchTerm==""){
		$SQL = "SELECT te.Team_Name, bs.Amount_Selling, bs.Price, p.username AS Seller_ID 
		FROM Players p, Team te, Blueprints_ForSale bs 
		Where p.Player_ID = bs.Seller_ID and te.Team_ID = bs.Team_ID Order By ".$sort;
	}
	else{
		$SQL = "SELECT te.Team_Name, bs.Amount_Selling, bs.Price, p.username AS Seller_ID 
		FROM Players p, Team te, Blueprints_ForSale bs Where p.Player_ID = bs.Seller_ID and 
		te.Team_ID = bs.Team_ID and (Team_Name LIKE '%".$searchTerm."%' or 
		Price LIKE '%".$searchTerm."%') ORDER BY ".$sort;
	}
	$allValues = mysql_query($SQL, $linkID);
	if (!$allValues) {
		echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
		exit;
	}
	echo "<TABLE BORDER=1 CELLPADDING=8>";
	echo "<TR><TD><B>Team_Name</B></TD><TD><B>Amount_Selling</B></TD><TD><B>Price</B></TD><TD><B>Seller_ID</B></TD>";
		$totalrows = mysql_num_rows($allValues);
		for ($i=1; $i <= $totalrows; $i++){
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
			echo "<TR>";
			echo "<TD>$Team_Name</TD>";
			echo "<TD>$Amount_Selling</TD>";
			echo "<TD>\$$Price</TD>";
			echo "<TD>$Seller_ID</TD>";
			echo "</TR>";
		}
		echo "</TABLE>";
	mysql_close($linkID);
?>
</div>



<div id="footer">
2016, Charles Fiedler, Brooks Musangu, Jake Gavin
</div>


</body>
</html>
