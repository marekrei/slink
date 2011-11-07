<?php
class DB {
	public static function safei($int)
	{
		if($int != null && is_int($int))
			return $int;
		else if($int == 0)
			return 0;
		else if(is_string($int))
			return intval($int);
		else
			return null;
	}
	
	public static function safes($str)
	{
		if(is_string($str))
			return mysql_real_escape_string($str);
		return null;
	}
	
	public static function connect()
	{
		$connection = mysql_connect(Config::get("mysql_host"), Config::get("mysql_username"), Config::get("mysql_password"));
		if (!$connection)
		{
			
			die('Could not connect: ' . mysql_error());
		}
		mysql_select_db(self::safes(Config::get("mysql_db")), $connection);
		mysql_query("SET NAMES 'utf8'");
	}
	
	public static function makeUserFromArray($array)
	{
		$user = new User();
		$user->id = (int)$array['id'];
		$user->username = $array['username'];
		$user->password = $array['password'];
		$user->email = $array['email'];
		$user->time_created = strtotime($array['time_created']);
		$user->time_accessed = strtotime($array['time_accessed']);
		$user->allowed_admin = $array['allowed_admin'];
		$user->reset_hash = $array['reset_hash'];
		$user->reset_time = strtotime($array['reset_time']);
		return $user;
	}
	
	public static function makeLinkFromArray($array)
	{
		$link = new Link();
		$link->id = (int)$array['id'];
		$link->short_url = $array['short_url'];
		$link->long_url = $array['long_url'];
		$link->file = $array['file'];
		$link->file_type = $array['file_type'];
		$link->file_size = $array['file_size'];
		$link->type = (int)$array['type'];
		$link->title = $array['title'];
		$link->password = $array['password'];
		$link->user = (int)$array['user'];
		if(key_exists('username', $array))
			$link->username = $array['username'];
		$link->time_created = strtotime($array['time_created']);
		$link->time_accessed = strtotime($array['time_accessed']);
		$link->count_accessed = $array['count_accessed'];
		return $link;
	}
	/*
	public static function getUserByUsername($username)
	{
		$query = sprintf("SELECT * FROM %susers WHERE username='%s' LIMIT 1",
			DB::safes(Config::get("mysql_prefix")),
            DB::safes($username));

		$result = mysql_query($query);
		if(!$result || mysql_num_rows($result) == 0)
			return null;

		$array = mysql_fetch_array($result);
		$user = self::makeUserFromArray($array);		
		
		return $user;
	}
	
	public static function getUserByEmail($email)
	{
		$query = sprintf("SELECT * FROM %susers WHERE email='%s' LIMIT 1",
			DB::safes(Config::get("mysql_prefix")),
            DB::safes(strtolower($email)));

		$result = mysql_query($query);
		if(!$result || mysql_num_rows($result) == 0)
			return null;

		$array = mysql_fetch_array($result);
		$user = self::makeUserFromArray($array);		
		
		return $user;
	}
	*/
	public static function getUser($id = null, $username = null, $email = null, $reset_hash = null)
	{
		$query = sprintf("SELECT * FROM %susers ", DB::safes(Config::get("mysql_prefix")));
		
		$conditions = array();
		if($id != null)
			$conditions[] = sprintf("id=%d", DB::safei($id));
		if($username != null)
			$conditions[] = sprintf("username='%s'", DB::safes($username));
		if($email != null)
			$conditions[] = sprintf("email='%s'", DB::safes($email));
		if($reset_hash != null)
			$conditions[] = sprintf("reset_hash='%s'", DB::safes($reset_hash));
		
		if(count($conditions) > 0) {
			$query .= "WHERE ".implode(" and ", $conditions)." ";
		}
		else
			return null;
		
		$query .= "LIMIT 1";

		Messenger::addDebug("MYSQL: ".$query);
		
		$result = mysql_query($query);
		if(!$result || mysql_num_rows($result) == 0)
			return null;

		$array = mysql_fetch_array($result);
		$user = self::makeUserFromArray($array);		
		
		return $user;
	}
	
	public static function addUser($user)
	{
		$query = "INSERT INTO ".DB::safes(Config::get("mysql_prefix"))."users SET ";
		$query .= "username='".DB::safes($user->username)."', ";
		$query .= "password='".DB::safes($user->password)."', ";
		$query .= "email='".DB::safes($user->email)."', ";
		$query .= "allowed_admin=".DB::safei($user->allowed_admin).", ";
		$query .= "time_created=now(), ";
		$query .= "time_accessed=now() ";

		Messenger::addDebug("MYSQL: ".$query);
		mysql_query($query);
	}
	
	public static function editUser($user)
	{
		$query = "UPDATE ".DB::safes(Config::get("mysql_prefix"))."users SET ";
		$query .= "username='".DB::safes($user->username)."', ";
		if($user->password != null && strlen($user->password) > 0)
			$query .= "password='".DB::safes($user->password)."', ";
		$query .= "email='".DB::safes($user->email)."', ";
		if($user->reset_hash != null)
			$query .= "reset_hash='".DB::safes($user->reset_hash)."', ";
		if($user->reset_time != null)
			$query .= "reset_time='".date( 'Y-m-d H:i:s', $user->reset_time )."', ";
		$query .= "allowed_admin=".DB::safei($user->allowed_admin)." ";
		$query .= "WHERE id=".DB::safei($user->id);

		Messenger::addDebug("MYSQL: " . $query);
		mysql_query($query);
	}
	
	public static function delUser($id)
	{
		$query = sprintf("DELETE FROM %susers where id=%d",
					DB::safes(Config::get("mysql_prefix")),
					DB::safei($id));

		Messenger::addDebug("MYSQL: ".$query);
		mysql_query($query);
	}
	
	private static function makeUsersQuery($strquery)
	{
		if(!is_string($strquery) || strlen($strquery) <= 0)
			$strquery = null;
		$query = "SELECT * FROM ".DB::safes(Config::get("mysql_prefix"))."users ";
		if($strquery != null)
			$query .= "where username LIKE '%".DB::safes($strquery)."%' OR email LIKE '%".DB::safes($strquery)."%' ";
		return $query;
	}
	
	public static function getUsersCount($strquery)
	{
		$query = self::makeUsersQuery($strquery);
		$result = mysql_query($query);
		if(!$result)
			return 0;
		return mysql_num_rows($result);
	}
	
	public static function getUsers($strquery, $order_by, $order_as, $page, $limit)
	{
		$query = self::makeUsersQuery($strquery);

		$order_by = self::checkUsersOrderBy($order_by);
		$order_as = self::checkOrderAs($order_as);
		if(!is_int($page) || $page < 0)
			$page = 0;
		if(!is_int($limit) || $limit < 0)
			$limit = 20;
		
		$query .= " ORDER BY ".DB::safes($order_by)." ".strtoupper(DB::safes($order_as))." ";
		$query .= "LIMIT ".DB::safei($page*$limit).",".DB::safei($limit);
		
		Messenger::addDebug("MYSQL: ".$query);
		
		$result = mysql_query($query);
		if(!$result || mysql_num_rows($result) == 0)
			return array();

		$users = array();
		while($array = mysql_fetch_array($result))
		{
			$user = self::makeUserFromArray($array);
			$users[] = $user;
		}
		return $users;
	}
	
	public static function getLink($id, $shortUrl)
	{
		$query = sprintf("SELECT %susers.username AS username, %slinks.* FROM %slinks LEFT JOIN %susers ON %susers.id=%slinks.user",
				DB::safes(Config::get("mysql_prefix")),
				DB::safes(Config::get("mysql_prefix")),
				DB::safes(Config::get("mysql_prefix")),
				DB::safes(Config::get("mysql_prefix")),
				DB::safes(Config::get("mysql_prefix")),
				DB::safes(Config::get("mysql_prefix")),
				DB::safes(Config::get("mysql_prefix")));
				
		$conditions = array();
		if($id != null)
			$conditions[] = sprintf("%slinks.id=%d",
				DB::safes(Config::get("mysql_prefix")),
				DB::safei($id));
		
		if($shortUrl != null)
			$conditions[] = sprintf("%slinks.short_url='%s'",
				DB::safes(Config::get("mysql_prefix")),
				DB::safes($shortUrl));
				
		if(count($conditions) > 0)
		{
			$condition = join(" AND ", $conditions);
			$query .= " WHERE ".$condition;
		}
		$query .= " LIMIT 1";
			
		Messenger::addDebug("MYSQL: ".$query);
		
		$result = mysql_query($query);
		if($result == null || mysql_num_rows($result) <= 0)
			return null;
			
		$array = mysql_fetch_array($result);
		$link = self::makeLinkFromArray($array);	
		
		$query2 = sprintf("SELECT %stagnames.name AS name, %stags.* FROM %stags LEFT JOIN %stagnames ON %stagnames.id=%stags.tag_id where %stags.link_id=%d order by name",
		DB::safes(Config::get("mysql_prefix")),
		DB::safes(Config::get("mysql_prefix")),
		DB::safes(Config::get("mysql_prefix")),
		DB::safes(Config::get("mysql_prefix")),
		DB::safes(Config::get("mysql_prefix")),
		DB::safes(Config::get("mysql_prefix")),
		DB::safes(Config::get("mysql_prefix")),
		DB::safei($id));
		
		Messenger::addDebug("MYSQL: ".$query2);
		$result2 = mysql_query($query2);
		$tags = array();
		while($data2 = mysql_fetch_array($result2)){
			$tags[] = $data2['name'];
		}
		$link->tags = $tags;
		
		return $link;
	}
	
	public static function addLink($link)
	{
		$query = "INSERT INTO ".DB::safes(Config::get("mysql_prefix"))."links SET ";
		$query .= "long_url='".DB::safes($link->long_url)."', ";
		$query .= "short_url='".DB::safes($link->short_url)."', ";
		if($link->file != null && strlen($link->file) > 0)
			$query .= "file='".DB::safes($link->file)."', ";
		if($link->file_type != null)
			$query .= "file_type='".DB::safes($link->file_type)."', ";
		if($link->file_size != null)
			$query .= "file_size='".DB::safei($link->file_size)."', ";
		$query .= "type=".DB::safei($link->type).", ";
		$query .= "title='".DB::safes($link->title)."', ";
		$query .= "user=".DB::safei($link->user).", ";
		if($link->password != null && strlen($link->password) > 0)
			$query .= "password='".DB::safes($link->password)."', ";
		$query .= "time_created=now(), ";
		$query .= "time_accessed=now(), ";
		$query .= "count_accessed=0";
		
		Messenger::addDebug("MYSQL: ".$query);

		mysql_query($query);
		$link->id = mysql_insert_id();
		
		foreach($link->tags_new as $tag_name){
			$tag_id = DB::getTagId($tag_name);
			if($tag_id == null)
				$tag_id = DB::createNewTag($tag_name);
			$query_tag = sprintf("INSERT INTO %stags set link_id=%d, tag_id=%d",
			DB::safes(Config::get("mysql_prefix")),
			DB::safei($link->id),
			DB::safei($tag_id));
			Messenger::addDebug("MYSQL: ".$query_tag);
			mysql_query($query_tag);
		}
		$link->tags_new = array();
	}
	
	public static function editLink($link)
	{
		$query = "UPDATE ".DB::safes(Config::get("mysql_prefix"))."links SET ";
		$query .= "long_url='".DB::safes($link->long_url)."', ";
		$query .= "short_url='".DB::safes($link->short_url)."', ";
		$query .= "file='".DB::safes($link->file)."', ";
		$query .= "file_type='".DB::safes($link->file_type)."', ";
		$query .= "file_size='".DB::safei($link->file_size)."', ";
		$query .= "type=".DB::safei($link->type).", ";
		$query .= "title='".DB::safes($link->title)."', ";
			
		if($link->password != null && strlen($link->password) > 0)
			$query .= "password='".DB::safes($link->password)."' ";
		else
			$query .= "password=null ";
		$query .= "WHERE id=".DB::safei($link->id);
		
		Messenger::addDebug("MYSQL: ".$query);

		mysql_query($query);
		
		foreach($link->tags_new as $tag_name){
			$tag_id = DB::getTagId($tag_name);
			if($tag_id == null)
				$tag_id = DB::createNewTag($tag_name);
			$query_tag = sprintf("INSERT INTO %stags set link_id=%d, tag_id=%d",
			DB::safes(Config::get("mysql_prefix")),
			DB::safei($link->id),
			DB::safei($tag_id));
			Messenger::addDebug("MYSQL: ".$query_tag);
			mysql_query($query_tag);
		}
		$link->tags_new = array();
	}
	
	public static function deleteLink($link)
	{
		$query = sprintf("DELETE FROM %slinks where id=%d",
					DB::safes(Config::get("mysql_prefix")),
					DB::safei($link->id));
					
		Messenger::addDebug("MYSQL: ".$query);

		mysql_query($query);
	}
	
	private static function makeLinksQuery($user, $type, $strquery, $tag_name)
	{
		if(!is_int($user))
			$user = -1;
		if(!is_int($type) || $type > 1 || $type < -1)
			$type = -1;
		if(!is_string($strquery) || strlen($strquery) <= 0)
			$strquery = null;
			
		$conditions = array();
		if($user > -1)
			$conditions[] = DB::safes(Config::get("mysql_prefix"))."links.user=".DB::safei($user);
		if($type > -1)
			$conditions[] = DB::safes(Config::get("mysql_prefix"))."links.type=".DB::safei($type);
		if($strquery != null)
			$conditions[] = "(".DB::safes(Config::get("mysql_prefix"))."links.long_url LIKE '%".DB::safes($strquery)."%' OR ".DB::safes(Config::get("mysql_prefix"))."links.short_url LIKE '%".DB::safes($strquery)."%')";	
		
		
		
		
		
		$query = "SELECT ".DB::safes(Config::get("mysql_prefix"))."users.username as username, ".DB::safes(Config::get("mysql_prefix"))."links.* FROM ".DB::safes(Config::get("mysql_prefix"))."links ";
		$query .= "LEFT JOIN ".DB::safes(Config::get("mysql_prefix"))."users ON ".DB::safes(Config::get("mysql_prefix"))."users.id=".DB::safes(Config::get("mysql_prefix"))."links.user ";
		
		if($tag_name != null && strlen(trim($tag_name)) > 0){
			$tag_id = DB::getTagId($tag_name);
			if($tag_id != null){
				$query .= sprintf("JOIN %stags ON %slinks.id = %stags.link_id ",
				DB::safes(Config::get("mysql_prefix")),
				DB::safes(Config::get("mysql_prefix")),
				DB::safes(Config::get("mysql_prefix")));
				$conditions[] = sprintf("%stags.tag_id = %d", DB::safes(Config::get("mysql_prefix")), $tag_id);
			}
		}
		
		$condition_string = null;
		if(count($conditions) > 0)
			$condition_string = implode(" AND ", $conditions);
		if($condition_string != null)
			$query .= "WHERE ".$condition_string." ";
		return $query;
	}
	
	public static function getLinksCount($user, $type, $strquery, $tag_name)
	{
		$query = self::makeLinksQuery($user, $type, $strquery, $tag_name);
		$result = mysql_query($query);
		if(!$result)
			return 0;
		return mysql_num_rows($result);
	}
	
	public static function getLinks($user, $type, $strquery, $tag_name, $order_by, $order_as, $page, $limit)
	{
		$query = self::makeLinksQuery($user, $type, $strquery, $tag_name);

		$order_by = self::checkLinksOrderBy($order_by);
		$order_as = self::checkOrderAs($order_as);
		if(!is_int($page) || $page < 0)
			$page = 0;
		if(!is_int($limit) || $limit < 0)
			$limit = 20;
			
		$query .= "ORDER BY ".DB::safes($order_by)." ".strtoupper(DB::safes($order_as))." ";
		$query .= "LIMIT ".DB::safei($page*$limit).",".DB::safei($limit);

		Messenger::addDebug("MYSQL: ".$query);
		
		$result = mysql_query($query);
		if(!$result || mysql_num_rows($result) == 0)
			return array();

		$links = array();
		while($array = mysql_fetch_array($result))
		{
			$link = self::makeLinkFromArray($array);
			$links[] = $link;
		}
		return $links;
	}

	public static function checkLinksOrderBy($order_by)
	{
		$allowed = array("type", "short_url", "long_url", "username", "time_created", "time_accessed", "count_accessed", "password", "title", "file_size", "file_type");
		if($order_by != null && is_string($order_by) && in_array($order_by, $allowed))
			return $order_by;
		return "time_created";
	}
	
	public static function checkOrderAs($order_as)
	{
		if($order_as != null && is_string($order_as) && ($order_as == "asc" || $order_as == "desc"))
			return $order_as;
		else
			return "desc";
	}
	
	public static function checkUsersOrderBy($order_by)
	{
		if($order_by != null && is_string($order_by) && ($order_by == "username" || $order_by == "email" || $order_by == "username" || $order_by == "allowed_links" || $order_by == "allowed_files" || $order_by == "allowed_admin" || $order_by == "time_created" || $order_by == "time_accessed"))
			return $order_by;
		else
			return "username";
	}
	
	public static function getConfig()
	{
		$query = sprintf("SELECT * FROM %ssettings",
			DB::safes(Config::get("mysql_prefix")));
		$result = mysql_query($query);
		if(!$result || mysql_num_rows($result) == 0)
		{
			//return array();
			die();
		}
			
		$conf_array = array();
		while($array = mysql_fetch_array($result))
		{
			$conf_array[$array['name']] = $array['value'];
		}

		return $conf_array;
	}
	
	public static function saveConfig()
	{
		foreach(array_merge(Config::$conf_boolean, Config::$conf_int, Config::$conf_string) as $update)
		{
			$query = sprintf("UPDATE %ssettings set value='%s' where name='%s'",
			DB::safes(Config::get("mysql_prefix")),
			DB::safes(Config::getString($update)),
			DB::safes($update));
			mysql_query($query);
		}
	}
	
	public static function isShortUrlAvailable($shortUrl)
	{
		$query = sprintf("SELECT * FROM %slinks where short_url='%s'",
			DB::safes(Config::get("mysql_prefix")),
			DB::safes($shortUrl));

		$result = mysql_query($query);
		if(!$result)
			return false;
		if(mysql_num_rows($result) > 0)
			return false;
		return true;
	}
	
	public static function countLinkAccess($link)
	{
		$query = sprintf("UPDATE %slinks set time_accessed=now(), count_accessed=count_accessed+1 where id=%d and short_url='%s'",
			DB::safes(Config::get("mysql_prefix")),
			DB::safei($link->id),
			DB::safes($link->short_url));
		mysql_query($query);
	}
	
	public static function isUsernameAvailable($username)
	{
		$query = sprintf("SELECT * FROM %susers where username='%s'",
			DB::safes(Config::get("mysql_prefix")),
			DB::safes($username));

		$result = mysql_query($query);
		if(!$result)
			return false;
		if(mysql_num_rows($result) > 0)
			return false;
		return true;
	}
	
	public static function isEmailAvailable($email){
		$query = sprintf("SELECT * FROM %susers where email='%s'",
			DB::safes(Config::get("mysql_prefix")),
			DB::safes($email));

		$result = mysql_query($query);

		if(!$result)
			return false;
		if(mysql_num_rows($result) > 0)
			return false;
		return true;
	}
	
	public static function isInstalled(){
		$query = sprintf("SELECT * FROM %susers LIMIT 1",
				DB::safes(Config::get("mysql_prefix")));

		$result = @mysql_query($query);
		if(!$result)
			return false;
		return true;
	}

	public static function getAllTags(){
		$tagquery = sprintf("SELECT * FROM %stagnames order by name",
		DB::safes(Config::get("mysql_prefix")));

		$tagresult = mysql_query($tagquery);
		$tags = array();
		while($tagdata = mysql_fetch_array($tagresult)){
			$tags[] = $tagdata['name'];
		}

		return $tags;
	}
	
	public static function getTagId($tag_name){
		$query = sprintf("SELECT * FROM %stagnames where name='%s'",
		DB::safes(Config::get("mysql_prefix")),
		DB::safes($tag_name));
		
		Messenger::addDebug("MYSQL: ".$query);
		
		$result = mysql_query($query);
		if(!$result || mysql_num_rows($result) == 0)
			return NULL;
		else {
			$data = mysql_fetch_array($result);
			return $data['id'];
		}
	}
	
	public static function createNewTag($tag_name){
		$query = sprintf("INSERT INTO %stagnames SET name='%s'",
		DB::safes(Config::get("mysql_prefix")),
		DB::safes($tag_name));
		
		Messenger::addDebug("MYSQL: ".$query);
		
		$result = mysql_query($query);
		
		return mysql_insert_id();
	}
	
	public static function deleteTag($tag_name, $link_id){
		$query = sprintf("SELECT * FROM %stagnames where name='%s'",
		DB::safes(Config::get("mysql_prefix")),
		DB::safes($tag_name));
		$result = mysql_query($query);
		
		if(!$result || mysql_num_rows($result) == 0)
			return NULL;
		else {
			$data = mysql_fetch_array($result);
			$tag_id = $data['id'];
			
			$query2 = sprintf("DELETE FROM %stags where tag_id=%d and link_id=%d",
			DB::safes(Config::get("mysql_prefix")),
			DB::safei($tag_id),
			DB::safei($link_id));
			Messenger::addDebug("MYSQL: ".$query2);
			mysql_query($query2);
			
			$query3 = sprintf("SELECT * FROM %stags where tag_id=%d",
			DB::safes(Config::get("mysql_prefix")),
			DB::safei($tag_id));
			Messenger::addDebug("MYSQL: ".$query3);
			
			if(mysql_num_rows(mysql_query($query3)) == 0){
				$query4 = sprintf("DELETE FROM %stagnames where id=%d",
				DB::safes(Config::get("mysql_prefix")),
				DB::safei($tag_id));
				Messenger::addDebug("MYSQL: ".$query4);
				mysql_query($query4);
			}
		}
	}
	
	public static function getTagNames(){
		$query = sprintf("SELECT * FROM %stagnames ",
				DB::safes(Config::get("mysql_prefix")));
		
		Messenger::addDebug("MYSQL: ".$query);
		
		$result = mysql_query($query);
		if(!$result || mysql_num_rows($result) == 0)
			return array();
		
		$tagnames = array();
		while($array = mysql_fetch_array($result)){
			$tagnames['name'] = $tagnames['id'];
		}
		return $tagnames;
	}
	
	public static function install($password){
		$query = sprintf("CREATE TABLE IF NOT EXISTS `%ssettings` (
					  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
					  `value` text COLLATE utf8_unicode_ci NOT NULL,
					  PRIMARY KEY (`name`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
					DB::safes(Config::get("mysql_prefix")));
		
		mysql_query($query);
		$query = sprintf("CREATE TABLE IF NOT EXISTS `%slinks` (
					  `id` int(9) NOT NULL AUTO_INCREMENT,
					  `short_url` text COLLATE utf8_unicode_ci NOT NULL,
					  `long_url` text COLLATE utf8_unicode_ci NOT NULL,
					  `title` text COLLATE utf8_unicode_ci NOT NULL,
					  `file` text COLLATE utf8_unicode_ci NOT NULL,
					  `type` int(9) NOT NULL,
					  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
					  `user` int(9) NOT NULL,
					  `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
					  `time_accessed` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `count_accessed` int(9) NOT NULL,
					  `file_size` bigint(32) NOT NULL,
					  `file_type` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
					DB::safes(Config::get("mysql_prefix")));
		mysql_query($query);
		
		$query = sprintf("CREATE TABLE IF NOT EXISTS `%susers` (
					  `id` int(9) NOT NULL AUTO_INCREMENT,
					  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
					  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
					  `email` text COLLATE utf8_unicode_ci NOT NULL,
					  `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
					  `time_accessed` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `allowed_admin` tinyint(1) NOT NULL,
					  `reset_hash` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
					  `reset_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
					DB::safes(Config::get("mysql_prefix")));
		mysql_query($query);

		$query = sprintf("CREATE TABLE IF NOT EXISTS `%stagnames` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `name` varchar(32) NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `id` (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
					DB::safes(Config::get("mysql_prefix")));
		mysql_query($query);

		$query = sprintf("CREATE TABLE IF NOT EXISTS `%stags` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `link_id` int(11) NOT NULL,
				  `tag_id` int(11) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `id` (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;", 
				DB::safes(Config::get("mysql_prefix")));
		mysql_query($query);
		
		$query = sprintf("INSERT INTO `%susers` (`id`, `username`, `password`, `email`, `time_created`, `time_accessed`, `allowed_admin`) VALUES
					(1, 'admin', '%s', '', now(), now(), 1);",
					DB::safes(Config::get("mysql_prefix")),
					DB::safes($password));
		mysql_query($query);
		
		$query = sprintf("INSERT INTO `%ssettings` (`name`, `value`) VALUES
					('allow_links', '1'),
					('allow_files', '1'),
					('items_per_page', '20'),
					('allow_link_passwords', '1'),
					('time_format', 'j M Y h:m'),
					('short_url_length', '2'),
					('short_url_allowed_characters', '1234567890abcdefghijklmnopqrstuvwxyz'),
					('allow_mirror', '1'),
					('always_mirror', '1'),
					('create_mirror_default', '1'),
					('sequential_short_url', ''),
					('debug', '0'),
					('short_url_random', '0'),
					('enable_thumbnails', '1'),
					('allow_tags', '1'),
					('allow_sharing', '1'),
					('default_view', '0');",
					DB::safes(Config::get("mysql_prefix")));
		mysql_query($query);
	}
}
?>
