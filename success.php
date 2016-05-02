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
    <li><a class="active" href="signIn.php">Sign In</a></li>
    <li><a href="signUp.php">Sign Up</a></li>
    <li><a href="logOut.php">Log Out</a></li>
    <li><a href="league.php">League Admin</a></li>
</ul>


<div id="section">
<h2><font color=#0099cc>Check below for sign in success:</font></h2><br />

<?php
	session_start();
	$usernameInput = addSlashes($_POST["username"]);
	$passwordInput = addSlashes($_POST["password"]);
	$confirmedBool = false;

	$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
	mysql_select_db("jgavin", $linkID);

	$SQL = "SELECT Confirmed FROM Players WHERE Username = '".$usernameInput."'";
	$allValues = mysql_query($SQL, $linkID);
	if (mysql_affected_rows() == 0) {
		echo "Log in request not handled correctly. " . mysql_error();
		exit;
	}

	$totalrows = mysql_num_rows($allValues);
		for ($i=1; $i <= $totalrows; $i++){
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
			$confirmedPlayer = $Confirmed;
			if($confirmedPlayer == 1){
				$confirmedBool = true;
				break;

			}
		}

		if($confirmedBool){



	$SQL = "SELECT Player_ID, password
		FROM Players
		WHERE Username = '".$usernameInput."'
		Order By Player_ID";
	$allValues = mysql_query($SQL, $linkID);
	if (mysql_affected_rows() == 0) {
		echo "Log in request not handled correctly. " . mysql_error();
		exit;
	}

	$totalrows = mysql_num_rows($allValues);
		for ($i=1; $i <= $totalrows; $i++){
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
			$currentUUID = $Player_ID;
			$currentPassword = $password;
			if($passwordInput == $password){
				$_SESSION['uuid'] = $currentUUID;
				echo "You have been successfully signed in!";
				break;

			}
		}


	$SQL = "SELECT Admin FROM Players
	WHERE Player_ID = '".$_SESSION['uuid']."'";

	$allValues = mysql_query($SQL, $linkID);
	if (mysql_affected_rows() == 0) {
		echo "Log in request not handled correctly. " . mysql_error();
		exit;
	}

	$totalrows = mysql_num_rows($allValues);
		for ($i=1; $i <= $totalrows; $i++){
			$thisValue = mysql_fetch_assoc($allValues);
			extract($thisValue);
			$adminStatus = $Admin;
			$_SESSION['admin'] = $adminStatus;
		}

		}
		else{
			echo "Your account is awaiting confirmation.";
		}

	mysql_close($linkID);
?>
</div>



<div id="footer">
2016, Charles Fiedler, Brooks Musangu, Jake Gavin
</div>


</body>
</html>
