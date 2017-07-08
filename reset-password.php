<?php
if(!empty($_GET["key"])) {
/*
  RESET-PASSWORD.php
  VERSION 1.0
*/
$pageTitle = 'Reset Password';
include("./system/config.php");
include("./layout/header.php");
include("./layout/navbar.php");
//if logged in redirect to users page
if($user->is_logged_in()){ header('Location: ./clientarea'); }
$stmt = $conn->prepare('SELECT `resetComplete` FROM `users` WHERE `resetToken` = ?');
$stmt->bind_param("s", $_GET['key']);
$stmt->execute();
$stmt->bind_result($row);
//if no token from conn then kill the page
while ($stmt->fetch()) {
	if(empty($row)){
		$stop = 'Invalid token provided, please use the link provided in the reset email.';
		break; //stop the while loop
	} elseif($row == 'Yes') {
		$stop = 'Your password has already been changed!';
	}
}
//if form has been submitted process it
if(isset($_POST['submit'])){
	//basic validation
	if(strlen($_POST['password']) < 3){
		$error[] = 'Password is too short.';
	}
	if(strlen($_POST['passwordConfirm']) < 3){
		$error[] = 'Confirm password is too short.';
	}
	if($_POST['password'] != $_POST['passwordConfirm']){
		$error[] = 'Passwords do not match.';
	}
	//if no errors have been created carry on
	if(!isset($error)){
		//hash the password
		$hashedpassword = $user->password_hash($_POST['password'], PASSWORD_BCRYPT);
		$stmt = $conn->prepare("UPDATE users SET password = ?, resetComplete = 'Yes'  WHERE resetToken = ?");
		$stmt->bind_param("ss", $hashedpassword, $_GET['key']);
		$stmt->execute();
		//redirect to index page
		header('Location: /sign-in/a/resetAccount');
		exit;
	}
}
?>
<div id="sub-header">
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<h1>Password Reset</h1>
			</div>
		</div>
	</div>
</div>
<div class="container">
	<div class="row">
		<? if(!$role->premium()){ echo $ccAdvert->ccAdvert("top", null); } ?>
	</div>
</div>
<div id="reset-password">
	<div class="container">
		<h2 class="header">Reset Password</h2>
		<p>Enter your new password.</p>
		<div class="row">
		    <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
		    	<?php if(isset($stop)){
		    		echo "<div class='alert alert-danger'>$stop</div>";
		    		} else {
					?>
					<form role="form" method="post" action="" autocomplete="off">
						<?php
						//check for any errors
						if(isset($error)){
							foreach($error as $error){
								echo '<div class="alert alert-danger" role="alert">'.$error.'</p></div>';
							}
						}
						//check the action
						switch ($_GET['action']) {
							case 'active':
								echo '<div class="alert alert-success" role="alert">Your account is now active you may now log in.</div>';
								break;
							case 'reset':
								echo '<div class="alert alert-success" role="alert">Please check your inbox for a reset link.</div>';
								break;
						}
						?>
						<div class="row">
							<div class="col-xs-12">
								<div class="form-group">
									<input type="password" name="password" id="password" class="form-control input-lg" placeholder="Password" tabindex="1">
								</div>
							</div>
							<div class="col-xs-12">
								<div class="form-group">
									<input type="password" name="passwordConfirm" id="passwordConfirm" class="form-control input-lg" placeholder="Confirm Password" tabindex="1">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-6 col-md-6 col-md-offset-3"><input type="submit" name="submit" value="Change Password" class="btn btn-primary btn-block btn-lg" tabindex="3"></div>
						</div>
					</form>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<?php
include("./layout/footer.php");
} else {
	header("Location: ./");
}
?>
