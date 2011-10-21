</div>
</div>
<?php 
if(!Auth::isMobile())
	print "<a href=\"index.php?mobile=true\">Go to Mobile View</a>";
else
	print "<a href=\"index.php?mobile=false\">Go to Desktop View</a>";
	
/*
if(Auth::isLoggedIn())
{
	$_USER = Auth::getUser();
	print " | <a href=\"index.php?page=edituser&id=".$_USER->id."\">".$_USER->username."</a>";
}
*/
?>
 | <a href="#">Slink</a>
</body>
</html>