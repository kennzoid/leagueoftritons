<?php
  include('connection.php');
  $return = "";
  
  if(!empty($_GET))
  {
	$searchSummoner = $_GET["summonerName"];
    $searchSummoner = strtolower($searchSummoner);
    $searchSummoner = str_replace(' ', '', $searchSummoner);
	$sql = "SELECT * FROM `players` WHERE `summoner_name` LIKE '".$searchSummoner."%' LIMIT 0,10";
	$results = $mysqli->query($sql);
	$counter = 0;
	
	$data = array("query"=>$searchSummoner, "suggestions"=>array());
	
	if($results != false)
	{
	  while($result = $results->fetch_array())
	  {
		$data["suggestions"][] = $result["true_name"];  
	  }
	}
	
	else
	{
	  	$data["suggestions"][] = "NO RESULT";
	}
  }
  
  echo json_encode($data);
?>
