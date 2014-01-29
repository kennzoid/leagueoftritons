<?php
  ini_set("max_execution_time", 6000);
  include('connection.php');
  include('updateKeys.php');

  $limitCounter = 0;
  $instanceCounter = 0;
  
  // UPDATE THE MOST PLAYED CHAMP FOR EVERY PLAYER
  
  // Get list of players from SQL.
  $result = $mysqli->query("SELECT * FROM `players`");
  
  // For each entry, run the function updateChamps	 
  while($row = $result->fetch_array())
  {
	updateChamp($row['summoner_id'], $mysqli, $instances[$instanceCounter]);
	
	// Rate limiting
	$limitCounter++;
	if($limitCounter == 10)
	{
	  $instanceCounter++;
	  if($instanceCounter >= count($instances))
	  { 
	    $instanceCounter = 0; 
		sleep(2);
	  }
	  $limitCounter = 0;	
	}
  }
  
  mysqli_close($mysqli);
  
  // This function calls the Riot API and updates their champ
  function updateChamp($summoner_id, $mysqli, $instance)
  {
    $mostGames = 0;
	$mostChamp = "0";
	$currTotal = 0;
	
	$rankStats = json_decode($instance->getStats($summoner_id,'ranked'), true);
	
	if(!(is_numeric($rankStats)))
	{
	  foreach($rankStats["champions"] as $value)
      {
		if($value["name"] != "Combined")
		{
		  $currTotal = $value["stats"]["totalSessionsWon"]+$value["stats"]["totalSessionsLost"];
		  if($currTotal > $mostGames)
		  {
			$mostGames = $currTotal;
			$mostChamp = $value["name"];
		  }
		}
	  }
	}
	
	else
    {
	  // if a 404 is returned, it's probably because they have no solo queue rating
      $mostChamp = "0";
	  
	  // if it's 429 or 503, it's the rate limit or API status, so we just don't update
	  if($rankStats == 429 || $rankStats == 503) $mostChamp = -1;
    }
	
	if($mostChamp != -1)
	{
 	  $result = mysqli_query($mysqli, "UPDATE `players` SET `champ`='".$mostChamp."' WHERE `summoner_id`=".$summoner_id);
	}
  }
?>
