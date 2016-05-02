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
    <li><a href="signIn.php">Sign In</a></li>
    <li><a class="active" href="signUp.php">Sign Up</a></li>
    <li><a href="logOut.php">Log Out</a></li>
    <li><a href="league.php">League Admin</a></li>
</ul>
<?php
	session_start();
	$username;
	$first;
	$last;
	$email;
	$pass1;
	$pass2;
	$correctEntries = 0;
		if($_POST["username"] != ""){
		$username = addSlashes($_POST["username"]);
		$correctEntries++;
		}
		else{
			echo "PLEASE ENTER A VALID USERNAME_____";
		}
		if($_POST["first"] != ""){
			$first = addSlashes($_POST["first"]);
			$correctEntries++;
		}
		else{
			echo "PLEASE ENTER A VALID FIRST NAME_____";
		}
		if($_POST["last"] != ""){
			$last = addSlashes($_POST["last"]);
			$correctEntries++;
		}
		else{
			echo "PLEASE ENTER A VALID LAST NAME_____";
		}
		if($_POST["email"] != ""){
			$email = addSlashes($_POST["email"]);
			$correctEntries++;
		}
		else{
			echo "PLEASE ENTER A VALID EMAIL_____";
		}
		if($_POST["pass1"] != ""){
			$pass1 = addSlashes($_POST["pass1"]);
			$correctEntries++;
		}
		if($_POST["pass2"] != ""){
			$pass2 = addSlashes($_POST["pass2"]);
			$correctEntries++;
		}
		if($pass1 != $pass2){
			echo "PASSWORDS MUST MATCH_____";
		}
		else{
			$correctEntries++;
		}

	if($correctEntries == 7){
		$linkID = mysql_connect("localhost","jgavin","Furmanlax17");
		mysql_select_db("jgavin", $linkID);

		$SQL = "INSERT INTO Players (Player_ID, Username, First_Name, Last_Name, Email_Address, password,Account_Balance,Available_Balance,Admin,Confirmed) VALUES (NULL, '".$username."', '".$first."', '".$last."', '".$email."', '".$pass1."',100,100,0,0);";

		$allValues = mysql_query($SQL, $linkID);
		if (mysql_affected_rows() == 0) {
			echo "Sign up request not handled correctly. " . mysql_error();
			exit;
		}
		else{
			echo "ACCOUNT INFORMATION RECIEVED AND AWAITING ADMINISTRATOR CONFIRMATION.";
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
