<?php
class Router {
	
	public static function direct($link, $mirror)
	{
		DB::countLinkAccess($link);
		if($link->type == 0)
		{
			if($mirror == true && $link->isMirrored())
			{
				header( 'Location: '.$link->getMirrorUrl() );
				Messenger::addBad("Failed to forward you to the URL ".$link->getMirrorUrl());
			}
			else
			{
				header( 'Location: '.$link->long_url );
				Messenger::addBad("Failed to forward you to the URL ".$link->long_url);
			}
		}
		else if($link->type == 1)
		{
			header( 'Location: '.$link->getFileUrl() );
			Messenger::addBad("Failed to forward you to the URL ".$link->getFileUrl());
		}
	}
	
	public static function run()
	{
		if(isset($_GET['l']) && strlen($_GET['l']) > 0)
		{
			$short_url = $_GET['l'];
			$mirror = false;
			if(strlen($short_url) > 0 && substr($short_url, strlen($short_url)-1, strlen($short_url)) == "!")
			{
				$short_url = substr($short_url, 0, strlen($short_url)-1);
				$mirror = true;
			}
			$link = DB::getLink(null, $short_url);
			if($link != null)
			{
				if($link->password != null && strlen($link->password) > 0)
				{
					if(isset($_POST['password']) && strlen($_POST['password']) > 0 && md5($_POST['password']) == $link->password)
					{
						self::direct($link, $mirror);
					}
					else
					{
						if(isset($_POST['password']) && strlen($_POST['password']) > 0)
							Messenger::addBad("Wrong password");
							
						include "pages/top.php";
						include "pages/gatekeeper.php";
						include "pages/bottom.php";
						die();
					}
				}
				else
					self::direct($link, $mirror);
				
			}
			else
			{
				$chunks = explode("/", $_GET['l']);
				if(count($chunks) <= 1)
					Messenger::addBad("The link you tried to access does not exist");
				else
					header( 'Location: '.Config::get("url_prefix").$chunks[0] );
			}
		}
	}
}
?>