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
		else if(isset($_GET['deltag']) && strlen($_GET['deltag']) > 0){
			DB::deleteTag($_GET['deltag'], $link->id);
			foreach ($link->tags as $key => $value){
				if ($value == $_GET['deltag'])
				unset($link->tags[$key]);
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
	<div class="row">
		<label>Current URL:</label> 
		<div class="data">
			<input type="text" readonly="readonly" name="long_url_done" id="long_url_done" value="<?php print $link->getMainUrl(); ?>" />
			<a href="<?php print $link->getMainUrl(); ?>" target="_blank" ><img src="img/external_link.gif" alt="go" /></a>
		</div>
	</div>		
	<div class="row">
		<label for="title">Title:</label>
		<div class="data"><input type="text" name="title" value="<?php print $link->title; ?>" id="title" /></div>
	</div>
<?php 
if(Config::get("allow_links")) {
?>
	<div class="row">
		<label for="long_url">New URL:</label>
		<div class="data"><input type="text" name="long_url" value="" id="long_url" /></div>
	</div>
<?php 
}
if(Config::get("allow_files")) {
?>
	<div class="row">
		<label for="upload_file">New file:</label>
		<div class="data"><input type="file" name="upload_file" id="upload_file" /></div>
	</div>
<?php 
}
?>
	<div class="row">
		<label for="short_url">Short URL:</label>
		<div class="data">
			<input type="text" name="short_url" value="<?php print $link->short_url; ?>" id="short_url" />
			<a href="<?php print $link->getShortUrl(); ?>" target="_blank" ><img src="img/external_link.gif" alt="go" /></a>
			<!-- <input type="hidden" id="url_prefix" name="url_prefix" value="<?php print Config::get("url_prefix"); ?>" />
			<div id="short_url_preview"></div> -->
		</div>
	</div>
<?php 
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
if(Config::get("allow_link_passwords")) {
?>
	<div class="row">
		<label for="password_protected">Password protected:</label>
		<div class="data">
			<input type="radio" name="password_protected" value="no" <?php if(!$link->isProtected()) print "checked "; ?>/> No <input type="radio" name="password_protected" value="yes" <?php if($link->isProtected()) print "checked "; ?>/> Yes
		</div>
	</div>
	<div class="row">
		<label for="link_password">New password:</label>
		<div class="data"><input type="password" name="link_password" value="" id="link_password" /></div>
	</div>
<?php 
}
if(Config::get("allow_tags")){
	?>
	<div class="row">
		<label for="link_tags">Tags:</label>
		<div class="data">
			<input type="text" name="link_tags" value="" id="link_tags" />
<?php 
if(count($link->tags) > 0)
	print "<br />";
foreach($link->tags as $tag){
	print "<div class=\"deltag\">".htmlspecialchars($tag)."<a href=\"?page=editlink&id=".$link->id."&deltag=".rawurlencode($tag)."\">X</a></div> ";	
}
?>
		</div>
	</div>
<?php 
	}
?>
	<div class="row">
		<label>Username:</label>
		<div class="data"><?php print $link->username; ?></div>
	</div>
	<div class="row">
		<div class="data">
			<input type="hidden" name="editing" value="true" />
			<input type="submit" value="Save" class="button" />
		</div>
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
}
?>
