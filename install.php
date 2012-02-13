<?php
ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);


if (!file_exists("config.php")) {
	print "./config.php is required but not found";
	die();
}

require_once "config.php";
require_once "classes/config.php";
require_once "classes/db.php";
require_once "classes/messenger.php";
require_once "classes/filemanager.php";
require_once "classes/user.php";
DB::connect();

if(!DB::isInstalled())
{
	$password = "";
	$length = 8;
	$characters = "0123456789abcdefghijklmnopqrstuvwxyz";
	for ($i = 0; $i < $length; $i++) {
        $password .= $characters[mt_rand(0, strlen($characters)-1)];
    }
	
    DB::install();
	
	$admin = new User();
	$admin->username = "admin";
	$admin->password = md5($password);
	$admin->allowed_admin = 1;
	DB::addUser($admin);
	
	FileManager::initUser("admin");
	FileManager::createHtAccess(".htaccess");
	print "<div style=\"text-aling:center\">";
	print "Script successfully installed<br \>\n";
	print "Username: admin<br \>\n";
	print "Password: ".$password."<br \>\n";
	print "You can delete install.php now.<br />\n";
	print "<a href=\"index.php\">Go to main page</a>";
	print "<div>";
}

?>