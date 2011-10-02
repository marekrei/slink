<?php 
$_USER = Auth::getUser();

if(isset($_GET['page']) && $_GET['page'] == "editlink" && isset($_GET['id']) && is_int((int)$_GET['id']))
{
	$link_id = (int)$_GET['id'];
	$link = DB::getLink($link_id, null);
	
	if($_USER->canEditLink($link))
	{
		if(isset($_POST['editing']))
		{
			if($link->processForm())
			{
				DB::editLink($link);
				Messenger::addGood("Link changed");
			}
		}
		Messenger::show();
?>
<div id="submenu">
<a href="index.php?page=links&del=<?php print $link->id; ?>">Delete</a>
</div>
<h2>Edit link</h2>
<div id="editlink" class="form_layout">
<form action="" method="post" enctype="multipart/form-data">
	<div>
		<label>Current URL:</label> 
		<input type="text" readonly="readonly" name="long_url_done" id="long_url_done" value="<?php print $link->getMainUrl(); ?>" />
		<a href="<?php print $link->getMainUrl(); ?>" target="_blank" ><img src="img/external_link.gif" alt="go" /></a>
	</div>		
	<div>
		<label for="title">Title:</label>
		<input type="text" name="title" value="<?php print $link->title; ?>" id="title" />
	</div>
<?php 
if(Config::get("allow_links")) {
?>
	<div>
		<label for="long_url">New URL:</label>
		<input type="text" name="long_url" value="" id="long_url" />
	</div>
<?php 
}
if(Config::get("allow_files")) {
?>
	<div>
		<label for="upload_file">New file:</label>
		<input type="file" name="upload_file" id="upload_file" /> 
	</div>
<?php 
}
?>
	<div>
		<label for="short_url">Short URL:</label>
		<input type="text" name="short_url" value="<?php print $link->short_url; ?>" id="short_url" />
		<a href="<?php print $link->getShortUrl(); ?>" target="_blank" ><img src="img/external_link.gif" alt="go" /></a>
		<!-- <input type="hidden" id="url_prefix" name="url_prefix" value="<?php print Config::get("url_prefix"); ?>" />
		<div id="short_url_preview"></div> -->
	</div>
<?php 
if(Config::get("allow_mirror") && !Config::get("always_mirror")){
?>
	<div>
		<label for="link_password">Create mirror:</label>
		<input type="radio" name="create_mirror" value="no" <?php print Config::isTrue("create_mirror_default")?"":"checked=\"checked\" "; ?>/> No 
		<input type="radio" name="create_mirror" value="yes" <?php print Config::isTrue("create_mirror_default")?"checked=\"checked\" ":""; ?>/> Yes
	</div>
<?php 
}
if(Config::get("allow_link_passwords")) {
?>
	<div>
		<label for="password_protected">Password protected:</label>
		<input type="radio" name="password_protected" value="no" <?php if(!$link->isProtected()) print "checked "; ?>/> No <input type="radio" name="password_protected" value="yes" <?php if($link->isProtected()) print "checked "; ?>/> Yes
	</div>
	<div>
		<label for="link_password">New password:</label>
		<input type="text" name="link_password" value="" id="link_password" />
	</div>
<?php 
}
?>
	<div>
		<label>Username:</label>
		<?php print $link->username; ?>
	</div>
	<div>
		<input type="hidden" name="editing" value="true" />
		<input type="submit" value="Save" class="button" />
	</div>
</form>
</div>
<?php
	}
}
?>