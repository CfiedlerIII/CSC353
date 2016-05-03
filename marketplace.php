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
		$teamBuy=$_POST["teamBuy"];
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
		$maxPrice = round(addSlashes($_POST["price"]),2,PHP_ROUND_HALF_DOWN);
		$checks++;
	}
	if($_POST["removeBuy"] != ""){
		$removeBuy = $_POST["removeBuy"];
		removeBuying($removeBuy,$userID);
	}
	
	$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
	mysql_select_db("jgavin", $linkID);
	if($userID!=null){
		//display the user's balances for reference
		$SQL = "SELECT Account_Balance,Available_Balance FROM Players WHERE Player_ID = ".$userID;
		$allValues = mysql_query($SQL, $linkID);
		if (!$allValues) {
			echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
			exit;
		}
		echo "<TABLE BORDER=1 CELLPADDING=8>";
		echo "<TR><TD><B>Current Balance</B></TD><TD><B>Available Balance</B></TD>";
		$thisValue = mysql_fetch_assoc($allValues);
		extract($thisValue);
		echo "<TR>";
		echo "<TD>\$".round($Account_Balance,2,PHP_ROUND_HALF_DOWN)."</TD>";
		echo "<TD>\$".round($Available_Balance,2,PHP_ROUND_HALF_DOWN)."</TD>";
		echo "</TR>";
		echo "</TABLE>";
	}

	//format and display the form for Bblueprints purchasing
	$SQL = "SELECT Team_Name FROM Team";
	$allValues = mysql_query($SQL, $linkID);
	if (!$allValues) {
		echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
		exit;
	}
	$totalrowsOverall = mysql_num_rows($allValues);	
	$select= '<h3><font color=#0099cc>Blueprints to purchase:</font></h3><form action="marketplace.php" method="post">
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
    <input type="text" name="price" maxlength="10" size="10">
    <input type="submit" value="Purchase Blueprints">
    </form>';
	echo $select;

	
	if($checks==3 && $userID!=null){
		//get the Team_ID instead of the Team_Name
		$SQL = "SELECT Team_ID FROM Team WHERE Team_Name = '$teamBuy'";
		$thisValue = sqlQuery($SQL);
		extract($thisValue);
		$teamBuy = $Team_ID;

		$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
		mysql_select_db("jgavin", $linkID);
		//get an array of all possible sales to buy
		$SQL = "SELECT Amount_Selling,Price,Seller_ID FROM Blueprints_ForSale
		WHERE Team_ID = '$teamBuy' ORDER BY Price";
		$allValues = mysql_query($SQL, $linkID);
		if (!$allValues) {
			echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
			exit;
		}
		$totalrows = mysql_num_rows($allValues);
		$sharesAquired = 0;
		$sharesStillWanted = $numBuy;

		for ($i=1; $i <= $totalrows; $i++){
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
			if($userID!=$Seller_ID){
				if($sharesStillWanted>0){
					if($Price<=$maxPrice){
						//define the seller's account balances
						$SQL = "SELECT Account_Balance, Available_Balance FROM Players WHERE Player_ID = '$Seller_ID'";
						$thisValue = sqlQuery($SQL);
						extract($thisValue);
	
						$sellerInitBal = $Account_Balance;
						$sellerInitAvail = $Available_Balance;
	
						//define the buyer's balances
						$SQL = "SELECT Account_Balance, Available_Balance FROM Players WHERE Player_ID = '$userID'";
						$thisValue = sqlQuery($SQL);
						extract($thisValue);
	
						$buyerInitBal = $Account_Balance;
						$buyerInitAvail = $Available_Balance;
						
						//if the buyer can afford all wanted shares
						$numSharesBuying = 0;
						$buyerTestBal = $buyerInitAvail;
						for($j=0;$j<$sharesStillWanted;$j++){
							$buyerTestBal = $buyerTestBal - $maxPrice;
							if($buyerTestBal>0.0){
								$numSharesBuying++;
							}
						}
						
						//remove shares from the seller's inventory
						if($numSharesBuying<$Amount_Selling){
							$sellingSharesLeft = $Amount_Selling - $numSharesBuying;
							$SQL = "UPDATE Blueprints_ForSale SET Amount_Selling = '$sellingSharesLeft' 
							WHERE Seller_ID = '$Seller_ID' AND Team_ID = ".$teamBuy;
							sqlAction($SQL);
						}
						else{
							//remove the blueprints from the seller's portfolio
							$SQL = "DELETE FROM Blueprints_ForSale
							WHERE Seller_ID = '$Seller_ID' AND Team_ID = '$teamBuy'";
							sqlAction($SQL);
	
							$numSharesBuying = $Amount_Selling;
	
							//remove pending marker from seller's portfolio
							$SQL = "UPDATE Players_Team SET Pending = '' 
							WHERE Team_ID = '$teamBuy' AND Player_ID = '$Seller_ID'";
							sqlAction($SQL);
						}
						
						//get number of shares owned by seller
						$SQL = "SELECT NumOfBlueprints FROM Players_Team 
						WHERE Team_ID = ".$teamBuy." AND Player_ID = ".$Seller_ID;
						$thisValue = sqlQuery($SQL);
						if(empty($thisValue)){
							$buyerInitShares = 0;
						}
						else{
							extract($thisValue);
						}
	
						//define the final number of shares for the seller
						$finalNumSeller = $NumOfBlueprints - $numSharesBuying;
						
						//remove blueprints from the seller's portfolio
						if($finalNumSeller==0){
							$SQL = "DELETE FROM Players_Team
							WHERE Player_ID = '$Seller_ID' AND Team_ID = '$teamBuy'";
							sqlAction($SQL);
						}
						else{
							$SQL = "UPDATE Players_Team SET NumOfBlueprints = '$finalNumSeller' 
							WHERE Player_ID = '$Seller_ID' AND Team_ID = '$teamBuy'";
							sqlAction($SQL);
						}
						
	
						//get the number of shares of that team the buyer owns
						$SQL = "SELECT NumOfBlueprints FROM Players_Team
						WHERE Team_ID = '$teamBuy' AND Player_ID = '$userID'";
						$thisValue = sqlQuery($SQL);
	
						if(empty($thisValue)){
							$buyerInitShares = 0;
						}
						else{
							extract($thisValue);
							$buyerInitShares = $NumOfBlueprints;
						}
	
						//define the final number of shares for the purchaser
						$buyerFinalShares = $buyerInitShares + $numSharesBuying;
	
						//add the purchased shares to the buyer's inventory
						if($buyerInitShares==0){
							$SQL = "INSERT INTO Players_Team 
							(NumOfBlueprints,Player_ID,Team_ID,Pending)
							VALUES('$numSharesBuying','$userID','$teamBuy','')";
							sqlAction($SQL);
						}
						else{
							$SQL = "UPDATE Players_Team 
							SET NumOfBlueprints = '$buyerFinalShares'
							WHERE Team_ID = '$teamBuy' AND Player_ID = '$userID'";
							sqlAction($SQL);
						}
						
						//update both account balances and update transaction table
						echo "NSB: ".$numSharesBuying;
						echo "maxP: ".$maxPrice;
						$sellerFinalBal = $sellerInitBal + ($maxPrice * $numSharesBuying);
						$sellerFinalAvail = $sellerInitAvail + ($maxPrice * $numSharesBuying);
						$buyerFinalBal = $buyerInitBal - ($maxPrice * $numSharesBuying);
						$buyerFinalAvail = $buyerInitAvail - ($maxPrice * $numSharesBuying);
	
						$SQL = "UPDATE Players 
						SET Account_Balance = ".$sellerFinalBal.",
						Available_Balance = ".$sellerFinalAvail."
						WHERE Player_ID = ".$Seller_ID;
						sqlAction($SQL);
	
						$SQL = "UPDATE Players 
						SET Account_Balance = ".$buyerFinalBal.",
						Available_Balance = ".$buyerFinalAvail."
						WHERE Player_ID = ".$userID;
						sqlAction($SQL);
	
						$SQL = "INSERT INTO Transaction
						(Transaction_ID,Value,QuantitySold,Team_ID,Buyer_ID,Seller_ID)
						VALUES(null,".$maxPrice.",".$numSharesBuying.",".$teamBuy.",".$userID.",".$Seller_ID.")";
						sqlAction($SQL);
	
						$sharesAquired = $sharesAquired + $numSharesBuying;
						$sharesStillWanted = $numBuy - $sharesAquired;
					}
					else{
						break;
					}
				}
				else{
					break;
				}
			}
			else{
				echo "<h2>YOU ARE CURRENTLY SELLING THAT TEAM.</h2>";
				echo "<h2>REMOVE YOUR PENDING SELL BEFORE PURCHASING MORE..</h2>";
			}
		}
		$sharesStillWanted = $numBuy - $sharesAquired;

		//if you still want to buy more shares
		if($sharesStillWanted>0){
			//insert a new pending buy into BlueprintsToBuy
			$SQL = "INSERT INTO BlueprintsToBuy
			(buyID,buyerID,Price,Amount_Buying,Team_ID)
			VALUES(null,'$userID','$maxPrice','$sharesStillWanted','$teamBuy')";
			sqlAction($SQL);
			
			$SQL = "SELECT Available_Balance FROM Players WHERE Player_ID = '$userID'";
			$thisValue = sqlQuery($SQL);
			extract($thisValue);

			//new balance available is decremented by the total cost of the pending buy
			$availBalance = $Available_Balance - ($maxPrice * $sharesStillWanted);
			
			$SQL = "UPDATE Players SET Available_Balance = ".$availBalance." 
			WHERE Player_ID = '$userID'";
			sqlAction($SQL);
		}
	}
	
	//display the Blueprints_ForSale table
	if($searchTerm==""){
		$SQL = "SELECT te.Team_Name, bs.Amount_Selling, bs.Price 
		FROM Players p, Team te, Blueprints_ForSale bs 
		Where p.Player_ID = bs.Seller_ID and te.Team_ID = bs.Team_ID Order By ".$sort;
	}
	else{
		$SQL = "SELECT te.Team_Name, bs.Amount_Selling, bs.Price
		FROM Players p, Team te, Blueprints_ForSale bs Where p.Player_ID = bs.Seller_ID and 
		te.Team_ID = bs.Team_ID and (Team_Name LIKE '%".$searchTerm."%' or 
		Price LIKE '%".$searchTerm."%') ORDER BY ".$sort;
	}
	$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
	mysql_select_db("jgavin", $linkID);
	$allValues = mysql_query($SQL, $linkID);
	if (!$allValues) {
		echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
		exit;
	}
	echo "<TABLE BORDER=1 CELLPADDING=8>";
	echo "<TR><TD><B>Team Name</B></TD><TD><B>Amount Selling</B></TD><TD><B>Price</B></TD>";
	$totalrows = mysql_num_rows($allValues);
	for ($i=1; $i <= $totalrows; $i++){
		$thisValue = mysql_fetch_assoc($allValues);
		extract($thisValue);
		echo "<TR>";
		echo "<TD>$Team_Name</TD>";
		echo "<TD>$Amount_Selling</TD>";
		echo "<TD>\$".round($Price,2,PHP_ROUND_HALF_DOWN)."</TD>";
		echo "</TR>";
	}
	echo "</TABLE>";


	mysql_close($linkID);
?>

<h3><font color=#0099cc>Buy Requests:</font></h3>

<?php
	$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
	mysql_select_db("jgavin", $linkID);
	
	//format and display the form for removing pending buys
	$SQL = "SELECT Team_Name FROM Team t,BlueprintsToBuy b
	WHERE t.Team_ID = b.Team_ID
	AND buyerID = '$userID'";
	$allValues = mysql_query($SQL, $linkID);
	if (!$allValues) {
		echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
		exit;
	}
	$totalrowsOverall = mysql_num_rows($allValues);	
	$select= '<form action="marketplace.php" method="post">
	<label for="removeBuy"><font color=#0099cc>Remove Pending Buy:</font></label>
	<select name="removeBuy" id="removeBuy" title="removeBuy">';
	for($i = 0;$i<$totalrowsOverall;$i++){
		$thisValue = mysql_fetch_assoc($allValues);
		extract($thisValue);
    	$select.='<option value="'.$Team_Name.'">'.$Team_Name.'
		</option>';
 	}
	$select.='</select>
    <input type="submit" value="Remove Pending Buys">
    </form>';
	echo $select;

	//display the BlueprintsToBuy table	
	$SQL = "SELECT te.Team_Name, bb.Amount_Buying, bb.Price
	FROM Players p, Team te, BlueprintsToBuy bb
	Where p.Player_ID = bb.buyerID and te.Team_ID = bb.Team_ID";
	$allValues = mysql_query($SQL, $linkID);
	if (!$allValues) {
		echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
		exit;
	}
    echo "<TABLE BORDER=1 CELLPADDING=8>";
	echo "<TR><TD><B>Team Name</B></TD><TD><B>Amount Buying</B></TD><TD><B>Price</B></TD>";
	$totalrows = mysql_num_rows($allValues);
	for ($i=1; $i <= $totalrows; $i++){
		$thisValue = mysql_fetch_assoc($allValues);
		extract($thisValue);
		echo "<TR>";
		echo "<TD>$Team_Name</TD>";
		echo "<TD>$Amount_Buying</TD>";
		echo "<TD>\$".round($Price,2,PHP_ROUND_HALF_DOWN)."</TD>";
		echo "</TR>";
	}
	echo "</TABLE>";
	mysql_close($linkID);


		
	function removeBuying($removeBuy,$userID){
		//aquire the user's available balance
		$SQL = "SELECT Available_Balance FROM Players WHERE Player_ID = '$userID'";
		$thisValue = sqlQuery($SQL);
		extract($thisValue);

		$availBalance = $Available_Balance;
		
		//aquire the rows from BlueprintsToBuy
		$SQL = "SELECT Amount_Buying, Price FROM BlueprintsToBuy 
		WHERE buyerID = '$userID' AND Team_ID = (SELECT Team_ID FROM Team WHERE Team_Name = '$removeBuy')";
		$thisValue = sqlQuery($SQL);
		extract($thisValue);

		$totalCost = $Price * $Amount_Buying;
		$availBalance = $availBalance + $totalCost;
		
		//update the user's available balance
		$SQL = "UPDATE Players SET Available_Balance = ".$availBalance." 
		WHERE Player_ID = '$userID'";
		sqlAction($SQL);

		//remove the user's pending buy
		$SQL = "DELETE FROM BlueprintsToBuy WHERE buyerID = '$userID' 
		AND Team_ID = (SELECT Team_ID FROM Team WHERE Team_Name = '$removeBuy')";
		sqlAction($SQL);
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
?>
</div>



<div id="footer">
2016, Charles Fiedler, Brooks Musangu, Jake Gavin
</div>


</body>
</html>
