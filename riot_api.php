<?php
/*

PHP Riot API 
Kevin Ohashi (http://kevinohashi.com)
http://github.com/kevinohashi/php-riot-api


The MIT License (MIT)

Copyright (c) 2013 Kevin Ohashi

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

*/

class riotapi {
	const API_URL_1_1 = 'http://prod.api.pvp.net/api/lol/{region}/v1.1/';
	const API_URL_1_2 = 'http://prod.api.pvp.net/api/lol/{region}/v1.2/';
	const API_URL_1_3 = 'http://prod.api.pvp.net/api/lol/{region}/v1.3/';
	const API_URL_2_1 = 'http://prod.api.pvp.net/api/lol/{region}/v2.2/';
	const API_URL_2_2 = 'http://prod.api.pvp.net/api/lol/{region}/v2.2/';
	const RATE_LIMIT_MINUTES = 500;
	const RATE_LIMIT_SECONDS = 10;
	const CACHE_LIFETIME_MINUTES = 60;
	const CACHE_ENABLED = false;
	private $REGION;
	private $API_KEY;
	
	public function __construct($region, $api)
	{
		$this->REGION = $region;
		$this->API_KEY = $api;		
	}

	public function getChampion(){
		$call = 'champion';

		//add API URL to the call
		$call = self::API_URL_1_1 . $call;

		return $this->request($call);
	}

	public function getGame($id){
		$call = 'game/by-summoner/' . $id . '/recent';

		//add API URL to the call
		$call = self::API_URL_1_2 . $call;

		return $this->request($call);
	}

	public function getLeague($id){
		$call = 'league/by-summoner/' . $id;

		//add API URL to the call
		$call = self::API_URL_2_2 . $call;

		return $this->request($call);
	}

	public function getStats($id,$option='summary'){
		$call = 'stats/by-summoner/' . $id . '/' . $option . '?season=SEASON4';

		//add API URL to the call
		$call = self::API_URL_1_2 . $call;

		return $this->request($call);
	}

	public function getSummoner($id,$option=null){
		$call = 'summoner/' . $id;
		switch ($option) {
			case 'masteries':
				$call .= '/masteries';
				break;
			case 'runes':
				$call .= '/runes';
				break;
			case 'name':
				$call .= '/name';
				break;
			
			default:
				//do nothing
				break;
		}

		//add API URL to the call
		$call = self::API_URL_1_3 . $call;

		return $this->request($call);
	}


	public function getSummonerByName($name){


		//sanitize name a bit - this will break weird characters
		$name = preg_replace("/[^a-zA-Z0-9 ]+/", "", $name);
		$call = 'summoner/by-name/' . $name;

		//add API URL to the call
		$call = self::API_URL_1_2 . $call;

		return $this->request($call);
	}


	public function getTeam($id){
		$call = 'team/by-summoner/' . $id;

		//add API URL to the call
		$call = self::API_URL_2_1 . $call;

		return $this->request($call);
	}

	private function request($call){

		//probably should put rate limiting stuff here


		//format the full URL
		$url = $this->format_url($call);

		//caching
		if(self::CACHE_ENABLED){
			$cacheFile = 'cache/' . md5($url);

		    if (file_exists($cacheFile)) {
		        $fh = fopen($cacheFile, 'r');
		        $cacheTime = trim(fgets($fh));

		        // if data was cached recently, return cached data
		        if ($cacheTime > strtotime('-'. CACHE_LIFETIME_MINUTES . ' minutes')) {
		            return fread($fh,filesize($cacheFile));
		        }

		        // else delete cache file
		        fclose($fh);
		        unlink($cacheFile);
		    }
		}

		//call the API and return the result
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if(self::CACHE_ENABLED){
			//create cache file
		    $fh = fopen($cacheFile, 'w');
		    fwrite($fh, time() . "\n");
		    fwrite($fh, $result);
		    fclose($fh);
		}

		if($http_status == 400 || $http_status == 404 || $http_status == 500 || $http_status == 429 || $http_status == 503)
		{
		        echo "".$http_status." ";
		        flush();
			return $http_status; 
		}
		
		else
		{
			return $result;
		}

	}

	//creates a full URL you can query on the API
	private function format_url($call){
      if(strpos($call, '?') != FALSE)
	  {
		return str_replace('{region}', $this->REGION, $call) . '&api_key=' . $this->API_KEY;
	  }
	  
	  else
	  {
		return str_replace('{region}', $this->REGION, $call) . '?api_key=' . $this->API_KEY;
	  }
	}
}




?>
