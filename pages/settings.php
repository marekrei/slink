<?php 
if(isset($_POST['editing']))
{
	if(isset($_POST['allow_links']) && $_POST['allow_links'] != null)
		Config::set("allow_links", 1);
	else
		Config::set("allow_links", 0);
		
	if(isset($_POST['allow_files']) && $_POST['allow_files'] != null)
		Config::set("allow_files", 1);
	else
		Config::set("allow_files", 0);
		
	if(isset($_POST['allow_link_passwords']) && $_POST['allow_link_passwords'] != null)
		Config::set("allow_link_passwords", 1);
	else
		Config::set("allow_link_passwords", 0);
		
	if(isset($_POST['allow_mirror']) && $_POST['allow_mirror'] != null)
		Config::set("allow_mirror", 1);
	else
		Config::set("allow_mirror", 0);
		
	if(isset($_POST['always_mirror']) && $_POST['always_mirror'] != null)
		Config::set("always_mirror", 1);
	else
		Config::set("always_mirror", 0);
		
	if(isset($_POST['create_mirror_default']) && $_POST['create_mirror_default'] != null)
		Config::set("create_mirror_default", 1);
	else
		Config::set("create_mirror_default", 0);
		
	if(isset($_POST['short_url_length']) && $_POST['short_url_length'] != null && intval($_POST['short_url_length']) > 0)
		Config::set("short_url_length", intval($_POST['short_url_length']));
	
	if(isset($_POST['short_url_allowed_characters']) && $_POST['short_url_allowed_characters'] != null && $_POST['short_url_allowed_characters'] > 0)
		Config::set("short_url_allowed_characters", $_POST['short_url_allowed_characters']);
	
	if(isset($_POST['items_per_page']) && $_POST['items_per_page'] != null && intval($_POST['items_per_page']) > 0)
		Config::set("items_per_page", intval($_POST['items_per_page']));
	
	if(isset($_POST['time_format']) && $_POST['time_format'] != null)
		Config::set("time_format", $_POST['time_format']);
		
	if(isset($_POST['debug']) && $_POST['debug'] != null)
		Config::set("debug", 1);
	else
		Config::set("debug", 0);
		
	if(isset($_POST['enable_thumbnails']) && $_POST['enable_thumbnails'] != null)
		Config::set("enable_thumbnails", 1);
	else
		Config::set("enable_thumbnails", 0);
		
	if(isset($_POST['short_url_random']) && $_POST['short_url_random'] != null)
		Config::set("short_url_random", 1);
	else
		Config::set("short_url_random", 0);
		
	
	/*if(isset($_POST['url_prefix']) && $_POST['url_prefix'] != null)
	{
		$url_prefix = $_POST['url_prefix'];
		if(strlen($url_prefix) > 0 && substr($url_prefix, strlen($url_prefix)-1, strlen($url_prefix)) != "/")
			$url_prefix .= "/";
		Config::set("url_prefix", $url_prefix);
	}*/
	DB::saveConfig();
	Messenger::addGood("Settings saved");
}
Messenger::show();
?>
<h2>Settings</h2>
<div id="settings" class="form_layout">
<form action="" method="post" enctype="multipart/form-data">
<?php /* 
	<div>
		<label for="url_prefix">Url prefix:</label> 
		<input type="text" name="url_prefix" value="<?php print Config::get("url_prefix"); ?>" id="url_prefix" />
	</div>
*/ ?>
	<div>
		<label for="allow_links">Allow links:</label> 
		<input type="checkbox" name="allow_links" value="true" id="allow_links" <?php print (Config::get("allow_links")?"checked=\"checked\"":""); ?>/>
	</div>	
	<div>
		<label for="allow_files">Allow files:</label> 
		<input type="checkbox" name="allow_files" value="true" id="allow_files" <?php print (Config::get("allow_files")?"checked=\"checked\"":""); ?>/>
	</div>		
	<div>
		<label for="allow_link_passwords">Allow passwords on links:</label> 
		<input type="checkbox" name="allow_link_passwords" value="true" id="allow_link_passwords" <?php print (Config::get("allow_link_passwords")?"checked=\"checked\"":""); ?>/>
	</div>
	<div>
		<label for="allow_mirror">Allow mirrors:</label> 
		<input type="checkbox" name="allow_mirror" value="true" id="allow_mirror" <?php print (Config::get("allow_mirror")?"checked=\"checked\"":""); ?>/>
	</div>
	<div>
		<label for="allow_mirror">Always mirror:</label> 
		<input type="checkbox" name="always_mirror" value="true" id="always_mirror" <?php print (Config::get("always_mirror")?"checked=\"checked\"":""); ?>/>
	</div>
	<div>
		<label for="create_mirror_default">Mirror by default:</label> 
		<input type="checkbox" name="create_mirror_default" value="true" id="create_mirror_default" <?php print (Config::get("create_mirror_default")?"checked=\"checked\"":""); ?>/>
	</div>
	<div>
		<label for="short_url_random">Random short URL:</label> 
		<input type="checkbox" name="short_url_random" value="true" id="short_url_random" <?php print (Config::get("short_url_random")?"checked=\"checked\"":""); ?>/>
	</div>
	<div>
		<label for="short_url_length">Short URL default length:</label> 
		<input type="text" name="short_url_length" value="<?php print Config::get("short_url_length"); ?>" id="short_url_length" />
	</div>
	<div>
		<label for="short_url_allowed_characters">Characters in short URL:</label> 
		<input type="text" name="short_url_allowed_characters" value="<?php print Config::get("short_url_allowed_characters"); ?>" id="short_url_allowed_characters" />
	</div>
	<div>
		<label for="items_per_page">Items per page:</label> 
		<input type="text" name="items_per_page" value="<?php print Config::get("items_per_page"); ?>" id="items_per_page" />
	</div>
	<div>
		<label for="enable_thumbnails">Enable file thumbnails:</label> 
		<input type="checkbox" name="enable_thumbnails" value="true" id="enable_thumbnails" <?php print (Config::get("enable_thumbnails")?"checked=\"checked\"":""); ?>/>
	</div>
	<div>
		<label for="time_format">Time format:</label> 
		<input type="text" name="time_format" value="<?php print Config::get("time_format"); ?>" id="time_format" />
	</div>
	<div>
		<label for="allow_mirror">Debugging mode:</label> 
		<input type="checkbox" name="debug" value="true" id="debug" <?php print (Config::get("debug")?"checked=\"checked\"":""); ?>/>
	</div>
	<div>
		<label for="version">Version:</label> 
		<div id="version"  style="display:inline;">
			<?php print Config::get("version"); ?>
			<button id="checkForUpdate">Check for update</button>
		</div>
	</div>
	<div>
		<input type="hidden" name="editing" value="true" />
		<input type="submit" value="Save" class="button" />
	</div>
</form>
</div>