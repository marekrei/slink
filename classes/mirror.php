<?php
class Mirror {
	
	public static function runRorrim($url, $dir) {
		Messenger::addDebug("Attempting to mirror using Rorrim");
		$url = escapeshellarg($url);
		$dir = escapeshellarg($dir);
		
		$cmd = "python ".Config::get("main_path")."python/rorrim_page.py ".$url." ".$dir;
		Messenger::addDebug($cmd);
		$path = exec($cmd);
		
		if(strlen($path) > strlen(Config::get("main_path")) && substr($path, 0, strlen(Config::get("main_path"))) == Config::get("main_path")) {
			Messenger::addDebug("Success");
			return $path;
		}
		else {
			Messenger::addDebug("Failed");
			return null;
		}
	}
	
	public static function runWget($url, $dir) {
		Messenger::addDebug("Attempting to mirror using Wget");
		$origurl = $url;
		$origdir = $dir;
		$url = escapeshellarg($url);
		$dir = escapeshellarg($dir);
		
		$cmd = "wget -nd -pHEKk --directory-prefix=".$dir." $url";
		Messenger::addDebug($cmd);
		system($cmd);
		
		$name = self::findFileName($origurl, $origdir);
		if($name == null || strlen($name) <= 0)
		{
			Messenger::addDebug("Could not find file on filesystem. Failed.");
			return null;
		}
		else
		{
			Messenger::addDebug("Success");
			$path = $origdir.$name;
			return $path;
		}
	}
	
	public static function runSimple($url, $dir)
	{
		Messenger::addDebug("Attempting to mirror using Simple approach");
		$name = basename($url);
		if($name == null || strlen($name) <= 0)
			$name = "index.html";
						
		$data = self::getUrlContents($url);
		file_put_contents($dir.$name, $data);
		
		return $dir.$name;
	}
	
	// Wget productes filesnames which can be hard to predict. 
	// This function tries some to see if the file exists.
	public static function findFileName($url, $dir)
	{
		$name = basename($url);
		Messenger::addDebug("Looking for: " . $name . " in ". $dir);
		if(file_exists($dir.$name))	
			return $name;
		else if(file_exists($dir.$name.".html"))
			return $name.".html";
		else if(file_exists($dir.$name.".htm"))
			return $name.".htm";
		else if(file_exists($dir.$name.".1.html"))
			return $name.".1.html";
		else if(file_exists($dir.$name.".1.htm"))
			return $name.".1.htm";
		else if(file_exists($dir."index.htm"))
			return "index.htm";
		else if(file_exists($dir."index.html"))
			return "index.html";
		else if (is_dir($dir) && $handle = opendir($dir)) {
		    while (false !== ($file = readdir($handle))) {
		        if(FileManager::getFileExtension($dir.$file) == "orig")
		        {
		        	$candidate = basename($file, ".orig");
		        	if(file_exists($dir.$candidate))
		        		return $candidate;
		        }
		    }
		}
		return null;		
	}
	
	public static function getUrlContents($url)
	{
		Messenger::addDebug("Fetching URL contents: ".$url);
		$data = @file_get_contents($url);
		return $data;
	}
	
	public static function getFileContents($path)
	{
		Messenger::addDebug("Fetching file contents: ".$path);
		$data = @file_get_contents($path);
		return $data;
	}
	
	public static function createMirror($url, $username, $shortUrl)
	{
		$url = Link::clean_url($url);
		$dir = FileManager::getUserMirrorDir($username).$shortUrl."/";
		FileManager::newDir($dir);
		
		$path = self::runRorrim($url, $dir);
		if($path == null || strlen($path) <= 0)
			$path = self::runWget($url, $dir);
		if($path == null || strlen($path) <= 0)
			$path = self::runSimple($url, $dir);
		if($path == null || strlen($path) <= 0)
		{
			Messenger::addDebug("Mirroring failed. Cleaning up.");
			FileManager::deleteDirRec($dir);
			return null;
		}
		else
			return substr($path, strlen($dir), strlen($path));
	}
	
	public static function getTitle($link)
	{
		if($link->isMirrored())
			$data = self::getFileContents($link->getMirrorPath());
		else
			$data = self::getUrlContents($link->getMainUrl());
		
		if($data != null)
		{
			if ( preg_match('/<title>(.*?)<\/title>/is', $data, $matches ) ) {
				return $matches[1];
			}
		}
		return null;
	}
}
?>