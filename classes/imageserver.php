<?php
class ImageServer
{
	static $file_types = array(
		"7z" => "package-x-generic.png",
		"avi" => "video-x-generic.png",
		"bz2" => "package-x-generic.png",
		"c" => "text-x-script",
		"cab" => "package-x-generic.png",
		"cpp" => "text-x-script",
		"doc" => "x-office-document.png",
		"docx" => "x-office-document.png",
		"exe" => "application-x-executable.png",
		"gz" => "package-x-generic.png",
		"gif" => "image-x-generic.png",
		"cab" => "package-x-generic.png",
		"htm" => "text-html.png",
		"html" => "text-html.png",
		"java" => "text-x-script",
		"jpg" => "image-x-generic.png",
		"jpeg" => "image-x-generic.png",
		"mov" => "video-x-generic.png",
		"mp3" => "audio-x-generic.png",
		"mp4" => "audio-x-generic.png",
		"mpeg" => "video-x-generic.png",
		"mpg" => "video-x-generic.png",
		"odg" => "x-office-drawing.png",
		"opd" => "x-office-presentation.png",
		"ods" => "x-office-spreadsheet.png",
		"odt" => "x-office-document.png",
		"php" => "text-x-script",
		"png" => "image-x-generic.png",
		"pps" => "x-office-presentation.png",
		"ppsx" => "x-office-presentation.png",
		"ppt" => "x-office-presentation.png",
		"pptx" => "x-office-presentation.png",
		"psd" => "x-office-drawing.png",
		"rar" => "package-x-generic.png",
		"tar" => "package-x-generic.png",
		"tgz" => "package-x-generic.png",
		"wav" => "audio-x-generic.png",
		"wma" => "audio-x-generic.png",
		"wmv" => "video-x-generic.png",
		"wcf" => "x-office-drawing.png",
		"xls" => "x-office-spreadsheet.png",
		"xlsx" => "x-office-spreadsheet.png",
		"zip" => "package-x-generic.png",
		"unknown" => "text-x-generic.png"		
	);
	
	//
	// Checks if an image is requested and displays one if needed
	//
	public static function showImage($id)
	{
		if(intval($id) > 0 && Config::get("enable_thumbnails"))
		{
			$link = DB::getLink(intval($id), null);
			if($link != null && $link->type == 1)
			{
				ImageServer::showThumbnail($link->getFilePath());
			}
			return true;
		}
		return false;
	}
	
	public static function isEnabledPdf()
	{
		if(class_exists("Imagick"))
			return true;
		return false;
	}
	
	public static function isEnabledThumbnail()
	{
		return Config::get("enable_thumbnails");
	}
	
	public static function openPdf($file)
	{
		if(!ImageServer::isEnabledPdf())
			return null;
			
		$im = new Imagick($file.'[0]');
		$im->setImageFormat( "png" );
		$str = $im->getImageBlob();
		$im2 = imagecreatefromstring($str);
		return $im2;
	}
	
	//
	// Creates and returns a thumbnail image object from an image file
	//
	public static function createThumbnail($file)
	{
		$max_width = 200;
		$max_height = 200;

		if(FileManager::getFileExtension($file) == "pdf")
			$image = ImageServer::openPdf($file);
		else
			$image = ImageServer::openImage($file);
		if($image == null)
			return;
			
		imagealphablending($image, true);
		imagesavealpha($image, true);
			
		$width = imagesx($image);
		$height = imagesy($image);
			
		$new_width = $max_width;
		$new_height = $max_height;
		if(($width/$height) > ($new_width/$new_height))
			$new_height = $new_width * ($height / $width);
		else 
			$new_width = $new_height * ($width / $height);   
		
		if($new_width >= $width && $new_height >= $height)
		{
			$new_width = $width;
			$new_height = $height;
		}
		
		$new_image = ImageCreateTrueColor($new_width, $new_height);
		imagealphablending($new_image, true);
		imagesavealpha($new_image, true);
		$trans_colour = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
		imagefill($new_image, 0, 0, $trans_colour);
		
		imagecopyResampled ($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		
		return $new_image;
	}
	
	//
	// Function for displaying the thumbnail.
	// Includes attempts at cacheing it so that generation is minimised.
	//
	public static function showThumbnail($file)
	{
		$mtime = gmdate('r', filemtime($file));
		$etag = md5($mtime.$file);
		
		if ((isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $mtime)
			|| (isset($_SERVER['HTTP_IF_NONE_MATCH']) && str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == $etag)) 
		{
			header('HTTP/1.1 304 Not Modified');
			return;
		}
		else
		{
			header('ETag: "'.$etag.'"');
			header('Last-Modified: '.$mtime);
			header('Content-Type: image/png');
			$image = ImageServer::createThumbnail($file);
			imagepng($image);
		}
	}
	
	//
	// A helping function for opening different types of image files
	//
	public static function openImage ($file) 
	{
	    $size = getimagesize($file);
	    
	    if($size["mime"] == "image/jpeg" && function_exists("imagecreatefromjpeg"))
	    	return imagecreatefromjpeg($file);
	    else if($size["mime"] == "image/gif" && function_exists("imagecreatefromgif"))
	    	return imagecreatefromgif($file);
	    else if($size["mime"] == "image/png" && function_exists("imagecreatefrompng"))
	    	return imagecreatefrompng($file);
	    return null;
	}
	
	public static function canMakeThumbnail($type)
	{
		if(($type == "jpg" || $type == "jpeg") && function_exists("imagecreatefromjpeg"))
			return true;
		if($type == "gif" && function_exists("imagecreatefromgif"))
			return true;
		if($type == "png" && function_exists("imagecreatefrompng"))
			return true;
		if($type == "pdf" && class_exists("Imagick"))
			return true;
		return false;
	}
	
	public static function getIcon($extension)
	{
		$dir = "img/icons/";
		if(key_exists($extension, self::$file_types))
			return $dir.self::$file_types[$extension];
		else 
			return $dir.self::$file_types["unknown"];
	}
}
?>