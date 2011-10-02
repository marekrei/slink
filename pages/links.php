<?php 
function formatFileSize($size) 
{
	$sizes = Array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
	$y = $sizes[0];
	for ($i = 1; (($i < count($sizes)) && ($size >= 1024)); $i++) 
	{
		$size = $size / 1024;
		$y  = $sizes[$i];
	}
	return round($size, 2)." ".$y;
}
	
$_USER = Auth::getUser();
if($_USER != null)
{
	if(isset($_GET['del']) && (int)$_GET['del'] > 0)
	{
		$link = DB::getLink(intval($_GET['del']), null);
		if($link == null)
			;//Messenger::addBad("The link you're trying to delete doesn't exist.");
		else if($_USER->canDeleteLink($link))
		{
			DB::deleteLink($link);
			FileManager::deleteLink($link);
			Messenger::addGood("Link deleted");
		}
		else
			Messenger::addBad("You do not have permissions to delete this link");
	}
	
	$sort_by = "";
	if(isset($_GET['sort_by']))
		$sort_by = DB::checkLinksOrderBy($_GET['sort_by']);
		
	$sort_as = "";
	if(isset($_GET['sort_as']))
		$sort_as = DB::checkOrderAs($_GET['sort_as']);
		
	$pnum = 0;
	if(isset($_GET['pnum']) && (int)$_GET['pnum'] > 0)
		$pnum = (int)$_GET['pnum'];
		
	$items_per_pnum = intval(Config::get("items_per_page"));
	
	$user_id = -1;
	if(isset($_GET['user']) && is_numeric($_GET['user']) && ($_USER->allowed_admin || intval($_GET['user']) == $_USER->id))
		$user_id = intval($_GET['user']);
	if(!$_USER->allowed_admin)
		$user_id = $_USER->id;
		
	$type = -1;
	if(isset($_GET['type']) && (intval($_GET['type']) == 0 || intval($_GET['type'] == 1)))
		$type =  intval($_GET['type']);
		
?>
<div id="submenu">
<?php 
if(Auth::isAllowedAdmin())
	print "<a href=\"index.php?page=links&user=-1&type=-1\">All links</a>";
print "<a href=\"index.php?page=links&user=".$_USER->id."&type=-1\">My links</a>";
if(Auth::isAllowedAdmin())
	print "<a href=\"index.php?page=links&user=-1&type=1\">All files</a>";
print "<a href=\"index.php?page=links&user=".$_USER->id."&type=1\">My files</a>";
?>
</div>
<?php 
 Messenger::show();
print "<h2>";
if($user_id == -1)
	print "All ";
else if($user_id == $_USER->id)
	print "My ";
else
	print "User ";
	
if($type == 1)
	print "files";
else
	print "links";
print "</h2>";
?>

<div id="links">

<?php 
if(!Auth::isMobile()){
	print "<table class=\"table_layout\">";
	print "<tr class=\"head\">";
	
	if($type == 1) {
		print "<th class=\"file_type\">";
		if($sort_by == "file_type" && $sort_as == "asc")
			print "<a href=\"?page=links&user=$user_id&type=$type&sort_by=file_type&sort_as=desc\">Type</a>";
		else
			print "<a href=\"?page=links&user=$user_id&type=$type&sort_by=file_type&sort_as=asc\">Type</a>";
		print "</th>";
	}
	else {
		print "<th class=\"type\">";
		if($sort_by == "type" && $sort_as == "asc")
			print "<a href=\"?page=links&user=$user_id&type=$type&sort_by=type&sort_as=desc\">Type</a>";
		else
			print "<a href=\"?page=links&user=$user_id&type=$type&sort_by=type&sort_as=asc\">Type</a>";
		print "</th>";
	}

	print "<th class=\"long_url\">";
	if($sort_by == "title" && $sort_as == "asc")
		print "<a href=\"?page=links&user=$user_id&type=$type&sort_by=title&sort_as=desc\">Link</a>";
	else
		print "<a href=\"?page=links&user=$user_id&type=$type&sort_by=title&sort_as=asc\">Link</a>";
	print "</th>";
	
	print "<th class=\"short_url\">";
	if($sort_by == "short_url" && $sort_as == "asc")
		print "<a href=\"?page=links&user=$user_id&type=$type&sort_by=short_url&sort_as=desc\">Short link</a>";
	else
		print "<a href=\"?page=links&user=$user_id&type=$type&sort_by=short_url&sort_as=asc\">Short link</a>";
	print "</th>";
	
	if($type == 1){
		print "<th class=\"file_size\">";
		if($sort_by == "file_size" && $sort_as == "asc")
			print "<a href=\"?page=links&user=$user_id&type=$type&sort_by=file_size&sort_as=desc\">Size</a>";
		else
			print "<a href=\"?page=links&user=$user_id&type=$type&sort_by=file_size&sort_as=asc\">Size</a>";
		print "</th>";
	}
	else if($type != 1) {
		print "<th class=\"password\">"; 
		if($sort_by == "password" && $sort_as == "asc")
			print "<a href=\"?page=links&user=$user_id&type=$type&sort_by=password&sort_as=desc\">PW</a>";
		else
			print "<a href=\"?page=links&user=$user_id&type=$type&sort_by=password&sort_as=asc\">PW</a>";
		print "</th>";
	}
	print "<th class=\"username\">";
	if($sort_by == "username" && $sort_as == "asc")
		print "<a href=\"?page=links&user=$user_id&type=$type&sort_by=username&sort_as=desc\">User</a>";
	else
		print "<a href=\"?page=links&user=$user_id&type=$type&sort_by=username&sort_as=asc\">User</a>";
	print "</th>";
	
	print "<th class=\"time_created\">";
	if($sort_by == "time_created" && $sort_as == "asc")
		print "<a href=\"?page=links&user=$user_id&type=$type&sort_by=time_created&sort_as=desc\">Created</a>";
	else
		print "<a href=\"?page=links&user=$user_id&type=$type&sort_by=time_created&sort_as=asc\">Created</a>";
	print "</th>";
	
	print "<th class=\"functions\">Edit</th>";
	print "</tr>";
}
else {
	print "<ul class=\"list_layout\">";
}
	



$links = DB::getLinks($user_id, $type, null, $sort_by, $sort_as, $pnum, $items_per_pnum);
$row = "one";
foreach($links as $link)
{
	if(!Auth::isMobile()){
		print "<tr class=\"$row\">";
		if($link->type == 1)
			print "<td><img src=\"".ImageServer::getIcon($link->file_type)."\" alt=\"".$link->file_type."\" /></td>";
		else 
			print "<td><img src=\"img/icon_link.png\" alt=\"link\" /></td>";
	
		print "<td><a class=\"title".($link->isValidForThumb()?" thumb":"")."\" href=\"".$link->getMainUrl()."\">".$link->title."</a>
				<a class=\"long_url".($link->isValidForThumb()?" thumb":"")."\" href=\"".$link->getMainUrl()."\">".$link->getMainUrl()."</a></td>";
		print "<td><a href=\"".Config::get("url_prefix").$link->short_url."\">".$link->short_url."</td>";
		
		if($type == 1) {
			print "<td>".formatFileSize($link->file_size)."</td>";
		}
		
		if($type != 1) {
			if($link->password != null && strlen($link->password) > 0)
				print "<td><img src=\"img/icon_passworded.png\" alt=\"passworded\" /></td>";
			else
				print "<td><img src=\"img/icon_public.png\" alt=\"public\" /></td>";
		}
		print "<td>".$link->username."</td>";
		print "<td>".date(Config::get("time_format"), $link->time_created)."</td>";
		print "<td><a href=\"?page=editlink&id=".$link->id."\" class=\"editlink\"><img src=\"img/icon_edit3.png\" alt=\"edit\" /></a> <a href=\"?page=links&user=$user_id&type=$type&del=".$link->id."\"  class=\"deletelink\"><img src=\"img/icon_delete3.png\" alt=\"delete\" /></a><span class=\"hidden link_id\">".$link->id."</span></td>";
		print "</tr>";
	}
	else {
		print "<li class=\"$row\"><a href=\"?page=editlink&id=".$link->id."\">";
		print "<img src=\"".ImageServer::getIcon($link->file_type)."\" alt=\"".$link->file_type."\" />";
		print "<span class=\"title\">".$link->title;
		if($link->type == 1)
			print " (".formatFileSize($link->file_size).")";
		print "</span>";
		print "<span class=\"short_url\">Short URL: ".$link->short_url."</span>";
		print "<span class=\"username\">".$link->username.", </span>";
		print "<span class=\"time_created\">".date(Config::get("time_format"), $link->time_created)."</span>";
		
		print "</a></li>";
	}
	$row = ($row == "one"?"two":"one");
}



if(!Auth::isMobile()){
	print "</table>";
}
else
	print "</ul>";
?>
<div class="pagelinks">
<?php 
	$linksCount = DB::getLinksCount($user_id, $type, null, $sort_by, $sort_as);
	$pcount = ceil($linksCount / $items_per_pnum);
	for($i = 0; $i < $pcount; $i++)
	{
		if($i != $pnum)
			print "<a href=\"?page=links&user=$user_id&type=$type&sort_by=".$sort_by."&sort_as=".$sort_as."&pnum=".$i."\">";
		print "<span>".($i+1)."</span>";
		if($i != $pnum)
			print "</a>";
	}
}
?>
</div>
</div>