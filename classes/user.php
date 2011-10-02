<?php
class User {
	var $id;
	var $username;
	var $password;
	var $email;
	var $time_created;
	var $time_accessed;
	var $allowed_admin;
	var $reset_hash;
	var $reset_time;
	
	function __construct() {
		$this->id = -1;
		$this->username = null;
		$this->password = null;
		$this->email = null;
		$this->time_created = null;
		$this->time_accessed = null;
		$this->allowed_admin = null;
		$this->reset_hash = null;
		$this->reset_time = null;
	}
	
	public function processForm(){
		if(isset($_POST['username']) && strlen($_POST['username']) > 0)
			$this->username = $_POST['username'];
		if(isset($_POST['password']) && strlen($_POST['password']) > 0)
			$this->password = md5($_POST['password']);
		if(isset($_POST['email']) && strlen($_POST['email']) > 0)
			$this->email = $_POST['email'];
			
		if(isset($_POST['allowed_admin']) && $_POST['allowed_admin'] == "true")
			$this->allowed_admin = 1;
		else 
			$this->allowed_admin = 0;
			
	}
	
	public static function isEmailValid($email)
	{
	   $isValid = true;
	   $atIndex = strrpos($email, "@");
	   if (is_bool($atIndex) && !$atIndex)
	   {
	      $isValid = false;
	   }
	   else
	   {
	      $domain = substr($email, $atIndex+1);
	      $local = substr($email, 0, $atIndex);
	      $localLen = strlen($local);
	      $domainLen = strlen($domain);
	      if ($localLen < 1 || $localLen > 64)
	      {
	         // local part length exceeded
	         $isValid = false;
	      }
	      else if ($domainLen < 1 || $domainLen > 255)
	      {
	         // domain part length exceeded
	         $isValid = false;
	      }
	      else if ($local[0] == '.' || $local[$localLen-1] == '.')
	      {
	         // local part starts or ends with '.'
	         $isValid = false;
	      }
	      else if (preg_match('/\\.\\./', $local))
	      {
	         // local part has two consecutive dots
	         $isValid = false;
	      }
	      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
	      {
	         // character not valid in domain part
	         $isValid = false;
	      }
	      else if (preg_match('/\\.\\./', $domain))
	      {
	         // domain part has two consecutive dots
	         $isValid = false;
	      }
	      else if(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local)))
	      {
	         // character not valid in local part unless 
	         // local part is quoted
	         if (!preg_match('/^"(\\\\"|[^"])+"$/',
	             str_replace("\\\\","",$local)))
	         {
	            $isValid = false;
	         }
	      }
	      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
	      {
	         // domain not found in DNS
	         $isValid = false;
	      }
	   }
	   return $isValid;
	}
	
	public static function isUsernameValid($username)
	{
		return FileManager::isUsernameValid($username);
	}
	
	public static function isUsernameAvailable($username)
	{
		return DB::isUsernameAvailable($username);
	}
	
	public static function isEmailAvailable($email)
	{
		return DB::isEmailAvailable($email);
	}
	
	public function canEditLink($link)
	{
		if($this->allowed_admin || $this->id == $link->user)
			return true;
		return false;
	}
	
	public function canDeleteLink($link)
	{
		return $this->canEditLink($link);
	}
}
?>