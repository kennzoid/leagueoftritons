<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>League of Tritons Rankings</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
  <div class="container">
  	<div class="header">
    	<p></p>  
  	<!-- end .header --></div>
  	<div class="content">
      <div class="topLinks">
        <a href="index.php" class="topLink">home /</a>
        <a href="signup.php" class="topLink">join /</a>
        <a href="" class="topLink">github /</a>
        <a href="about.php" class="topLink">about /</a>
        <a href="help.php" class="topLink">help</a>        
      </div>
    
      <form name="input" action="index.php" method="get">
  	    <input name="name" type="text" class="searchbar" value="" maxlength="16" />
  	    <input type="submit" id="searchButton" value=""/>
      </form>
    
      <table border="1" cellspacing="0" cellpadding="0" class="ladderEntries">
<?php
  include('connection.php');
  
  $color = "#4a4a4a";
  $selectedSummoner = " ";
  $okgo = true;
  $midPage = 1;
  $numPlayersArray = $mysqli->query("SELECT COUNT(*) FROM `players`")->fetch_array();
  $numPlayers = $numPlayersArray[0];
  $numPages = ceil($numPlayers/10);
  $printedCount = 0;
  
  echo "<div class=\"numPlayers\">".$numPlayers;
  
  if(empty($_GET))
  {
    $ordered = $mysqli->query("SELECT * FROM `players` ORDER BY `place` ASC LIMIT 10");
  }
  
  else if(!empty($_GET["name"]))
  {
	$searchSummoner = $_GET["name"];
	$selectedSummoner = strtolower($searchSummoner);
	$selectedSummoner = str_replace(' ', '', $selectedSummoner);
	$selectedRow = $mysqli->query("SELECT * FROM `players` WHERE `summoner_name` =\"".$selectedSummoner."\"");
	
	// CHECK IF SUMMONER IS VALID if(mysqli_num_rows($selectedSummoner) == 0)
	if(mysqli_num_rows($selectedRow) == 0)
	{
	  $okgo = false;
	  echo "<tr>";
	  echo "<td width=\"436\" class=\"summonerText\">Summoner not found.</td>";
	  echo "</td>";
	  echo "</tr>";
	  
	  echo "<tr>";
	  echo "<td width=\"436\" class=\"summonerText\">Go here to add a UCSD summoner to the ladder.</td>";
	  echo "</td>";
	  echo "</tr>";
	}
	
	else
	{
	  $selectedArray = $selectedRow->fetch_array();
	  $selectedIndex = $selectedArray["index"];
	  $midPage = floor($selectedIndex/10)+1;
      $ordered = $mysqli->query("SELECT * FROM `players` ORDER BY `index` ASC LIMIT 10 OFFSET ".floor($selectedIndex/10)*10);
	}
  }
  
  else if(!empty($_GET["page"]))
  {
    $midPage = $_GET["page"];
    $ordered = $mysqli->query("SELECT * FROM `players` ORDER BY `index` ASC LIMIT 10 OFFSET ".(($midPage-1)*10));
  }
  
  if($okgo)
  while($entry = $ordered->fetch_array())
  {
	$rankText = $entry['place'];
	$summonerText = $entry['summoner_name'];
	
	if($selectedSummoner == $summonerText)
	{
	  $color = "red";	
	}
	
	else
	{
	  $color = "#4a4a4a";	
	}
  
	if($entry['rank'] != 0 && $entry['rank'] != -1)
	{
	  switch(substr($entry['rank'], 0, 1))
	  {
		case 1:
		$tierText = "Bronze";
		break;
		case 2:
		$tierText = "Silver";
		break;
		case 3:
		$tierText = "Gold";
		break;
		case 4:
		$tierText = "Platinum";
		break; 
		case 5:
		$tierText = "Diamond";
		break;
		case 6:
		$tierText = "Challenger";
		break;
	  }
	  
	  switch(substr($entry['rank'], 1, 1))
	  {
		case 1:
		$divText = "V";
		break;
		case 2:
		$divText = "IV";
		break;
		case 3:
		$divText = "III";
		break;
		case 4:
		$divText = "II";
		break;
		case 5:
		$divText = "I";
		break;
	  }
	  
	  $lpText = substr($entry['rank'], 2, 3);
	  if($lpText != 0) 
	  {
	    $lpText = ltrim($lpText, '0');
	  }
	  
	  else
	  {
		 $lpText = 0; 
	  }
	}
	
	else
	{		
	  if($entry['rank'] == 0)
	  {
	    $divText = "RANKED";
	    $tierText = "NOT";
	    $lpText = "0";
	  }
	  
	  else
	  {
		$divText = "ERROR";
		$tierText = "YO";
		$lpText = "0";  
	  }
	}
	 
	echo "<tr class=\"ladderRow\">";
	echo "<td width=\"144\" class=\"rankText\" style=\"color:".$color."\">".$rankText."</td>";
	echo "<td width=\"436\" class=\"summonerText\" style=\"color:".$color."\">".$summonerText."</td>";
	echo "<td width=\"207\" class=\"leagueText\" style=\"color:".$color."\">";
	echo "<p class=\"tierText\">".$tierText." ".$divText."</p>";
	echo "<p class=\"divText\">".$lpText." League Points</p>";
	if($printedCount < 9) echo "<img src=\"images/divider.png\" class=\"divider\"/>";
	echo "</td>";
	echo "</tr>";
	
	$printedCount++;
  }
 
  // fill empty rows
  for($i = $printedCount; $i<11; $i++)
  {
	echo "<tr class=\"ladderRow\">";
	echo "<td width=\"144\" class=\"rankText\" style=\"color:".$color."\"></td>";
	echo "<td width=\"436\" class=\"summonerText\" style=\"color:".$color."\"></td>";
	echo "<td width=\"207\" class=\"leagueText\" style=\"color:".$color."\">";
	echo "<p class=\"tierText\"></p>";
	echo "<p class=\"divText\"></p>";
	echo "</td>";
	echo "</tr>"; 
  }
  echo "</table>";
    
  echo "<div class=\"pageLinks\">";
  
  // prev/first
  if($midPage > 1)
  {
    echo "<a href=\"index.php?page=1\" class=\"pageLink\">first</a>";
	echo "<a href=\"index.php?page=".($midPage-1)."\" class=\"pageLink\"><</a>";
  }
  
  if(($midPage-2)>0)echo "<a href=\"index.php?page=".($midPage-2)."\" class=\"pageLink\">".($midPage-2)."</a>";
  if(($midPage-1)>0)echo "<a href=\"index.php?page=".($midPage-1)."\" class=\"pageLink\">".($midPage-1)."</a>";
  echo "<a style=\"font-weight:bold\" class=\"pageLink\">".$midPage."</a>";
  if(($midPage+1)<=$numPages)echo "<a href=\"index.php?page=".($midPage+1)."\" class=\"pageLink\">".($midPage+1)."</a>";
  if(($midPage+2)<=$numPages)echo "<a href=\"index.php?page=".($midPage+2)."\" class=\"pageLink\">".($midPage+2)."</a>";
  
  // next/last
  if($midPage < $numPages)
  {
	  echo "<a href=\"index.php?page=".($midPage+1)."\" class=\"pageLink\">></a>";
	  echo "<a href=\"index.php?page=".$numPages."\" class=\"pageLink\">last</a>";
  }
  
  echo "</div>";
?>


  	<!-- end .content --></div>
  
  	<div class="footer">
    	<p class="footText">This product is not endorsed, certified or otherwise approved in any way by Riot Games, Inc. or any of its affiliates.</p>
  	<!-- end .footer --></div>
  <!-- end .container --></div>
</body>

</html>
