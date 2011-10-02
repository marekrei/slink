<?php 
$_USER = Auth::getUser();
if($_USER != null && $_USER->allowed_admin)
{
	if(isset($_POST['newuser']))
	{
		$user = new User();
		$user->processForm();
		if($user->password == null || strlen($user->password) == 0)
			Messenger::addBad("Password needs to be set");
		else if(!User::isUsernameValid($user->username))
			Messenger::addBad("Username contains illegal characters");
		else if(!User::isUsernameAvailable($user->username))
			Messenger::addBad("Username is already taken");
		else if($user->email != null && strlen($user->email) > 0 && !User::isEmailValid($user->email))
			Messenger::addBad("Email address is invalid");
		else if($user->email != null && strlen($user->email) > 0 && !User::isEmailAvailable($user->email))
			Messenger::addBad("Email address is already in use");
		else
		{
			DB::addUser($user);
			FileManager::initUser($user->username);
			Messenger::addGood("New user added");
		}
	}
	Messenger::show();
?>
<h2>New user</h2>
<div id="newuser" class="form_layout">
<form action="" method="post" enctype="multipart/form-data">	
	<div>
		<label for="username">Username:</label> 
		<input type="text" name="username" value="" id="username" />
	</div>		
	<div>
		<label for="password">Password:</label>
		<input type="text" name="password" value="" id="password" />
	</div>
	<div>
		<label for="email">E-mail:</label>
		<input type="text" name="email" id="email" /> 
	</div>
<!--
	<div>
		<label for="allowed_links">Can add links:</label>
		<input type="checkbox" name="allowed_links" id="allowed_links" value="true" checked="checked" />
	</div>
	<div>
		<label for="allowed_files">Can add files:</label>
		<input type="checkbox" name="allowed_files" value="true" checked="checked" />
	</div>
-->
	<div>
		<label for="allowed_admin">Is admin:</label>
		<input type="checkbox" name="allowed_admin" value="true" id="allowed_admin" />
	</div>
	<div>
		<input type="hidden" name="newuser" value="true" />
		<input type="submit" value="Create" class="button" />
	</div>
</form>
</div>
<?php 
}
?>