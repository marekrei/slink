<?php
ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);


if (!file_exists("config.php")) {
	print "./config.php is required but not found";
	die();
}
include "config.php";
include "classes/config.php";
include "classes/db.php";
include "classes/filemanager.php";
include 'classes/messenger.php';
DB::connect();

if(!DB::isInstalled())
{
	$password = "";
	$length = 8;
	$characters = "0123456789abcdefghijklmnopqrstuvwxyz";
	for ($i = 0; $i < $length; $i++) {
        $password .= $characters[mt_rand(0, strlen($characters)-1)];
    }
	DB::install(md5($password));
	FileManager::initUser("admin");
	FileManager::createHtAccess(".htaccess");
	print "<div style=\"text-aling:center\">";
	print "Script successfully installed<br \>\n";
	print "Username: admin<br \>\n";
	print "Password: ".$password."<br \>\n";
	print "<a href=\"index.php\">Go to main page</a>";
	print "<div>";
}

?>