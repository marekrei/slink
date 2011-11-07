<?php
class Config {
	
	static $conf_int = array("items_per_page", "short_url_length", "default_view");
	static $conf_string = array("url_prefix", "time_format", "short_url_allowed_characters", "sequential_short_url");
	static $conf_boolean = array("allow_links", "allow_files", "allow_link_passwords", "allow_mirror", "create_mirror_default", "debug", "enable_thumbnails", "short_url_random", "always_mirror", "allow_tags", "allow_sharing");
	
	public static function init() {
		global $_CONFIG;
		$array = DB::getConfig();
		foreach($array as $key => $value)
		{
			$_CONFIG[$key] = $value;
		}
		
		if(Config::get("timezone") != null && strlen(Config::get("timezone")) > 0)
			ini_set('date.timezone', Config::get("timezone"));
	}
	
	public static function get($key) {
		global $_CONFIG;
		if(isset($_CONFIG) && isset($_CONFIG[$key]))
		{
			if(in_array($key, self::$conf_int))
				return intval($_CONFIG[$key]);
			else if(in_array($key, self::$conf_boolean))
			{
				if($_CONFIG[$key] != null && intval($_CONFIG[$key]) > 0)
					return true;
				else
					return false;
			}
			else
				return $_CONFIG[$key];
		}
		else
			return null;
	}
	
	public static function getString($key)
	{
		global $_CONFIG;
		return $_CONFIG[$key];
	}
	
	public static function set($key, $value)
	{
		global $_CONFIG;
		
		if(in_array($key, self::$conf_int) || in_array($key, self::$conf_string))
			$_CONFIG[$key] = "".$value;
		else if(in_array($key, self::$conf_boolean))
		{
			if($value == 1 || $value == true)
				$_CONFIG[$key] = "1";
			else
				$_CONFIG[$key] = "0";
		}
	}
	
	public static function isTrue($key) {
		$val = self::get($key);
		if($val == null)
			return false;
		if(intval($val) > 0)
			return true;
		return false;
	}
	
	public static function checkForUpdate()
	{
		$data = Mirror::getFileContents(self::get("update_check_url"));
		if($data == null || strlen($data) == 0)
			$response = "Unable to check for updates";
		else
		{
			$data_r = explode("\n", $data);
			$new_version = floatval(trim($data_r[0]));
			$current_version = self::get("version");
			if($new_version <= $current_version)
				$response = "You are using the latest version";
			else if($new_version > $current_version && count($data_r) > 1 && strlen($data_r[1]) > 0)
				$response = "New version ".trim($data_r[0])." available. <a href=\"".$data_r[1]."\">Click here</a>.";
			else if($new_version > $current_version)
				$response = "New version ".trim($data_r[0])." available";
			else
				$response = "Unable to check for updates";
		}
		
		return $response;		
	}
	
	
}


?>
