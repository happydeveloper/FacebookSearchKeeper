<?php

if (($loader = require_once __DIR__ . './../vendor/autoload.php') == null)  {
  die('Vendor directory not found, Please run composer install.');
}

class Ot_stream
{
	//페이스북 객체 
	public $facebook;

	public $user;

	public $fql;

	public $result;

	public function __construct() 
	{

	$this->facebook = new Facebook(array(
		'appId' => '541305629256667',
		'secret' => '95492b0183156cd27d69b1308980ef26',
		'cookie' => true));

	$this->user = $this->facebook->getUser();
	
		if($this->user) 
		{

			try {
				$this->user_profile = $this->facebook->api('/me');
			} catch(FacebookApiException $e) {
				error_log($e);
				$this->user = null;
			}
		}
	}

	public function getUserState(){
		if($this->user) {
			return $logoutUrl = $this->facebook->getLogoutUrl();
		} else {
			return $loginUrl = $this->facebook->getLoginUrl();
		}
	}

	public function getStream($startDate, $endDate)
	{
		$startDateType = new Datetime($startDate);
		$endDateType   = new Datetime($endDate);

		if($this->user) {
			$this->fql = "SELECT post_id, created_time, permalink, message FROM stream WHERE source_id = 174499879257223 AND created_time < ".$endDateType->format('U')." AND created_time >= ".$startDateType->format('U')." LIMIT 50";
		
		$params = array('method' => 'fql.query', 'query' => $this->fql, );
			echo "<br /> ".$this->fql;
			$this->result =  $this->facebook->api($params);
		}
	}
}