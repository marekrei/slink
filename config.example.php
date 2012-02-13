<?php
$_CONFIG = array();
$_CONFIG['mysql_host'] = "mysqlhost";
$_CONFIG['mysql_db'] = "mysqldb";
$_CONFIG['mysql_username'] = "mysqluser";
$_CONFIG['mysql_password'] = "mysqlpass";
$_CONFIG['mysql_prefix'] = "slink_";


$_CONFIG['timezone'] = "Europe/London"; // The default timezone - list of available time zones can be found at http://php.net/manual/en/timezones.php
$_CONFIG['main_path'] = realpath(dirname(__FILE__)."/")."/";
$_CONFIG['reject_extensions'] = "php";
$_CONFIG['update_check_url'] = "http://www.marekrei.com/slink/version.php";
$_CONFIG['version'] = 0.6;
$_CONFIG['cookie_prefix'] = "slink_";
$_CONFIG['reset_time_limit'] = 60*60*24;
$_CONFIG['url_prefix'] = "http://www.domain.com/subdir/"; // The URL where the script is being installed
?>