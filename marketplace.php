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
    <li><a href="league.php">League Admin</a></li>
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
<?php
	session_start();
	$inputPart;
	$searchTerm;
	$teamBuy;
	$numBuy;
	$maxPrice;
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
		$maxPrice = "";
	}
	else{
		$maxPrice=addSlashes($_POST["price"]);
		$checks++;
	}
	
	$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
	mysql_select_db("jgavin", $linkID);


	$SQL = "SELECT Team_Name FROM Team";
	$allValues = mysql_query($SQL, $linkID);
	if (!$allValues) {
		echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
		exit;
	}
	$totalrowsOverall = mysql_num_rows($allValues);	
	$select= '<form action="marketplace.php" method="post">
	<label for="teamBuy"><font color=#0099cc>Team To Purchase:</font></label>
	<select name="teamBuy" id="teamBuy" title="teamBuy">';
	for($i = 0;$i<$totalrowsOverall;$i++){
		$thisValue = mysql_fetch_assoc($allValues);
		extract($thisValue);
    	$select.='<option value="'.$Team_Name.'">'.$Team_Name.'
		</option>';
 	}
	$select.='</select>
	<font color=#0099cc>Number to Purchase:</font>
    <input type="text" name="numBuy" maxlength="10" size="10">
    <font color=#0099cc>Price/Blueprint:</font>
    <input type="text" name="prices" maxlength="10" size="10">
    <input type="submit" value="Purchase Blueprints">
    </form>';
	echo $select;

	
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
		$sharesAquired = 0;
		$sharesStillWanted = $numBuy;
		for ($i=1; $i <= $totalrows; $i++){
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
			$Amount_Selling;
			$Price;
			$Seller_ID;
			if($sharesStillWanted>0){
				if($Price<=$maxPrice){
					$sharesStillWanted = $numBuy - $sharesAquired;
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
					for($i=0;$i<$sharesStillWanted;$i++){
						$buyerTestBal = $buyerTestBal - $maxPrice;
						if($buyerTestBal>0){
							$numSharesBuying++;
						}
					}
					
					
					
					$sellingSharesLeft = $Amount_Selling - $numSharesBuying;

					//remove shares from the seller's inventory
					if($Amount_Selling>$numSharesBuying){
						$SQL = "UPDATE Blueprints_ForSale SET Amount_Selling = '$sellingSharesLeft' 
						WHERE Seller_ID = '$Seller_ID' AND Team_ID = ".$teamBuy;
						$allValues = mysql_query($SQL, $linkID);
						if (!$allValues) {
							echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
							exit;
						}
					}
					else{
						//remove the blueprints from the seller's portfolio
						$SQL = "DELETE FROM Blueprints_ForSale
						WHERE Seller_ID = '$Seller_ID' AND Team_ID = '$teamBuy'";
						$allValues = mysql_query($SQL, $linkID);
						if (!$allValues) {
							echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
							exit;
						}
						$numSharesBuying = $Amount_Selling;

						//remove pending marker from seller's portfolio
						$SQL = "UPDATE Players_Team SET Pending = '' 
						WHERE Team_ID = '$teamBuy' AND Player_ID = '$sellerID'";
						$allValues = mysql_query($SQL, $linkID);
						if (!$allValues) {
							echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
							exit;
						}
					}
					
					//get amount owned by seller
					$SQL = "SELECT NumOfBlueprints FROM Players_Team 
					WHERE Team_ID = ".$teamBuy." AND Player_ID = ".$Seller_ID;
					$allValues = mysql_query($SQL, $linkID);
					if (!$allValues) {
						echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
						exit;
					}
					$thisValue = mysql_fetch_assoc($allValues);
					extract($thisValue);
					$finalNumSeller = $NumOfBlueprints - $numSharesBuying;
					
					if($finalNumSeller==0){
						$SQL = "DELETE FROM Players_Team
						WHERE Player_ID = '$Seller_ID' AND Team_ID = '$teamBuy'";
						$allValues = mysql_query($SQL, $linkID);
						if (!$allValues) {
							echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
							exit;
						}
					}
					else{
						$SQL = "UPDATE Players_Team SET NumOfBlueprints = '$finalNumSeller' 
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
					if(empty($thisValue)){
						$buyerInitShares = 0;
					}
					else{
						extract($thisValue);
						$buyerInitShares = $NumOfBlueprints;
					}
					$buyerFinalShares = $buyerInitShares + $numSharesBuying;

					//add the purchased shares to the buyer's inventory
					if($buyerInitShares==0){
						$SQL = "INSERT INTO Players_Team 
						(NumOfBlueprints,Player_ID,Team_ID,Pending)
						VALUES('$numSharesBuying','$userID','$teamBuy','')";
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
					SET Account_Balance = ".$sellerFinalBal."
					WHERE Player_ID = ".$Seller_ID;
					$allValues = mysql_query($SQL, $linkID);
					if (!$allValues) {
						echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
						exit;
					}
					$SQL = "UPDATE Players 
					SET Account_Balance = ".$buyerFinalBal."
					WHERE Player_ID = ".$userID;
					$allValues = mysql_query($SQL, $linkID);
					if (!$allValues) {
						echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
						exit;
					}
					$SQL = "INSERT INTO Transaction
					(Transaction_ID,Value,QuantitySold,Team_ID,Buyer_ID,Seller_ID)
					VALUES(null,".$Price.",".$numSharesBuying.",".$teamBuy.",".$userID.",".$Seller_ID.")";
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
		$sharesStillWanted = $numBuy - $sharesAquired;

		//if you still want to buy more shares
		if($sharesStillWanted>0){
			$SQL = "INSERT INTO BlueprintsToBuy
			(buyID,buyerID,Price,Amount_Buying,Team_ID)
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

<h3><font color=#0099cc>Purchase Request:</font></h3>
  <form action="marketplace.php" method="post">
  <font color=#0099cc>Team to Sell:</font>
  <input type="text" name="teamSell" maxlength="35" size="35">
  <font color=#0099cc>Price to Sell for:</font>
  <input type="text" name="amountSell" maxlength="35" size="35">
  <font color=#0099cc>Blueprints to Sell:</font>
  <input type="text" name="numSell" maxlength="10" size="10">
    <input type="submit" value="Sell Blueprints">
  </form>
<?php
	$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
	mysql_select_db("jgavin", $linkID);
	
		$SQL = "SELECT te.Team_Name, bb.Amount_Buying, bb.Price, p.username AS Buyer_ID
		FROM Players p, Team te, BlueprintsToBuy bb
		Where p.Player_ID = bb.buyerID and te.Team_ID = bb.Team_ID";
	$allValues = mysql_query($SQL, $linkID);
	if (!$allValues) {
		echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
		exit;
	}
  echo "<TABLE BORDER=1 CELLPADDING=8>";
	echo "<TR><TD><B>Team_Name</B></TD><TD><B>Amount_Buying</B></TD><TD><B>Price</B></TD><TD><B>buyerID</B></TD>";
		$totalrows = mysql_num_rows($allValues);
		for ($i=1; $i <= $totalrows; $i++){
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
			echo "<TR>";
			echo "<TD>$Team_Name</TD>";
			echo "<TD>$Amount_Buying</TD>";
			echo "<TD>\$$Price</TD>";
			echo "<TD>$Buyer_ID</TD>";
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
