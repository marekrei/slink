<?php 
$_USER = Auth::getUser();
if($_USER != null && $_USER->allowed_admin == true)
{
	if(isset($_GET['del']) && (int)$_GET['del'] > 0)
	{
		DB::delUser((int)$_GET['del']);
	}
	
	$sort_by = "";
	if(isset($_GET['sort_by']))
		$sort_by = DB::checkUsersOrderBy($_GET['sort_by']);
		
	$sort_as = "";
	if(isset($_GET['sort_as']))
		$sort_as = DB::checkOrderAs($_GET['sort_as']);
		
	$pnum = 0;
	if(isset($_GET['pnum']) && (int)$_GET['pnum'] > 0)
		$pnum = (int)$_GET['pnum'];
		
	$items_per_pnum = intval(Config::get("items_per_page"));
?>
<div id="submenu">
<a href="index.php?page=newuser">New User</a>
</div>

<h2>Users</h2>
<div id="users">
<?php 
if(!Auth::isMobile()){
	print "<table class=\"table_layout\">";
	print "<tr class=\"head\">";
	print "<th class=\"username\">";
	
	if($sort_by == "username" && $sort_as == "asc")
		print "<a href=\"?page=users&sort_by=username&sort_as=desc\">Username</a>\n";
	else
		print "<a href=\"?page=users&sort_by=username&sort_as=asc\">Username</a>\n";
	print "</th>";
	
	print "<th class=\"email\">";
	if($sort_by == "email" && $sort_as == "asc")
		print "<a href=\"?page=users&sort_by=email&sort_as=desc\">E-mail</a>\n";
	else
		print "<a href=\"?page=users&sort_by=email&sort_as=asc\">E-mail</a>\n";
		
	print "</th>";
	print "<th class=\"allowed_admin\">";
	if($sort_by == "allowed_admin" && $sort_as == "asc")
		print "<a href=\"?page=users&sort_by=allowed_admin&sort_as=desc\">Admin</a>\n";
	else
		print "<a href=\"?page=users&sort_by=allowed_admin&sort_as=asc\">Admin</a>\n";
	
	print "</th>";
	print "<th class=\"time_created\">";
	if($sort_by == "time_created" && $sort_as == "asc")
		print "<a href=\"?page=users&sort_by=time_created&sort_as=desc\">Created</a>\n";
	else
		print "<a href=\"?page=users&sort_by=time_created&sort_as=asc\">Created</a>\n";
	print "</th>";
	print "<th class=\"functions\">Edit</th>";
	print "</tr>";
}
else {
	print "<ul class=\"list_layout\">";
}

$users = DB::getUsers(null, $sort_by, $sort_as, $pnum, $items_per_pnum);
$row = "one";
foreach($users as $user)
{
	if(!Auth::isMobile()){
		print "<tr class=\"$row\">";
		print "<td>".$user->username."</td>";
		print "<td>".$user->email."</td>";
		if($user->allowed_admin == true)
			print "<td><img src=\"img/icon_yes.png\" alt=\"yes\" /></td>";
		else
			print "<td><img src=\"img/icon_no.png\" alt=\"no\" /></td>";
		print "<td>".date(Config::get("time_format"), $user->time_created)."</td>";
		print "<td><a href=\"?page=edituser&id=".$user->id."\" class=\"edituser\"><img src=\"img/icon_edit3.png\" alt=\"edit\" /></a> <a href=\"?page=users&del=".$user->id."\" class=\"deleteuser\"><img src=\"img/icon_delete3.png\" alt=\"delete\" /></a></td>";
		print "</tr>";
	}
	else {
		print "<li class=\"$row\"><a href=\"index.php?page=edituser&id=".$user->id."\">";
		print "<span class=\"username\">".$user->username."&nbsp;</span>";
		print "<span class=\"email\">".$user->email."&nbsp;</span>";
		print "</a></li>";
	}
	$row = ($row == "one"?"two":"one");
}

if(!Auth::isMobile()){
	print "</table>";
}
else {
	print "</ul>";
}
?>
<div class="pagelinks">
<?php 

	$usersCount = DB::getUsersCount(null);
	$pcount = ceil($usersCount / $items_per_pnum);
	for($i = 0; $i < $pcount; $i++)
	{
		if($i != $pnum)
			print "<a href=\"?page=users&sort_by=".$sort_by."&sort_as=".$sort_as."&pnum=".$i."\">";
		print "<span>".($i+1)."</span>";
		if($i != $pnum)
			print "</a>";
	}
}
?>
</div>
</div>