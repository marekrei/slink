<?php
class FileManager {
	
	public static function getUserMainDir($username)
	{
		return Config::get("main_path")."user/".$username."/";
	}
	
	public static function getUserFileDir($username)
	{
		return Config::get("main_path")."user/".$username."/files/";
	}
	
	public static function getUserProtectedDir($username)
	{
		return Config::get("main_path")."user/".$username."/protected/";
	}
	
	public static function getUserMirrorDir($username)
	{
		return Config::get("main_path")."user/".$username."/mirror/";
	}
	
	public static function isUsernameValid($str)
	{
		if($str == null)
			return false;
		if(ctype_alnum($str))
			return true;
		return false;
	}
	
	public static function initUser($username)
	{
		if(!FileManager::isUsernameValid($username))
		{
			Messenger::addBad("Not using a valid username.");
			return null;
		}
		
		if(!is_dir(self::getUserMainDir($username)))
			FileManager::newDir(self::getUserMainDir($username));
		if(!is_dir(self::getUserFileDir($username)))
			FileManager::newDir(self::getUserFileDir($username));
		if(!is_dir(self::getUserProtectedDir($username)))
			FileManager::newDir(self::getUserProtectedDir($username));
		if(!is_dir(self::getUserMirrorDir($username)))
			FileManager::newDir(self::getUserMirrorDir($username));
	}

	public static function newDir($dirname)
	{
		if(strlen($dirname) > 0)
		{
			if(is_dir($dirname))
				return true;
				
			if(!mkdir($dirname, 0777, true))
			{
				Messenger::addBad("Failed to create directory ".$dirname);
				return false;
			}
			else
			{
				chmod($dirname, 0777);
				Messenger::addDebug("created dir " . $dirname);
				return true;
			}
		}
		return false;
	}
	
	public static function getFileExtension($filepath)
	{
		return strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
	}
	
	public static function makeNewFileName($dir, $filename)
	{
		$extension = pathinfo($filename, PATHINFO_EXTENSION);
		if($extension != null && strlen($extension) > 0)
			$basename = basename($filename, ".".$extension);
		else
			$basename = basename($filename);
		
		$new_filename = $filename;
		$i = 1;
		while(true)
		{
			if(!file_exists($dir.$new_filename))
				break;
			
			if($extension != null && strlen($extension) > 0)
				$new_filename = $basename."_".$i.".".$extension;
			else 	
				$new_filename = $basename."_".$i;
			$i++;
		}
		
		return $new_filename;
		
	}
	
	public static function getFileMime($filepath)
	{
		$fhandle = finfo_open(FILEINFO_MIME);
		$mime_type = finfo_file($fhandle, $filepath);
		$mime_type_chunks = preg_split('/\s+/', $mime_type);
		$mime_type = $mime_type_chunks[0];
		$mime_type_chunks = explode(";", $mime_type);
		$mime_type = $mime_type_chunks[0];
		return $mime_type;
	}
	
	public static function uploadFile($username, $userfile, $protected, $link)
	{
		if($protected == true)
			$upload_dir = self::getUserProtectedDir($username);
		else
			$upload_dir = self::getUserFileDir($username);
		
		$filename = basename($userfile['name']);
		if(get_magic_quotes_gpc())
			$filename = stripslashes($filename);
			
		$filename = FileManager::makeNewFileName($upload_dir, $filename);

		if(function_exists("finfo_open") && function_exists("finfo_file"))
			$mime_type = FileManager::getFileMime($userfile['tmp_name']);
		else
			$mime_type = $userfile['type'];
	
		$extension = strtolower(FileManager::getFileExtension($userfile['name']));
		$reject_extensions = array();
		foreach(explode(",", Config::get("reject_extensions")) as $reject_extension)
			$reject_extensions[] = strtolower(trim($reject_extension));
		
		if(!is_writable($upload_dir))
		{
			Messenger::addBad("Upload dir ".$upload_dir." is not writable");
			return false;
		}
		else if(!is_uploaded_file($userfile['tmp_name']))
		{
			Messenger::addBad("Upload failed.");
			return false;
		}
		/*else if(is_array(EncodeExplorer::getConfig("upload_allow_type")) && count(EncodeExplorer::getConfig("upload_allow_type")) > 0 && !in_array($mime_type, EncodeExplorer::getConfig("upload_allow_type")))
		{
			$encodeExplorer->setErrorString("upload_type_not_allowed");
		}
		*/
		else if(in_array($extension, $reject_extensions))
		{
			Messenger::addBad("This extension is not allowed");
			return false;
		}
		else if(!@move_uploaded_file($userfile['tmp_name'], $upload_dir.$filename))
		{
			Messenger::addBad("Moving of uploaded file failed");
			return false;
		}
		else
		{
			chmod($upload_dir.$filename, 0755);
			/*if(substr($upload_dir.$filename, 0, strlen(Config::get("main_path"))) == Config::get("main_path"))
				return substr($upload_dir.$filename, strlen(Config::get("main_path")), strlen($upload_dir.$filename));
			*/
			$link->file = $filename;
			$link->file_type = $extension;
			$link->file_size = $userfile['size'];
			return true;
		}
		return false;
	}
	
	public static function deleteUser($username)
	{
		self::deleteDirRec(self::getUserMainDir($username));
	}
	
	public static function deleteDirRec($dir) 
	{
		if (is_dir($dir)) 
		{
			$objects = scandir($dir);
			foreach ($objects as $object) 
			{
				if ($object != "." && $object != "..")
				{
					if (filetype($dir."/".$object) == "dir") 
						deleteDirRec($dir."/".$object); 
					else 
						unlink($dir."/".$object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
	} 
	
	public static function deleteLink($link)
	{
		if($link->file != null && strlen($link->file) > 0 && is_file(self::getUserFileDir($link->username).$link->file))
		{
			unlink(self::getUserFileDir($link->username).$link->file);
		}
		self::deleteDirRec(self::getUserMirrorDir($link->username).$link->short_url);
		self::deleteDirRec(self::getUserProtectedDir($link->username).$link->short_url);
	}
	
	public static function renameLink($oldShortUrl, $link)
	{
		if(is_dir(self::getUserMirrorDir($link->username).$oldShortUrl))
			rename(self::getUserMirrorDir($link->username).$oldShortUrl, self::getUserMirrorDir($link->username).$link->short_url);
		if(is_dir(self::getUserProtectedDir($link->username).$oldShortUrl))
			rename(self::getUserProtectedDir($link->username).$oldShortUrl, self::getUserProtectedDir($link->username).$link->short_url);
	}
	
	public static function moveLink($link, $protected)
	{
		if($link->isProtected() && !$protected && file_exists(self::getUserProtectedDir($link->username).$link->file))
		{
			$filename = self::makeNewFileName(self::getUserFileDir($link->username), $link->file);
			Messenger::addDebug("Moving file from ".self::getUserProtectedDir($link->username).$link->file." to ". self::getUserFileDir($link->username).$filename);
			rename(self::getUserProtectedDir($link->username).$link->file, self::getUserFileDir($link->username).$filename);
			$link->file = $filename;
			}
		if(!$link->isProtected() && $protected && file_exists(self::getUserFileDir($link->username).$link->file))
		{
			$filename = self::makeNewFileName(self::getUserProtectedDir($link->username), $link->file);
			Messenger::addDebug("Moving file from ".self::getUserFileDir($link->username).$link->file." to ". self::getUserProtectedDir($link->username).$filename);
			rename(self::getUserFileDir($link->username).$link->file, self::getUserProtectedDir($link->username).$filename);
			$link->file = $filename;
			
		}
	}
	
	public static function createHtAccess($path){
		$fp = fopen($path, 'w+');
		$base = parse_url(Config::get("url_prefix"), PHP_URL_PATH);
		$contents = "<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ ".$base."index.php?l=$1 [L]
</IfModule>";
		fwrite($fp, $contents);
		fclose($fp);
	}
}
?>