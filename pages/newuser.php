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
	<div class="row">
		<label for="username">Username:</label> 
		<div class="data"><input type="text" name="username" value="" id="username" /></div>
	</div>		
	<div class="row">
		<label for="password">Password:</label>
		<div class="data"><input type="text" name="password" value="" id="password" /></div>
	</div>
	<div class="row">
		<label for="email">E-mail:</label>
		<div class="data"><input type="text" name="email" id="email" /></div>
	</div>
<!--
	<div class="row">
		<label for="allowed_links">Can add links:</label>
		<div class="data"><input type="checkbox" name="allowed_links" id="allowed_links" value="true" checked="checked" /></div>
	</div>
	<div class="row">
		<label for="allowed_files">Can add files:</label>
		<div class="data"><input type="checkbox" name="allowed_files" value="true" checked="checked" /></div>
	</div>
-->
	<div class="row">
		<label for="allowed_admin">Is admin:</label>
		<div class="data"><input type="checkbox" name="allowed_admin" value="true" id="allowed_admin" /></div>
	</div>
	<div class="row">
		<div class="data">
			<input type="hidden" name="newuser" value="true" />
			<input type="submit" value="Create" class="button" />
		</div>
	</div>
</form>
</div>
<?php 
}
?>
