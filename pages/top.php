<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta name="viewport" content="width=device-width" />
<meta content="text/html; charset=UTF-8" http-equiv="content-type" />
<link rel="stylesheet" type="text/css" href="style.css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
<script src="js/main.js" type="text/javascript"></script>
<title>Slink</title>
</head>
<?php 
if(isset($_SESSION['mobile']) && $_SESSION['mobile'] == true)
	print "<body class=\"mobile\">";
else
	print "<body>";
?>
<div id="frame">