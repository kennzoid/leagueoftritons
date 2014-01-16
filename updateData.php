<?php
  ini_set("max_execution_time", 6000);
  include('connection.php');
  include('updateKeys.php');

  $limitCounter = 0;
  $instanceCounter = 0;

  // UPDATE THE RANKS FOR EVERY PLAYER
  
  // Get list of players from SQL.
  $result = $mysqli->query("SELECT * FROM `players`");

  // For each entry, run the function updateUser	 
  while($row = $result->fetch_array())
  {
	updateUser($row['summoner_id'], $mysqli, $instances[$instanceCounter]);
	
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
  
  
  // This function calls the Riot API and updates their rank
  function updateUser($summoner_id, $mysqli, $instance)
  {
	$rankStats = json_decode($instance->getLeague($summoner_id), true);
	$rankScore = -1;

    if(!(is_numeric($rankStats)))
	{
	  if(array_key_exists($summoner_id, $rankStats))
      foreach($rankStats[$summoner_id]["entries"] as $value)
      {
	// If a valid JSON with Solo Queue info is found, their score is calculated 
        if($value["playerOrTeamId"] == $summoner_id)
		{
		  $rankScore = 0;
		  
	  // Tiers are the first digit, from 1 - 6
      	  switch($value["tier"])
          {
            case "BRONZE":
			$rankScore = $rankScore + 10000;
			break;
			case "SILVER":
			$rankScore = $rankScore + 20000;
			break;
			case "GOLD":
			$rankScore = $rankScore + 30000;
			break;
			case "PLATINUM":
			$rankScore = $rankScore + 40000;
			break;
			case "DIAMOND":
			$rankScore = $rankScore + 50000;
			break;
			case "CHALLENGER":
			$rankScore = $rankScore + 60000;
			break;
          }

          // Divisions are the second digit, from 1 - 5
          switch($value["rank"])
          {
            case "V":
			$rankScore = $rankScore + 1000;
			break;
			case "IV":
			$rankScore = $rankScore + 2000;
			break;
			case "III":
			$rankScore = $rankScore + 3000;
			break;
			case "II":
			$rankScore = $rankScore + 4000;
			break;
			case "I":
			$rankScore = $rankScore + 5000;
			break;
          }

          // League points are the last 3 digits
          $rankScore = $rankScore + $value["leaguePoints"];
	    }
	  }
	  
	  else
	  {
		$rankScore = 0;  
	  }
	}

    else
    {
	  // if a 404 is returned, it's probably because they have no solo queue rating
          $rankScore = 0;
	  
	  // if it's 429 or 503, it's the rate limit or API status, so we just don't update
	  if($rankStats == 429 || $rankStats == 503) $rankScore = -1;
    }

    if($rankScore != -1)
	{
 	  $result = mysqli_query($mysqli, "UPDATE `players` SET `rank`=".$rankScore." WHERE `summoner_id`=".$summoner_id);
	}
  }
?>
