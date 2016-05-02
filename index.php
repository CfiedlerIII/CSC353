<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="main.css">
</head>
<body>

<title>Blueprint</title>

<ul>
	<li><a class="active" href="index.php">Home</a></li>
  	<li><a href="marketplace.php">Marketplace</a></li>
  	<li><a href="portfolio.php">Portfolio</a></li>
    <li><a href="signIn.php">Sign In</a></li>
    <li><a href="signUp.php">Sign Up</a></li>
    <li><a href="logOut.php">Log Out</a></li>
    <li><a href="league.php">League Admin</a></li>
</ul>


<div id="section">
<h2><font color=#0099cc>See what's going on:</font></h2><br />
<form action="index.php" method="post">
	<font color=#0099cc>Search:</font>
    <input type="text" name="searchTerm" maxlength="35" size="35">
    <label for="sort"><font color=#0099cc>Sort By:</font></label>
    <select name="sort" id="sort" title="sort">
      <option value="trans">ID#</option>
      <option value="buyer">Buyer ID</option>
    </select>
      <input type="submit" value="Update"><br>
    </form>
<?php
	session_start();
	$inputPart;
	$searchTerm;
	if($_POST["searchTerm"] == ""){
		$searchTerm = "";
	}
	else{
		$searchTerm=addSlashes($_POST["searchTerm"]);
	}
	$inputSort=$_POST["sort"];
	$sort;
	if($inputSort == "trans"){
		$sort = "Transaction_ID";
	}
	else{
		$sort = "Buyer_ID";
	}

	$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
	mysql_select_db("jgavin", $linkID);
	//if($searchTerm==""){
	//	$SQL = "SELECT * FROM Transaction ORDER BY ".$sort;
	//}
	//else{
		//$SQL = "SELECT *  FROM Transaction WHERE Transaction_ID LIKE '%".$searchTerm."%' ORDER BY ".$sort;}
		if($searchTerm==""){
			$SQL = "SELECT t.Transaction_ID, t.Value, t.QuantitySold, te.Team_Name, 		 p.username as Buyer_ID, pl.username AS Seller_ID
FROM Players p, Transaction t, Team te, Players pl
Where p.player_ID = t.buyer_ID
and te.Team_ID = t.Team_ID
and pl.player_ID = t.seller_ID
Order By ".$sort;
		}
		else{
			$SQL = "SELECT t.Transaction_ID, t.Value, t.QuantitySold, te.Team_Name, p.username as Buyer_ID, pl.username AS Seller_ID
FROM Players p, Transaction t, Team te, Players pl
Where p.player_ID = t.buyer_ID
and te.Team_ID = t.Team_ID
and pl.player_ID = t.seller_ID
and (Team_Name LIKE '%".$searchTerm."%'
or p.username LIKE '%".$searchTerm."%'
or pl.username LIKE '%".$searchTerm."%')
Order By ".$sort;
		}
	$allValues = mysql_query($SQL, $linkID);
	if (!$allValues) {
		echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
		exit;
	}
	echo "<TABLE BORDER=1 CELLPADDING=8>";
	echo "<TR><TD><B>Transaction ID</B></TD><TD><B>Value</B></TD><TD><B>Quantity Sold</B></TD><TD><B>Team Name</B></TD><TD><B>Buyer ID</B></TD><TD><B>Seller ID</B></TD>";
		$totalrows = mysql_num_rows($allValues);
		for ($i=1; $i <= $totalrows; $i++){
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
			echo "<TR>";
			echo "<TD>$Transaction_ID</TD>";
			echo "<TD>\$".round($Value,2,PHP_ROUND_HALF_DOWN)."</TD>";
			echo "<TD>$QuantitySold</TD>";
			echo "<TD>$Team_Name</TD>";
			echo "<TD>$Buyer_ID</TD>";
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
