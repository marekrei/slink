<?php
class Messenger {
	static $good_messages = array();
	static $bad_messages = array();
	static $debug_messages = array();
	
	public static function addBad($text) {
		self::$bad_messages[] = $text;
	}
	
	public static function addGood($text) {
		self::$good_messages[] = $text;
	}
	
	public static function addDebug($text) {
		self::$debug_messages[] = $text;
	}
	
	public static function show() {
		if(count(self::$bad_messages) > 0 || count(self::$good_messages) > 0 || count(self::$debug_messages) > 0)
		{
			if(count(self::$bad_messages) > 0)
			{
				print "<ul class=\"messenger bad\">";
				foreach(self::$bad_messages as $message)
					print "<li>".$message."</li>";
				print "</ul>";
			}
			
			if(count(self::$good_messages) > 0)
			{
				print "<ul class=\"messenger good\">";
				foreach(self::$good_messages as $message)
					print "<li>".$message."</li>";
				print "</ul>";
			}

			if(Config::get("debug") && count(self::$debug_messages) > 0)
			{
				print "<ul class=\"messenger debug\">";
				foreach(self::$debug_messages as $message)
					print "<li>".$message."</li>";
				print "</ul>";
			}
		}
	}
}
?>