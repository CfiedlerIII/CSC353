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
    <li><a href="league.php">League Admin</a></li>
    </ul>
    


<div id="section">
<h2><font color=#0099cc>Your Blueprints:</font></h2><br />
<form action="portfolio.php" method="post">
	<font color=#0099cc>Search:</font>
    <input type="text" name="searchTerm" maxlength="35" size="35">
    <label for="sort"><font color=#0099cc>Sort By:</font></label>
    <select name="sort" id="sort" title="sort">
      <option value="teamName">TeamName</option>
      <option value="Num_Owned">Num_Owned</option>
    </select>
      <input type="submit" value="Update">
    </form>
    
    <h3><font color=#0099cc>Blueprints to sell:</font></h3>
    
<?php
	session_start();
	$inputPart;
	$searchTerm;
	$teamSell;
	$numSell;
	$prices;
	$sellAll = false;
	$userID = $_SESSION['uuid'];
	if($_POST["teamSell"] != ""){
		$teamSell = addSlashes($_POST["teamSell"]);
	}
	if($_POST["numSell"] != ""){
		$numSell = addSlashes($_POST["numSell"]);
	}
	if($_POST["prices"] != ""){
		$prices = addSlashes($_POST["prices"]);
	}
	if($_POST["searchTerm"] == ""){
		$searchTerm = "";
	}
	else{
		$searchTerm=addSlashes($_POST["searchTerm"]);
	}
	$inputSort=$_POST["sort"];
	$sort;
	if($inputSort == "Num_Owned"){
		$sort = "Num_Owned";
	}
	else{
		$sort = "Team_Name";
	}
	
	$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
	mysql_select_db("jgavin", $linkID);
	if($userID == null){
		echo "Please sign in to view your personal Blueprints.";
	}
	else{
		$SQL = "SELECT Team_ID FROM Players_Team 
		WHERE Player_ID = ".$userID;
		$allValues = mysql_query($SQL, $linkID);
		if (!$allValues) {
			echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
			exit;
		}
		$totalrowsOverall = mysql_num_rows($allValues);
		$teamNameArray[$totalrowsOverall];
		for($i = 0;$i<$totalrowsOverall;$i++){
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
			$SQL = "SELECT Team_Name FROM Team WHERE Team_ID = ".$Team_ID;
			$allValues2 = mysql_query($SQL, $linkID);
			if (!$allValues2) {
				echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
				exit;
			}
			$thisValue2 = mysql_fetch_assoc($allValues2);
			extract($thisValue2);
			$teamNameArray[$i] = $Team_Name;
		}
		
		
		$select= '<form action="portfolio.php" method="post">
		<label for="teamSell"><font color=#0099cc>Team To Sell:</font></label>
		<select name="teamSell" id="teamSell" title="teamSell">';
		for($i = 0;$i<$totalrowsOverall;$i++){
      		$select.='<option value="'.$teamNameArray[$i].'">'.$teamNameArray[$i].'
			</option>';
 		}
		$select.='</select>
		<font color=#0099cc>Blueprints to Sell:</font>
    	<input type="text" name="numSell" maxlength="10" size="10">
     	<font color=#0099cc>Price/Blueprint:</font>
    	<input type="text" name="prices" maxlength="10" size="10">
      	<input type="submit" value="Sell Blueprints">
    	</form>';
		echo $select;
	}
		
	$SQL = "SELECT Account_Balance FROM Players WHERE Player_ID = ".$userID;
	$allValues = mysql_query($SQL, $linkID);
	if (!$allValues) {
		echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
		exit;
	}
	echo "<TABLE BORDER=1 CELLPADDING=8>";
	echo "<TR><TD><B>Current_Balance</B></TD>";
	$thisValue = mysql_fetch_assoc($allValues);
	extract($thisValue);
	echo "<TR>";
	echo "<TD>\$$Account_Balance</TD>";
	echo "</TR>";
	echo "</TABLE>";
			
	if($searchTerm==""){
		$SQL = "SELECT t.Team_Name as Team_Name, 	
		(t.IPO_Value/t.NumOfTotBlueprints)*pt.NumOfBlueprints as Value_Owned, 
		pt.NumOfBlueprints as Num_Owned, pt.Pending
		FROM Team t, Players_Team pt
		Where pt.Team_ID = t.Team_ID
		AND pt.Player_ID = ".$userID."
		Order By ".$sort;
	}
	else{
		$SQL = "SELECT t.Team_Name as Team_Name, 		 
		(t.IPO_Value/t.NumOfTotBlueprints)*pt.NumOfBlueprints as Value_Owned, 	
		pt.NumOfBlueprints as Num_Owned, pt.Pending
		FROM Team t, Players_Team pt
		Where pt.Team_ID = t.Team_ID
		AND pt.Player_ID = ".$userID."
		AND Team_Name LIKE '%".$searchTerm."%'
		Order By ".$sort;
	}
	$allValues = mysql_query($SQL, $linkID);
	if (!$allValues) {
		echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
		exit;
	}
	echo "<TABLE BORDER=1 CELLPADDING=8>";
	echo "<TR><TD><B>Team_name</B></TD><TD><B>Value_Owned</B></TD><TD><B>Num_Owned</B></TD>
	<TD><B>Notes</B></TD>";
	$totalrows = mysql_num_rows($allValues);
	for ($i=1; $i <= $totalrows; $i++){
		$thisValue = mysql_fetch_assoc($allValues);
		extract($thisValue);
		echo "<TR>";
		echo "<TD>$Team_Name</TD>";
		echo "<TD>\$$Value_Owned</TD>";
		echo "<TD>$Num_Owned</TD>";
		echo "<TD>$Pending</TD>";
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
