<?php
  include('connection.php');

  // calculate places and movement
  $ordered = $mysqli->query("SELECT * FROM `players` ORDER BY `rank` DESC");
  
  while($row = $ordered->fetch_array())
  {
	$rows[] = $row;
  }
  
  $place = 1;
  $mysqli->query("UPDATE `players` SET `index`=0, `place`=".$place.", `movement`=".-1*($place-$rows[0]['place']).
                 " WHERE `summoner_id`=".$rows[0]["summoner_id"]);
  
  for($i = 1; $i<count($rows); $i++)
  {  
	if($rows[$i]['rank'] == $rows[$i-1]['rank'])
	{
      $mysqli->query("UPDATE `players` SET `index`=".$i.", `place`=".$place.", `movement`=".-1*($place-$rows[$i]['place']).
	                 " WHERE `summoner_id`=".$rows[$i]["summoner_id"]);	
	}
	
	else
	{
	  $place = $i + 1;
	  $mysqli->query("UPDATE `players` SET `index`=".$i.", `place`=".$place.", `movement`=".-1*($place-$rows[$i]['place']). 
	                 " WHERE `summoner_id`=".$rows[$i]["summoner_id"]);	
	}
  }
  
  mysql_close($mysqli);
?>
