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
  	<li><a href="portfolio.php">Portfolio</a></li>
    <li><a href="signIn.php">Sign In</a></li>
    <li><a href="signUp.php">Sign Up</a></li>
    <li><a href="logOut.php">Log Out</a></li>
    <li><a class="active" href="league.php">League Admin</a></li>
    <li><a href="userAdmin.php">User Administration</a></li>
    </ul>
    
<div id="section">

<?php
	session_start();
	$adminStatus = $_SESSION['admin'];
	$same = false;
	$same2 = false;
	$userID = $_SESSION['uuid'];
	//Don't forget to check for admin privleges first!
	if($playerName1==$playerName2){
		$same = true;
	}
	if($playerName3==$playerName4){
		$same2 = true;
	}
	
	if($adminStatus == '1'){
		winnerLooserForm();
		setIPOForm();
		possSetIPO();
		possUpdateIPO();
		displayTeams();
	}
	else{
		echo "You are not an admin, and do not have administrator privileges.";
	}
	
	function displayTeams(){
		$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
		mysql_select_db("jgavin", $linkID);
		$SQL = "SELECT Team_Name,IPO_Value,Win,Losses FROM 
		Team;";
		$allValues = mysql_query($SQL, $linkID);
		if (!$allValues) {
			echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
			exit;
		}
		echo "<TABLE BORDER=1 CELLPADDING=8>";
		echo "<TR><TD><B>Team Name</B></TD><TD><B>IPO</B></TD><TD><B>Wins</B></TD>	
		<TD><B>Losses</B></TD>";
		$totalrows = mysql_num_rows($allValues);
		for($i=1; $i <= $totalrows; $i++){
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
			echo "<TR>";
			echo "<TD>$Team_Name</TD>";
			echo "<TD>\$".round($IPO_Value,2,PHP_ROUND_HALF_DOWN)."</TD>";
			echo "<TD>$Win</TD>";
			echo "<TD>$Losses</TD>";
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
			mysql_close($linkID);
			$SQL = "DELETE FROM Players WHERE Player_ID = '".$removeID."'";
			sqlAction($SQL);
		}
		if($same2){
			$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
			mysql_select_db("jgavin", $linkID);
			$SQL = "SELECT Player_ID FROM Players WHERE Username = '".$playerName3."'";
			$allValues = mysql_query($SQL, $linkID);
			if (!$allValues) {
				echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
				exit;
			}
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
			$confirmID = $Player_ID;
			mysql_close($linkID);
			$SQL = "UPDATE Players SET Confirmed = 1 WHERE Player_ID = '".$confirmID."'";
			sqlAction($SQL);
		}
	}
	function winnerLooserForm(){
		$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
		mysql_select_db("jgavin", $linkID);
		$SQL = "SELECT Team_Name FROM Team;";
		$allValues = mysql_query($SQL, $linkID);
		if (!$allValues) {
			echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
			exit;
		}
		$totalrows = mysql_num_rows($allValues);
		$select= '<form action="league.php" method="post">
		<label for="winningTeam"><font color=#0099cc>Winning team:</font></label>
		<select name="winningTeam" id="winningTeam" title="winningTeam">';
		for($i = 1;$i<=$totalrows;$i++){
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
      		$select.='<option value="'.$Team_Name.'">'.$Team_Name.'</option>';
 		}
		$select.='</select>';
		
		$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
		mysql_select_db("jgavin", $linkID);
		$SQL = "SELECT Team_Name FROM Team;";
		$allValues = mysql_query($SQL, $linkID);
		if (!$allValues) {
			echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
			exit;
		}
		$select2= '<form action="league.php" method="post">
		<label for="loosingTeam"><font color=#0099cc>Losing team:</font></label>
		<select name="loosingTeam" id="loosingTeam" title="loosingTeam">';
		for($i=1; $i <= $totalrows; $i++){
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
      		$select2.='<option value="'.$Team_Name.'">'.$Team_Name.'</option>';
 		}
		$select2.='</select><input type="submit" value="Report Match">
    	</form>';
		$totalForm = $select.$select2;
		echo $totalForm;
		mysql_close($linkID);
	}
	
	function possUpdateIPO(){
		$wTeam = ''.$_POST["winningTeam"];
		$lTeam = ''.$_POST["loosingTeam"];
		if($wTeam!=$lTeam){
			$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
			mysql_select_db("jgavin", $linkID);
			//get the IPO value for the loosing team
			$SQL = "SELECT IPO_Value FROM Team WHERE Team_Name = '".$lTeam."'";
			$allValues = mysql_query($SQL, $linkID);
			if (!$allValues) {
				echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
				exit;
			}
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
			$lValue = $IPO_Value;
			$deltaValue = $lValue*0.1;
			$newLValue = round($lValue - $deltaValue,2,PHP_ROUND_HALF_DOWN);
			//get the IPO value for the winning team
			$SQL = "SELECT IPO_Value FROM Team WHERE Team_Name = '".$wTeam."'";
			$allValues = mysql_query($SQL, $linkID);
			if (!$allValues) {
				echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
				exit;
			}
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
			$newWValue = $IPO_Value;
			$newWValue = round($newWValue + $deltaValue,2,PHP_ROUND_HALF_UP);
			mysql_close($linkID);
			//update the loosing team's IPO
			$SQL = "UPDATE Team SET IPO_Value = ".$newLValue." WHERE Team_Name = '".$lTeam.
			"'";
			sqlAction($SQL);
			//update the winning team's IPO
			$SQL = "UPDATE Team SET IPO_Value = ".$newWValue." WHERE Team_Name = '".$wTeam.
			"'";
			sqlAction($SQL);
			
			
			//get the winning team's win record
			$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
			mysql_select_db("jgavin", $linkID);
			$SQL = "SELECT Win FROM Team WHERE Team_Name = '".$wTeam."'";
			$allValues = mysql_query($SQL, $linkID);
			if (!$allValues) {
				echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
				exit;
			}
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
			$winValue = $Win + 1;
			mysql_close($linkID);
			//update the winning teams win record
			$SQL = "UPDATE Team SET Win = ".$winValue." WHERE Team_Name = '".$wTeam.
			"'";
			sqlAction($SQL);
			//get the loosing team's win record
			$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
			mysql_select_db("jgavin", $linkID);
			$SQL = "SELECT Losses FROM Team WHERE Team_Name = '".$lTeam."'";
			$allValues = mysql_query($SQL, $linkID);
			if (!$allValues) {
				echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
				exit;
			}
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
			$lossValue = $Losses + 1;
			mysql_close($linkID);
			//update the loosing teams loss record
			$SQL = "UPDATE Team SET Losses = ".$lossValue." WHERE Team_Name = '".$lTeam.
			"'";
			sqlAction($SQL);
		}
	}
	
	function setIPOForm(){
		$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
		mysql_select_db("jgavin", $linkID);
		$SQL = "SELECT Team_Name FROM Team;";
		$allValues = mysql_query($SQL, $linkID);
		if (!$allValues) {
			echo "Could not successfully run query ($SQL) from DB: " . mysql_error();
			exit;
		}
		$totalrows = mysql_num_rows($allValues);
		$select= '<form action="league.php" method="post">
		<label for="teamIPO"><font color=#0099cc>Team Set IPO:</font></label>
		<select name="teamIPO" id="teamIPO" title="teamIPO">';
		for($i = 1;$i<=$totalrows;$i++){
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
      		$select.='<option value="'.$Team_Name.'">'.$Team_Name.'</option>';
 		}
		$select.='</select>
		<font color=#0099cc>New IPO Value:</font>
    	<input type="text" name="newIPO" maxlength="7" size="7">
		<input type="submit" value="Set IPO">
    	</form>';
		echo $select;
		mysql_close($linkID);
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
	
	function possSetIPO(){
		$team = ''.$_POST["teamIPO"];
		$newIPO = round($_POST["newIPO"],2,PHP_ROUND_HALF_DOWN);
		if($team!='' && $newIPO>=0){
			$SQL = "UPDATE Team SET IPO_Value = ".$newIPO." WHERE Team_Name = '".$team."'";
			sqlAction($SQL);
		}
	}
?>
</div>

<div id="footer">
2016, Charles Fiedler, Brooks Musangu, Jake Gavin
</div>


</body>
</html>
