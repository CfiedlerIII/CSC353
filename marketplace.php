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
	if($_POST["price"] == ""){
		$price = "";
	}
	else{
		$price=addSlashes($_POST["price"]);
		$checks++;
	}
	
	$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
	mysql_select_db("jgavin", $linkID);
	
	if($checks==3 && $userID!=null){
		//get the Team_ID instead of the Team_Name
		$SQL = "SELECT Team_ID FROM Team WHERE Team_Name = '$teamBuy'";
		$allValues = mysql_query($SQL, $linkID);
		if (!$allValues) {
			echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
			exit;
		}
		$thisValue = mysql_fetch_assoc($allValues);
		extract($thisValue);
		$teamBuy = $Team_ID;

		//get an array of all possible sales to buy
		$SQL = "SELECT Amount_Selling,Price,Seller_ID FROM Blueprints_ForSale
		WHERE Team_ID = '$teamBuy' ORDER BY Price";
		$allValues = mysql_query($SQL, $linkID);
		if (!$allValues) {
			echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
			exit;
		}
		$totalrows = mysql_num_rows($allValues);
		$leaveForLoop = false;
		$teamBuy;
		$numBuy;
		$maxPrice;
		$sharesAquired = 0;
		$sharesStillWanted;
		for ($i=1; $i <= $totalrows; $i++){
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
			$Amount_Selling;
			$Price;
			$Seller_ID;
			$sharesStillWanted = $numBuy - $sharesAquired;
			if($sharesStillWanted>0){
				if($Price<=$maxPrice){
					//aquire the current balances of the buyer and seller
					$SQL = "SELECT Account_Balance FROM Players WHERE Player_ID = '$Seller_ID'";
					$allValues = mysql_query($SQL, $linkID);
					if (!$allValues) {
						echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
						exit;
					}
					$thisValue = mysql_fetch_assoc($allValues);
					extract($thisValue);
					$sellerInitBal = $Account_Balance;

					$SQL = "SELECT Account_Balance FROM Players WHERE Player_ID = '$userID'";
					$allValues = mysql_query($SQL, $linkID);
					if (!$allValues) {
						echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
						exit;
					}
					$thisValue = mysql_fetch_assoc($allValues);
					extract($thisValue);
					$buyerInitBal = $Account_Balance;
					
					//if the buyer can afford all wanted shares
					$numSharesBuying = 0;
					$buyerTestBal = $buyerInitBal;
					for($i=0;$i<s$haresStillWanted,$i++){
						$buyerTestBal = $buyerTestBal - $Price;
						if($buyerTestBal>0){
							$numSharesBuying++;
						}
					}
					$sellingSharesLeft = $Amount_Selling - $numSharesBuying;

					//remove shares from the seller's inventory
					if($Amount_Selling>$numSharesBuying){
						$SQL = "UPDATE Players_Team SET Amount_Selling = '$sellingSharesLeft' 
						WHERE Seller_ID = '$Seller_ID'";
						$allValues = mysql_query($SQL, $linkID);
						if (!$allValues) {
							echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
							exit;
						}
					}
					else{
						$SQL = "DELETE FROM Players_Team
						WHERE Player_ID = '$Seller_ID' AND Team_ID = '$teamBuy'";
						$allValues = mysql_query($SQL, $linkID);
						if (!$allValues) {
							echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
							exit;
						}
					}
					

					//get the number of shares of that team the buyer owns
					$SQL = "SELECT NumOfBlueprints FROM Players_Team
					WHERE Team_ID = '$teamBuy' AND Player_ID = '$userID'";
					$allValues = mysql_query($SQL, $linkID);
					if (!$allValues) {
						echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
						exit;
					}
					$thisValue = mysql_fetch_assoc($allValues);
					extract($thisValue);
					$buyerInitShares = $NumOfBlueprints;
					$buyerFinalShares = $buyerInitShares + $numSharesBuying;

					//add the purchased shares to the buyer's inventory
					if($buyerInitShares==0){
						$SQL = "INSERT INTO Players_Team 
						(NumOfBlueprints,Player_ID,Team_ID,Pending)
						VALUES('$numSharesBuying','$userID','$teamBuy',null)";
						$allValues = mysql_query($SQL, $linkID);
						if (!$allValues) {
							echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
							exit;
						}
					}
					else{
						$SQL = "UPDATE Players_Team 
						SET NumOfBlueprints = '$buyerFinalShares'
						WHERE Team_ID = '$teamBuy' AND Player_ID = '$userID'";
						$allValues = mysql_query($SQL, $linkID);
						if (!$allValues) {
							echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
							exit;
						}
					}

					//update both account balances and update transaction table
					$sellerFinalBal = $sellerInitBal + ($Price * $numSharesBuying);
					$buyerFinalBal = $buyerInitBal - ($Price * $numSharesBuying);

					$SQL = "UPDATE Players 
					SET Account_Balance = '$sellerFinalBal'
					WHERE Player_ID = '$Seller_ID';
					UPDATE Players 
					SET Account_Balance = '$buyerFinalBal'
					WHERE Player_ID = '$userID';
					INSERT INTO Transaction
					(Transaction_ID,Value,QuantitySold,Team_ID,Buyer_ID,Seller_ID)
					VALUES(null,'$Price','$numSharesBuying','$teamBuy','$userID','$Seller_ID');";
					$allValues = mysql_query($SQL, $linkID);
					if (!$allValues) {
						echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
						exit;
					}
					$sharesAquired = $sharesAquired + $numSharesBuying;
				}
				//there are no shares selling buyer's price range
				else{
					break;
				}
			}
			else{
				break;
			}
		}
		//if you still want to buy more shares
		if($sharesStillWanted>0){
			$SQL = "INSERT INTO BlueprintsToBuy
			(buyID,buyerID,Price,AmountBuying,Team_ID)
			VALUES(null,'$userID','$maxPrice','$sharesStillWanted','$teamBuy')";
			$allValues = mysql_query($SQL, $linkID);
			if (!$allValues) {
				echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
				exit;
			}
		}
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
