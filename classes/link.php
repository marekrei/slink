<?php
class Link {
	var $id;
	var $short_url;
	var $long_url;
	var $long_url_full;
	var $file;
	var $file_size;
	var $file_type;
	var $type;
	var $title;
	var $password;
	var $user;
	var $username;
	var $time_created;
	var $time_accessed;
	var $count_accessed;
	var $tags;
	var $tags_new;
	
	function __construct() {
		$this->id = -1;
		$this->short_url = null;
		$this->long_url = null;
		$this->file = null;
		$this->file_size = null;
		$this->file_type = null;
		$this->type = null;
		$this->password = null;
		$this->title = null;
		$this->user = -1;
		$this->username = null;
		$this->time_created = null;
		$this->time_accessed = null;
		$this->count_accessed = null;
		$this->tags = array();
		$this->tags_new = array();
		
	}
	
	public function setPassword($password)
	{
		if($password == null)
			$this->password = null;
		else
			$this->password = md5($password);
	}
	
	public function isProtected() {
		if(isset($this->password) && $this->password != null && strlen($this->password) > 0)
		{
			return true;
		}
		return false;
	}
	
	public function isMirrored()
	{
		if($this->isLink() && $this->file != null && strlen($this->file) > 0)
			return true;
		return false;
	}
	
	public function isLink(){
		if($this->type == 0)
			return true;
		return false;
	}
	
	public function processForm() {
		
		// Checking short URL
		if(isset($_POST['short_url']) && strlen($_POST['short_url']) > 0)
		{
			if($_POST['short_url'] != $this->short_url && !ShortUrl::isAvailable($_POST['short_url']))
			{
				Messenger::addBad("The short url is not available");
				return false;
			}
			else if(!ShortUrl::isValid($_POST['short_url']))
			{
				Messenger::addBad("Illegal characters in the short URL");
				return false;
			}
			else
			{
				$old_short_url = $this->short_url;
				$this->short_url = $_POST['short_url'];
				if($old_short_url != null)
					FileManager::renameLink($old_short_url, $this);
			}
		}
		
		if($this->short_url == null || strlen($this->short_url) == 0)
		{
			Messenger::addBad("Short URL needs to be set");
			return false;
		}
		
		// Checking password	
		if(isset($_POST['link_password']) && strlen($_POST['link_password']) > 0)
		{
			FileManager::moveLink($this, true);
			$this->setPassword($_POST['link_password']);
		}
		else if(isset($_POST['password_protected']) && $_POST['password_protected'] == "no")
		{
			FileManager::moveLink($this, false);
			$this->setPassword(null);
		}
		
		// Checking long url and file
		if((isset($_POST['long_url']) && strlen($_POST['long_url']) > 0) || (isset($_FILES['upload_file']['name']) && strlen($_FILES['upload_file']['name']) > 0))
		{
			//we are resetting the contents of the link as new content is added
			FileManager::deleteLink($this);
			if(isset($_POST['long_url']) && strlen($_POST['long_url']) > 0)
			{
				$this->type = 0;
				$this->long_url = self::clean_url($_POST['long_url']);
				$this->file = null;
			}
			else if(isset($_FILES['upload_file']['name']) && strlen($_FILES['upload_file']['name']) > 0)
			{
				$this->type = 1;
				$this->long_url = null;
				if(!FileManager::uploadFile($this->username, $_FILES['upload_file'], $this->isProtected(), $this))
				{
					return false;
				}
			}
			
			if($this->type == 0 && 
				((isset($_POST['create_mirror']) && $_POST['create_mirror'] == "yes" && Config::isTrue("allow_mirror")) 
					|| 
				(Config::get("always_mirror"))))
			{
				$this->file = Mirror::createMirror($this->long_url, $this->username, $this->short_url);
				if($this->file == null)
					Messenger::addBad("Creating a mirror did not work");
			}
		}
		
		if($this->long_url == null && $this->file == null)
		{
			Messenger::addBad("URL or file needs to be set");
			return false;
		}
		
		

		// Setting the title
		if(isset($_POST['title']))
			$this->title = $_POST['title'];
		else {
			$this->title = Mirror::getTitle($this);
			if($this->title == null) {
				if($this->type == 0)
					$this->title = rawurldecode(basename($this->long_url));
				else if($this->type == 1)
					$this->title = $this->file;
			}
		}
		
		// Parsing the tags
		if(isset($_POST['link_tags']) && strlen($_POST['link_tags']) > 0){
			$tag_names = explode(",", $_POST['link_tags']);
			foreach($tag_names as $tag_name){
				if(strlen(trim($tag_name)) > 0 && !in_array(trim($tag_name), $this->tags) && !in_array(trim($tag_name), $this->tags_new)){
					$this->tags[] = trim($tag_name);
					$this->tags_new[] = trim($tag_name);
				}
			}
			sort($this->tags);
		}
		return true;
	}
	
	public function getMirrorUrl()
	{
		if($this->isMirrored())
		{
			$mirror_prefix = substr(FileManager::getUserMirrorDir($this->username), strlen(Config::get("main_path")), strlen(FileManager::getUserMirrorDir($this->username)));
			return Config::get("url_prefix").$mirror_prefix.$this->short_url."/".rawurlencode($this->file);
		}
		return null;
	}
	
	public function getMirrorPath() {
		if($this->isMirrored())
			return FileManager::getUserMirrorDir($this->username).$this->short_url."/".$this->file;
		return null;
	}
	
	public function getFileUrl()
	{
		if($this->type == 1 && $this->file != null && strlen($this->file) > 0)
		{
			if($this->isProtected())
				$prefix = substr(FileManager::getUserProtectedDir($this->username), strlen(Config::get("main_path")), strlen(FileManager::getUserProtectedDir($this->username)));
			else
				$prefix = substr(FileManager::getUserFileDir($this->username), strlen(Config::get("main_path")), strlen(FileManager::getUserFileDir($this->username)));
			return Config::get("url_prefix").$prefix.rawurlencode($this->file);			
		}
		return null;
	}
	
	public function getFilePath()
	{
		if($this->type == 1 && $this->file != null && strlen($this->file) > 0)
		{
			if($this->isProtected())
				return FileManager::getUserProtectedDir($this->username).$this->file;
			else
				return FileManager::getUserFileDir($this->username).$this->file;		
		}
	}
	
	public function getMainUrl()
	{
		if($this->type == 0)
			return $this->long_url;
		else
			return $this->getFileUrl();
	}
	
	public function getShortUrl()
	{
		return Config::get("url_prefix").$this->short_url;
	}
	
	public static function deep_replace($search_array, $subject){
		do{
			$found = false;
			foreach( (array) $search_array as $val ) {
				while(strpos($subject, $val) !== false) {
					$found = true;
					$subject = str_replace($val, '', $subject);
				}
			}
		} while($found);
		
		return $subject;
	}
	
	// Function for cleaning the URL. Based on WP and YOURLS.
	public static function clean_url($url) 
	{
		$url = str_replace('http://http://', 'http://', $url);
		if ( !preg_match('!^([a-zA-Z]+://)!', $url))
			$url = 'http://'.$url;
		$url = str_replace(" ", "%20", $url);
		$url = preg_replace('|[^a-z0-9-~+_.?\[\]\^#=!&;,/:%@$\|*\'"()\\x80-\\xff]|i', '', $url );
		$strip = array('%0d', '%0a', '%0D', '%0A');
		$url = self::deep_replace($strip, $url);
		
		$url = str_replace(';//', '://', $url);
		$url = str_replace('&amp;', '&', $url);
		
		return $url;
	}
	
	public function isValidForThumb()
	{
		if(Config::get("enable_thumbnails") && ImageServer::canMakeThumbnail($this->file_type))
			return true;
		return false;
	}
}