<?php
/*
	Author: David I. Martinez
	Email: dhavhid@gmail.com
	- This is a layer between instagram API and a bussines layer in an app.
*/
// It is important to set the timezone when working with dates.
date_default_timezone_set('America/New_York');


class instagram{

	private $instagram;	
	private $link;
	
	// Constructor. It sets the global variables needed to use the instagram API
	public function instagram(){
		// Please replace all of this variables with your own.
		$this->instagram = array(
			'access_token'=>'248652717.9a1df48.702ee88c30e4',
			'client_id'=>'9a1df489874241c32a',
			'client_secret'=>'d432cb333d7df7ebb1dc4',
			'redirect_uri'=>'http://www.example.com/'
		);
	}
	
	// Gets a url ready to look media by username
	function search_url($username, $count = 1){
		$this->instagram['user_search'] = "https://api.instagram.com/v1/users/search?q={$username}&count={$count}&access_token=" . $this->instagram['access_token'];
	}
	
	/*
		Gets a url to look for a recent media based on a user id. You can get the user id by calling first the search user method.
		The media is gotten from one day old on.
	*/
	function user_url($user_id, $count = 10){
		$d = date('Y-m-d',mktime(0,0,0,date('m'),date('d')-1,date('Y')));
		$date = new DateTime($d);
		$mintimestamp = $date->getTimestamp();
		$this->instagram['user_media'] = "https://api.instagram.com/v1/users/{$user_id}/media/recent/?count={$count}&access_token=" . $this->instagram['access_token'] . "&min_timestamp=" . $mintimestamp;
	}
	
	
	// Returns a json object with the full profile of matching users based on the username, you can set the number of results returned by the method.
	function search_user($username, $count = 1){
		$this->search_url($username,$count);
		$hand = curl_init();
		curl_setopt($hand,CURLOPT_URL,$this->instagram['user_search']);
		curl_setopt($hand, CURLOPT_RETURNTRANSFER, true);
		$data=curl_exec($hand);
		$info = curl_getinfo($hand);
		curl_close($hand);
		if ($info['http_code'] == 200) {
		    $entries = json_decode($data, true);
		}
		if( !empty($entries) && is_array($entries['data']) && count($entries['data']) >= $count )return reset($entries['data']);
		else return FALSE;
	}
	
	// Returns a JSON object with the lastest media from a instagram user.
	function get_recentmedia($user_id, $count = 10){
		$this->user_url($user_id, $count);
		$hand = curl_init();
		curl_setopt($hand,CURLOPT_URL,$this->instagram['user_media']);
		curl_setopt($hand, CURLOPT_RETURNTRANSFER, true);
		$data=curl_exec($hand);
		$info = curl_getinfo($hand);
		curl_close($hand);
		if ($info['http_code'] == 200) {
		    $entries = json_decode($data, true);
		}
		if( !empty($entries) && is_array($entries['data']) )return $entries['data'];
		else return FALSE;
	}
}
?>