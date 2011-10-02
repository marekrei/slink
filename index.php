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
include "classes/messenger.php";
include "classes/router.php";
include "classes/link.php";
include "classes/filemanager.php";
include "classes/user.php";
include "classes/auth.php";

DB::connect();
Config::init();
Router::run();

include "classes/shorturl.php";

include "classes/mirror.php";
include "classes/imageserver.php";
Auth::init();
if(isset($_GET['thumb']) && ImageServer::showImage($_GET['thumb']))
	die();
	
if(isset($_GET['checkForUpdate']))
{
	print Config::checkForUpdate();
	die();
}

if(isset($_GET['mobile']) && $_GET['mobile'] == "true")
	$_SESSION['mobile'] = true;
if(isset($_GET['mobile']) && $_GET['mobile'] == "false")
	$_SESSION['mobile'] = false;

if(isset($_GET['page']) && is_string($_GET['page']) && strlen($_GET['page']) > 0)
	$_PAGE = $_GET['page'];
else
	$_PAGE = null;

// User using the logout link
if($_PAGE == "logout")
{
	Auth::logout();
	header( 'Location: index.php' );
}

// User using the login link
if($_PAGE == "login" && Auth::isLoggedIn())
	header( 'Location: index.php' );

// User submitting a link and wants to be remembered
if($_PAGE == null && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['remember_me']) && $_POST['remember_me'] == "yes")		
	Auth::login();


include "pages/top.php";
include "pages/header.php";

switch($_PAGE){
	case "login":
		include "pages/login.php";
		break;
	case "links":
		include "pages/links.php";
		break;
	case "users":
		include "pages/users.php";
		break;
	case "editlink":
		include "pages/editlink.php";
		break;
	case "newuser":
		include "pages/newuser.php";
		break;
	case "edituser":
		include "pages/edituser.php";
		break;
	case "settings":
		include "pages/settings.php";
		break;
	case "resetpass":
		include "pages/resetpass.php";
		break;
	default:
		if(Auth::isLoggedIn())
			include "pages/main.php";
		else 
			include "pages/login.php";
}

include "pages/bottom.php";
?>