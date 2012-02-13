<?php
$_USER = Auth::getUser();
/*if($_USER == null && isset($_POST['username']) && isset($_POST['password']) && !isset($_POST['remember_me']))
{
	$_USER = Auth::getUserByUsername($_POST['username'], $_POST['password']);
	if($_USER == null)
		Messenger::addBad("Username or password incorrect");
}*/

$link = new Link();
$addedLink = false;
if($_USER != null && (isset($_POST['long_url']) || isset($_FILES['upload_file'])))
{
	$link->user = $_USER->id;
	$link->username = $_USER->username;

	if($link->processForm())
	{
		DB::addLink($link);
		Messenger::addGood("Link successfully added");
		$addedLink = true;
	}
}
Messenger::show();

if($addedLink)
{
?>
<h2>Your link</h2>
<div id="main_done" class="form_layout">
	<div class="row">
		<label>Title:</label>
		<div class="data"><?php print $link->title; ?></div>
	</div>
	<div class="row">
		<label for="short_url_done">URL:</label>
		<div class="data">		
			<input type="text" readonly="readonly" name="short_url_done" value="<?php print Config::get("url_prefix").$link->short_url; ?>" id="short_url_done" />
		</div>
	</div>
	
<?php 
if($link->isMirrored())
{
?>
	<div class="row">
		<label for="mirror_done">Mirror:</label>
		<div class="data">
			<input type="text" readonly="readonly" name="mirror_done" value="<?php print Config::get("url_prefix").$link->short_url; ?>!" id="mirror_done" />
		</div>
	</div>
<?php 
}
?>
<form action="index.php?page=editlink&id=<?php print $link->id; ?>" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="data"><input type="submit" value="Edit" class="button" /></div>
	</div>
<?php
if(Config::get("allow_sharing")){
?>
	<div class="row" id="sharebar">
		<div class="data">
		<a href="http://www.facebook.com/sharer/sharer.php?u=<?php print rawurlencode(Config::get("url_prefix").$link->short_url); ?>" target="_blank"><img src="img/media/facebook.png" alt="Facebook" /></a>
		<a href="http://twitter.com/home?status=<?php print rawurlencode(Config::get("url_prefix").$link->short_url); ?>" target="_blank"><img src="img/media/twitter.png" alt="Twitter" /></a>
		<a href="mailto:?subject=<?php print rawurlencode($link->title);?>&body=<?php print rawurlencode($link->title);?>
 <?php print rawurlencode(Config::get("url_prefix").$link->short_url);?>" target="_blank"><img src="img/media/mail.png" alt="E-Mail" /></a>
		</div>
	</div>
<?php
}
?>
</form>
</div>

<?php 
}
if(!$addedLink)
{
	$newShortUrl = ShortUrl::generateRandom();
?>

<h2>Create new link</h2>

<form action="" method="post" enctype="multipart/form-data">
<div id="main" class="form_layout">
<?php 
	if(Config::get("allow_links")){
		$value_long_url = null;
		if(isset($_GET['u']) && strlen($_GET['u']) > 0)
			$value_long_url = htmlspecialchars($_GET['u']);
		else if(isset($_POST['long_url']) && strlen($_POST['long_url']) > 0)
			$value_long_url = htmlspecialchars($_POST['long_url']);
		
		$value_short_url = null;
		if(isset($_POST['short_url']) && strlen($_POST['short_url']) > 0)
			$value_short_url = htmlspecialchars($_POST['short_url']);
		
		$value_link_password = null;
		if(isset($_POST['link_password']) && strlen($_POST['link_password']) > 0)
			$value_link_password = htmlspecialchars($_POST['link_password']);
		
		$value_link_tags = null;
		if(isset($_POST['link_tags']) && strlen($_POST['link_tags']) > 0)
		$value_link_tags = htmlspecialchars($_POST['link_tags']);
			
?>
	<div class="row">
		<label for="long_url">URL:</label>
		<div class="data"><input type="text" name="long_url" value="<?php print $value_long_url!=null?$value_long_url:""; ?>" id="long_url" /></div>
	</div>
<?php 
	}
	if(Config::get("allow_files")){
?>
	<div class="row">
		<label for="upload_file">Upload file:</label>
		<div class="data"><input type="file" name="upload_file" id="upload_file" /></div>
	</div>
<?php 
	}
?>
	<div class="row">
		<label for="short_url">Short URL (opt):</label>
		<div class="data"><input type="text" name="short_url" value="<?php print $value_short_url!=null?$value_short_url:""; ?>" id="short_url" />
<?php /*
		<input type="text" name="short_url" value="<?php print $newShortUrl; ?>" id="short_url" class="initial" />
		<input type="hidden" id="url_prefix" name="url_prefix" value="<?php print Config::get("url_prefix"); ?>" />
		<input type="hidden" id="generated_short_url" name="generated_short_url" value="<?php print $newShortUrl; ?>" />
		<div id="short_url_preview"></div>
	*/
?>
		</div>
	</div>
<?php 
	if(Config::get("allow_link_passwords")){
?>
	<div class="row">
		<label for="link_password">Link Password (opt):</label>
		<div class="data">
			<input type="password" name="link_password" value="<?php print $value_link_password!=null?$value_link_password:""; ?>" id="link_password" />
		</div>
	</div>
<?php 
	}
	if(Config::get("allow_tags")){
?>
	<div class="row">
		<label for="link_tags">Tags:</label>
		<div class="data"><input type="text" name="link_tags" value="<?php print $value_link_tags!=null?$value_link_tags:""; ?>" id="link_tags" /></div>
	</div>
<?php 
	}
	if(Config::get("allow_mirror") && !Config::get("always_mirror")){
?>
	<div class="row">
		<label for="link_password">Create mirror:</label>
		<div class="data">
			<input type="radio" name="create_mirror" value="no" <?php print Config::isTrue("create_mirror_default")?"":"checked=\"checked\" "; ?>/> No 
			<input type="radio" name="create_mirror" value="yes" <?php print Config::isTrue("create_mirror_default")?"checked=\"checked\" ":""; ?>/> Yes
		</div>
	</div>
<?php 
	}
	if(Auth::isLoggedIn())
	{
		$user = Auth::getUser();
?>
	<div class="row">
		<label>Username:</label>
		<div class="data"><?php print $user->username; ?></div>
	</div>
<?php 
	}
	else 
	{
?>
	<div class="row">
		<label for="username">Username:</label>
		<div class="data"><input type="text" name="username" value="" id="username" /></div>
	</div>
	<div class="row">
		<label for="password">Password:</label>
		<div class="data">
			<input type="password" name="password" value="" id="password" />
			<input type="checkbox" name="remember_me" value="true" checked="checked" /> Remember me
		</div>
	</div>
<?php 
	}
?>
	<div class="row">
		<div class="data">
			<input type="submit" value="Create" class="button" />
			<img src="img/loading.gif" alt="Loading" class="loading hidden" />
		</div>
	</div>
</div>
</form>

<?php 
}
?>
