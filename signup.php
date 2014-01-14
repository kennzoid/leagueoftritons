<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>League of Tritons Rankings Sign-Up</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
  <div class="container">
  	<div class="header">
    	<p></p>  
  	<!-- end .header --></div>
  	<div class="contentCopy">
      <div class="topLinksCopy">
        <a href="index.php" class="topLink">home /</a>
        <a href="signup.php" class="topLink">join /</a>
        <a href="https://github.com/kennzoid/leagueoftritons" class="topLink">github /</a>
        <a href="about.php" class="topLink">about /</a>
        <a href="help.php" class="topLink">help</a>        
      </div>
    <form name="signup" action="signup.php" method="get" class="signup">
      This is currently on an honor system until a method of verification is implemented. <br>
      Please only enter the ladder if you are a member of UCSD's League of Tritons. Thanks!<br>
      <br>
      Enter a valid Summoner name to be added to the ladder. <br>
      You will be placed at the end until the ladder updates, which is about every 15 minutes. <br><br />
  	  Summoner name:  <input name="signName" type="text" class="signUpBar" value="" maxlength="16">
  	  <input type="submit" id="submitSign" value="Submit"/>
    </form>
 
<?php
  include('connection.php');
  include('signupkey.php');
  
  $numPlayersArray = $mysqli->query("SELECT COUNT(*) FROM `players`")->fetch_array();
  $numPlayers = $numPlayersArray[0];
  
  if(empty($_GET))
  {
	  // print form here..
  }
  
  else if(!empty($_GET["signName"]))
  {
	$signName = $_GET["signName"];
    $signName = strtolower($signName);
	$signName = str_replace(' ', '', $signName);
	$summInfo = json_decode($instance2->getSummonerByName($signName), true);

	if(!(is_numeric($summInfo))) 
	{
	  $signId = $summInfo["id"];
	  $mysqli->query("INSERT IGNORE INTO `players` (`rank`, `summoner_id`, `summoner_name`, `place`, `movement`, `index`)
	                  VALUES (0, '".$signId."', '".$signName."', ".($numPlayers+1).", 0, ".$numPlayers.")");
	  if($mysqli->affected_rows == 1)
	  {
	    echo "<p class=\"signMessage\"> Added ".$signName.".</p>";
	  }
	  
	  else
	  {
		echo "<p class=\"signMessage\">User already entered.</p>";  
	  }
	}
	
	else
	{
	  echo "<p class=\"signMessage\">Invalid summoner name entered.</p>";	
	}
  }
  
  else
  {
	$rsignName = $_GET["rsignName"]; 
  }
?> 

  	<!-- end .content --></div>
  
  	<div class="footer">
    	<p class="footTextCopy">This product is not endorsed, certified or otherwise approved in any way by Riot Games, Inc. or any of its affiliates.</p>
  	<!-- end .footer --></div>
  <!-- end .container --></div>
</body>

</html>
