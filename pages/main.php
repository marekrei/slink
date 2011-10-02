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

	$link->short_url = ShortUrl::generate();

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
	<div>
		<label>Title:</label>
		<?php print $link->title; ?>
	</div>
	<div>
		<label for="short_url_done">URL:</label>
		<input type="text" readonly="readonly" name="short_url_done" value="<?php print Config::get("url_prefix").$link->short_url; ?>" id="short_url_done" />
	</div>
	
<?php 
if($link->isMirrored())
{
?>
	<div>
		<label for="mirror_done">Mirror:</label>
		<input type="text" readonly="readonly" name="mirror_done" value="<?php print Config::get("url_prefix").$link->short_url; ?>!" id="mirror_done" />
	</div>
<?php 
}
?>
<form action="index.php?page=editlink&id=<?php print $link->id; ?>" method="post" enctype="multipart/form-data">
	<div>
		<input type="submit" value="Edit" class="button" />
	</div>
</form>
</div>

<?php 
}
if(!$addedLink)
{
	$newShortUrl = ShortUrl::generateRandom();
?>

<h2>Create new link</h2>
<div id="main" class="form_layout">
<form action="" method="post" enctype="multipart/form-data">
	<div>
<?php 
	if(Config::get("allow_links")){
?>
	<div>
		<label for="long_url">URL:</label>
		<input type="text" name="long_url" value="" id="long_url" />
	</div>
<?php 
	}
	if(Config::get("allow_files")){
?>
	<div>
		<label for="upload_file">Upload file:</label>
		<input type="file" name="upload_file" id="upload_file" /> 
	</div>
<?php 
	}
?>
	<div>
		<label for="short_url">Short URL (opt):</label>
		<input type="text" name="short_url" value="" id="short_url" />
<?php /*
		<input type="text" name="short_url" value="<?php print $newShortUrl; ?>" id="short_url" class="initial" />
		<input type="hidden" id="url_prefix" name="url_prefix" value="<?php print Config::get("url_prefix"); ?>" />
		<input type="hidden" id="generated_short_url" name="generated_short_url" value="<?php print $newShortUrl; ?>" />
		<div id="short_url_preview"></div>
	*/
?>
	</div>
<?php 
	if(Config::get("allow_link_passwords")){
?>
	<div>
		<label for="link_password">Link Password (opt):</label>
		<input type="password" name="link_password" value="" id="link_password" />
	</div>
<?php 
	}
	if(Config::get("allow_mirror") && !Config::get("always_mirror")){
?>
	<div>
		<label for="link_password">Create mirror:</label>
		<input type="radio" name="create_mirror" value="no" <?php print Config::isTrue("create_mirror_default")?"":"checked=\"checked\" "; ?>/> No 
		<input type="radio" name="create_mirror" value="yes" <?php print Config::isTrue("create_mirror_default")?"checked=\"checked\" ":""; ?>/> Yes
	</div>
<?php 
	}
	if(Auth::isLoggedIn())
	{
		$user = Auth::getUser();
?>
	<div>
		<label>Username:</label>
		<?php print $user->username; ?>
	</div>
<?php 
	}
	else 
	{
?>
	<div>
		<label for="username">Username:</label>
		<input type="text" name="username" value="" id="username" />
	</div>
	<div>
		<label for="password">Password:</label>
		<input type="password" name="password" value="" id="password" />
		<input type="checkbox" name="remember_me" value="true" checked="checked" /> Remember me
	</div>
<?php 
	}
?>
	<div>
		<input type="submit" value="Create" class="button" />
		<img src="img/loading.gif" alt="Loading" class="loading hidden" />
	</div>

	
	</div>
</form>
</div>
<?php 
}
?>