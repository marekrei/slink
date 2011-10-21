<?php 
Messenger::show();
?>

<h2>Protected Link</h2>

<div id="gatekeeper" class="form_layout">
<form action="index.php<?php if(isset($_GET['l'])) print "?l=".$_GET['l']; ?>" method="post" enctype="multipart/form-data">
	<div class="row">
		<label for="password">Password:</label>
		<div class="data"><input type="password" name="password" value="" id="password" /></div>
	</div>
	<div class="row">
		<div class="data"><input type="submit" value="Enter" class="button" /></div>
	</div>
</form>


</div>
