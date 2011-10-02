<div id="header">
<a href="index.php" class="logo"><img src="img/slink_logo1.png" alt="Slink" /></a>
<?php 
$_USER = Auth::getUser();
if(Auth::isLoggedIn() && $_USER != null)
{
	print "<a href=\"index.php?page=links\" class=\"menu\">Links</a>";
	if(Auth::isAllowedAdmin())
	{
		print "<a href=\"index.php?page=users\" class=\"menu\">Users</a>";
		print "<a href=\"index.php?page=settings\" class=\"menu\">Settings</a>";
	}
	print "<a href=\"index.php?page=edituser&id=".$_USER->id."\" class=\"menu\">My Profile</a>\n";
	print "<a href=\"index.php?page=logout\" class=\"menu\">Log out</a>\n";
}
else 
{
	print "<a href=\"index.php?page=login\" class=\"menu\">Log in</a>\n";
}
?>

</div>
<div id="content">