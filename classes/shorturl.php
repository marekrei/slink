<?php
class ShortUrl {
	public static function isValid($str) {
		/*if(ctype_alnum($str))
			return true;
		return false;*/
		
		$allowed_characters = Config::get("short_url_allowed_characters");
		$allowed_character_array = array();
		for($i = 0; $i < strlen($allowed_characters); $i++)
			$allowed_character_array[] = $allowed_characters[$i];
		
		for($i = 0; $i < strlen($str); $i++)
		{
			if(!in_array($str[$i],$allowed_character_array))
				return false;
		}
		return true;
	}
	
	public static function incrementString($string, $pos, $characters)
	{
		Messenger::addDebug("Called incrementString() with parameters: ".$string." ".$pos." ");
		if($pos < 0)
			return $characters[0].$string;
		$num = array_search($string[$pos], $characters);
		if($num + 1 < count($characters))
			$string[$pos] = $characters[$num+1];
		else 
		{
			$string[$pos] = $characters[0];
			$string = self::incrementString($string, $pos-1, $characters);
		}
		return $string;
	}
	
	public static function generateSequential() {
		$length = intval(Config::get("short_url_length")) >= 1?intval(Config::get("short_url_length")):1;
		$allowed_characters = Config::get("short_url_allowed_characters");
		$latest_short_url = Config::get("sequential_short_url");
		
		$char_array = array();
		for($i = 0; $i < strlen($allowed_characters); $i++)
			$char_array[] = $allowed_characters[$i];
		
		
		if($latest_short_url == null || strlen($latest_short_url) == 0)
		{
			$new_url = "";
			for($i = 0; $i < $length; $i++)
				$new_url .= $allowed_characters[0];
		}
		else
			$new_url = self::incrementString($latest_short_url, strlen($latest_short_url)-1, $char_array);
		
		Messenger::addDebug("Trying to use short url: ". $new_url);
		
		$count = 0;
		while(!self::isValid($new_url) || !self::isAvailable($new_url)){
			$new_url = self::incrementString($new_url, strlen($new_url)-1, $char_array);
			Messenger::addDebug("The url was not available, generated a new one: " . $new_url);
		}
		
		Config::set("sequential_short_url", $new_url);
		DB::saveConfig();
		return $new_url;
	}
	
	public static function generateRandom() {
		$length = intval(Config::get("short_url_length"));
		if($length <= 0)
			$length = 8;
			
		$counter = 0;
		while(true)
		{
			$shortUrl = self::generateString($length);
			if(DB::isShortUrlAvailable($shortUrl))
				return $shortUrl;
			if($counter++ > 100)
				return null;
		}
	}
	
	public static function generate() {
		if(Config::get("short_url_random"))
			return self::generateRandom();
		else
			return self::generateSequential();
	}
	
	public static function generateString($length)
	{
	    $characters = Config::get("short_url_allowed_characters");
	    $string = "";
	
	    for ($i = 0; $i < $length; $i++) {
	        $string .= $characters[mt_rand(0, strlen($characters)-1)];
	    }
	
	    return $string;
	}
	
	public static function isAvailable($shortUrl)
	{
		return DB::isShortUrlAvailable($shortUrl);
	}
}