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
    <li><a class="active" href="signUp.php">Sign Up</a></li>
    <li><a href="logOut.php">Log Out</a></li>
    <li><a href="league.php">League Admin</a></li>
</ul>


<div id="section">
<h2><font color=#0099cc>Please fill out the information below to create your account:</font></h2><br />
<form action="signingUp.php" method="post">
	<p><font color=#0099cc>Desired Username:</font>
	  <input type="text" name="username" maxlength="35" size="35">
	</p>
    <p><font color=#0099cc>First Name:</font>
	  <input type="text" name="first" maxlength="35" size="35">
	</p>
    <p><font color=#0099cc>Last Name:</font>
	  <input type="text" name="last" maxlength="35" size="35">
	</p>
	<p>	  <font color=#0099cc>Email Address:</font>
	  <input type="text" name="email" maxlength="35" size="35">
    <p><font color=#0099cc>Password:</font>
	  <input type="text" name="pass1" maxlength="35" size="35">
	</p>
    <p><font color=#0099cc>Repeat Password:</font>
	  <input type="text" name="pass2" maxlength="35" size="35">
	</p>
    <p><font color=#ff0000>Note: Please wait for the admin to confirm your sign up request. You will recieve an email once the admin has granted you access.</font>
    </p>
	  <input type="submit" value="Create Account"><br>
    </form>
</div>



<div id="footer">
2016, Charles Fiedler, Brooks Musangu, Jake Gavin
</div>


</body>
</html>
