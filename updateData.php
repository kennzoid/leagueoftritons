<?php
  ini_set("max_execution_time", 6000);
  include('connection.php');
  include('updateKeys.php');

  $limitCounter = 0;
  $instanceCounter = 0;

  // update just the data
  $result = $mysqli->query("SELECT * FROM `players`");
	 
  while($row = $result->fetch_array())
  {
	updateUser($row['summoner_id'], $mysqli, $instances[$instanceCounter]);
	
	$limitCounter++;
	if($limitCounter == 10)
	{
	  $instanceCounter++;
	  if($instanceCounter >= count($instances))
	  { 
	    $instanceCounter = 0; 
		//sleep(5);
	  }
	  $limitCounter = 0;	
	}
  }
  
  mysqli_close($mysqli);
  
  function updateUser($summoner_id, $mysqli, $instance)
  {
	$rankStats = json_decode($instance->getLeague($summoner_id), true);
	$rankScore = -1;

    if(!(is_numeric($rankStats)))
	{
      foreach($rankStats[$summoner_id]["entries"] as $value)
      {
        if($value["playerOrTeamId"] == $summoner_id)
		{
		  $rankScore = 0;
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

          $rankScore = $rankScore + $value["leaguePoints"];
	    }
	  }
	}

    else
    {
      $rankScore = 0;
    }

    if($rankScore != -1)
	{
 	  $result = mysqli_query($mysqli, "UPDATE `players` SET `rank`=".$rankScore." WHERE `summoner_id`=".$summoner_id);
	}
  }
?>
