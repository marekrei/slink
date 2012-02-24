<?php
class Tools{
	public static function isMobileUser(){
		$user_agents = array(
			'iPhone' =>'iphone',
			'iPod' => 'ipod',
			'iPad' => 'ipad',
			'PocketIE' => 'iemobile',
			'Opera Mobile' => 'Opera Mobi',
			'Android' => 'android',
			'Symbian' => 'symbian',
			'Symbian S60' => 'series60',
			'Symbian S70' => 'series70',
			'Symbian S80' => 'series80',
			'Symbian S90' => 'series90',
			'Symbian S60' => 'series 60',
			'Symbian S70' => 'series 70',
			'Symbian S80' => 'series 80',
			'Symbian S90' => 'series 90',
			'BlackBerry' => 'blackberry',
			'BlackBerry Storm' => 'blackberry05',
			'Palm' => 'palm',
			'Web OS' => 'webos',
		);
		
		foreach ($user_agents as $key => $val){
			if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), $val))
				return true;
		}
		
		if(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']))
			return true;
		
		return false;
		
	}
}
?>