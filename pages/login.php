<?php 
Messenger::show();
?>
<h2>Log in</h2>
<div id="login" class="form_layout">

<form action="index.php?page=login" method="post" enctype="multipart/form-data">
	
	<div>
		<label for="username">Username:</label>
		<input type="text" name="username" value="" tabindex="1" id="username" />
		<input type="checkbox" class="checkbox" name="remember_me" value="true" checked="checked"  tabindex="3" /> Remember me
	</div>
	<div>
		<label for="password">Password:</label>
		<input type="password" name="password" value="" id="password"  tabindex="2" />
		<a href="index.php?page=resetpass" tabindex="4">Forgotten password</a>
	</div>
	<div>
		<input type="submit" value="Log in" class="button" tabindex="5" />
	</div>
</form>


</div>