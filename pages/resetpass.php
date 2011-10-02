<?php
$enter_new_pass = false;
if(isset($_GET) && isset($_GET['hash']) && strlen($_GET['hash']) > 0)
{
	$user = DB::getUser(null, null, null, $_GET['hash']);
	if($user != null) {
		if($user->reset_time + Config::get("reset_time_limit") > time()) {
			if(isset($_POST['password']) && strlen($_POST['password']) > 0){
				$user->password = md5($_POST['password']);
				DB::editUser($user);
				Messenger::addGood("Password updated. You can now log in.");
			}
			else
				$enter_new_pass = true;
		}
		else
			Messenger::addBad("The password reset link has expired");
	}
	else
		Messenger::addBad("Invalid link for resetting the password");
}
else if(isset($_POST) && isset($_POST['email']) && strlen($_POST['email']) > 0){
	if(!User::isEmailValid($_POST['email'])) {
		Messenger::addBad("Not a valid e-mail");
	}
	else {
		$user = DB::getUser(null, null, $_POST['email'], null);
		if($user == NULL)
			Messenger::addBad("No such e-mail");
		else {
			$hash = md5("".$user->username.time());
			$user->reset_hash = $hash;
			$user->reset_time = time();
			DB::editUser($user);
			
			$url = Config::get("url_prefix")."index.php?page=resetpass&hash=".$hash;
			$message = "Someone requested to reset Your password at ".Config::get("url_prefix")."\n
			To set a new password, go to the following page:\n
			".$url."";
			$email = $user->email;
			$title = "Slink password recovery";
			Messenger::addDebug("Sending password recovery e-mail to ".$email);
			if(mail($email, $title, $message))
				Messenger::addGood("E-mail has been sent to the address");
			else
				Messenger::addBad("Unable to send e-mail");
		}
	}
}

Messenger::show();
?>
<h2>Reset password</h2>
<div id="resetpass" class="form_layout">
<?php 
if($enter_new_pass){
?>
<form action="" method="post" enctype="multipart/form-data">
	<div>
		<label for="password">New password:</label>
		<input type="password" name="password" value="" id="password" />
	</div>
	<div>
		<input type="submit" value="Save" class="button" />
	</div>
</form>
<?php 
}
else {
?>
<form action="" method="post" enctype="multipart/form-data">
	<div>
		<label for="email">E-mail:</label>
		<input type="text" name="email" value="" id="email" />
	</div>
	<div>
		<input type="submit" value="Send message" class="button" />
	</div>
</form>
<?php 
}
?>
</div>