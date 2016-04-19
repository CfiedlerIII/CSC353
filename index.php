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
	echo "<TR><TD><B>Transaction_ID</B></TD><TD><B>Value</B></TD><TD><B>QuantitySold</B></TD><TD><B>Team_Name</B></TD><TD><B>Buyer_ID</B></TD><TD><B>Seller_ID</B></TD>";
		$totalrows = mysql_num_rows($allValues);
		for ($i=1; $i <= $totalrows; $i++){
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
			echo "<TR>";
			echo "<TD>$Transaction_ID</TD>";
			echo "<TD>\$$Value</TD>";
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
