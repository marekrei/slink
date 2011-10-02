<?php

class Auth {
	static $user = null;
	
	public static function init()
	{
		session_start();
		self::$user = null;
		if(!self::isLoggedIn())
			self::login();
	}
	
	public static function getUser() {
		if(self::$user == null)
		{
			if(self::isLoggedIn())
				return self::$user;
			else
				return null;
		}
		else
			return self::$user;
	}
	/*
	public static function getUserByUsername($username, $password)
	{
		if($username == null || $password == null)
			return null;
			
		$usr = DB::getUser(null, $_POST['username'], null, null);

		if($usr == null)
			return null;
		if($usr->password != md5($_POST['password']))
			return null;
		return $usr;
	}
	*/
	public static function isLoggedIn() {
 		if(session_id() == "")
 			return false;
		if(self::$user != null)
			return true;
		
		if(isset($_SESSION) && isset($_SESSION['username']) && isset($_SESSION['password']))
		{
			$usr = DB::getUser(null, $_SESSION['username'], null, null);
			if($usr == null)
				return false;
			if($usr->password != $_SESSION['password'])
				return false;
			self::$user = $usr;
			return true;
		}
		
		return false;
	}
	
	public static function login() {
  			
		if(isset($_POST) && isset($_POST['username']) && isset($_POST['password']))
		{
			$usr = DB::getUser(null, $_POST['username'], null, null);

			if($usr == null || $usr->password != md5($_POST['password']))
			{
				Messenger::addBad("Wrong username or password");
				return false;
			}
			self::$user = $usr;
			$_SESSION['username'] = $_POST['username'];
			$_SESSION['password'] = md5($_POST['password']);
			if(isset($_POST['remember_me']) && $_POST['remember_me'] == "true")
				self::setPersistentCookie();
			return true;
		}
		else if(isset($_COOKIE) && isset($_COOKIE[Config::get("cookie_prefix")."username"]) && isset($_COOKIE[Config::get("cookie_prefix")."password"]))
		{
			$usr = DB::getUser(null, $_COOKIE[Config::get("cookie_prefix")."username"], null, null);

			if($usr != null && $usr->password == $_COOKIE[Config::get("cookie_prefix")."password"])
			{
				self::$user = $usr;
				$_SESSION['username'] = $_COOKIE[Config::get("cookie_prefix")."username"];
				$_SESSION['password'] = $_COOKIE[Config::get("cookie_prefix")."password"];
				return true;
			}
		}
		return false;
	}
	
	public static function logout() {
		
		unset($_SESSION['username']);
		unset($_SESSION['password']);
		self::deletePersistentCookie();
		session_destroy();
	}
	
	public static function isAllowedLinks()
	{
		if(self::isLoggedIn())
		{
			$user = self::getUser();
			if($user->allowed_links == true && is_numeric(Config::get("allow_links")) && intval(Config::get("allow_links")) == 1)
				return true;
		}
		return false;
	}
	
	public static function isAllowedFiles()
	{
		if(self::isLoggedIn())
		{
			$user = self::getUser();
			if($user->allowed_files == true && is_numeric(Config::get("allow_files")) && intval(Config::get("allow_files")) == 1)
				return true;
		}
		return false;
	}
	
	public static function isAllowedAdmin()
	{
		if(self::isLoggedIn())
		{
			$user = self::getUser();
			if($user->allowed_admin == true)
				return true;
		}
		return false;
	}
	
	public static function setPersistentCookie()
	{
		$user = self::getUser();
		if($user != null)
		{
			$exp = time()+60*60*24*30;
			setcookie(Config::get("cookie_prefix")."username", $user->username, $exp);
			setcookie(Config::get("cookie_prefix")."password", $user->password, $exp);
		}
	}
	
	public static function deletePersistentCookie()
	{
		setcookie(Config::get("cookie_prefix")."username", "", time()-60*60);
		setcookie(Config::get("cookie_prefix")."password", "", time()-60*60);
	}
	
	public static function isMobile(){
		if(isset($_SESSION) && isset($_SESSION['mobile']) && $_SESSION['mobile'] == true)
			return true;
		return false;
	}
	
	public static function updatePassword($pass)
	{
		$_SESSION['password'] = md5($pass);
	}
}

?>