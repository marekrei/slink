</div>
</div>
<?php 
if(!Auth::isMobile())
	print "<a href=\"index.php?mobile=true\">Mobile view</a>";
else
	print "<a href=\"index.php?mobile=false\">Standard view</a>";
	
if(Auth::isLoggedIn())
{
	$_USER = Auth::getUser();
	print " | <a href=\"index.php?page=edituser&id=".$_USER->id."\">".$_USER->username."</a>";
}
?>
 | <a href="#">Slink</a>
</body>
</html>