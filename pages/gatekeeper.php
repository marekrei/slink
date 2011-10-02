<?php 
Messenger::show();
?>

<h2>Protected Link</h2>

<div id="gatekeeper" class="form_layout">
<form action="index.php<?php if(isset($_GET['l'])) print "?l=".$_GET['l']; ?>" method="post" enctype="multipart/form-data">
	<div>
		<label for="password">Password:</label>
		<input type="password" name="password" value="" id="password" />
	</div>
	<div>
		<input type="submit" value="Enter" class="button" />
	</div>
</form>


</div>