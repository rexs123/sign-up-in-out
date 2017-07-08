<?php
/*
  SIGN-IN.php
  VERSION 1.0
*/
$pageTitle = 'Sign-in';
include("./system/config.php");
include("./layout/header.php");
include("./layout/navbar.php");
//check if already logged in move to home page
if( $user->signedin() ){ header('Location: ./clientarea'); }
//process login form if submitted
$returned = $_POST['return'];
if(isset($_POST['submit'])){
	$username = $_POST['username'];
	$password = $_POST['password'];

	if($user->signin($username, $password, $remember)){
		$_SESSION['username'] = $username;
		if(!empty($returned)) {
			header("Location: /".$_POST["return"]);
			exit;
		} else {
			header("Location: /clientarea");
			exit;
		}
	} else {
		$error[] = 'Wrong username or password or your account has not been activated.';
	}
}//end if submit
if(isset($error)){
  foreach($error as $error){
    $msg = '<div class="alert alert-danger" role="alert">'.$error.'</div>';
  }
}
if(isset($_GET['action'])) {
  switch ($_GET['action']) {
		case 'active':
			$msg = '<div class="alert alert-success" role="alert">Your account is now active. You may now log in.</div>';
			break;
		case 'reset':
			$msg = '<div class="alert alert-warning" role="alert">Please check your inbox for a reset link.</div>';
			break;
		case 'resetAccount':
			$msg = '<div class="alert alert-success" role="alert">Password changed. You may now login.</div>';
			break;
		case 'joined':
			$msg = "<div class='alert alert-warning'>Registration successful. Please check your email to activate your account.</div>";
			break;
		case 'failed':
			$msg = "<div class='alert alert-danger'>Registration failed! Please contact us.</div>";
			break;
  }
}
?>
<div id="sign-in">
	<div class="container">
		<div class="row">
		  <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
	      <?php echo $msg; ?>
	      <form role="form" method="POST" action="<? echo $_SERVER['PHP_SELF'];?>" autocomplete="off">
					<input type="hidden" name="return" id="return" class="form-control input-sm" value="<?php echo $_GET['return']; ?>" tabindex="1"><br>

	      	<div class="form-group">
	        	<input type="text" name="username" id="username" class="form-control input-lg" placeholder="Enter your username" value="<?php if(isset($error)){ echo $_POST['username']; } ?>" tabindex="1">
	      	</div>
	      	<div class="form-group">
	        	<input type="password" name="password" id="password" class="form-control input-lg" placeholder="Enter your password" tabindex="2">
	      	</div>
	      	<div class="row">
	        	<div class="col-xs-6 col-md-6">
	          	<input type="submit" name="submit" value="Sign-in" class="btn btn-primary btn-block btn-lg" tabindex="3">
	        	</div>
	        	<div class="col-xs-6 col-md-6">
	          	<a href="./sign-up" class="btn btn-info btn-block btn-lg" tabindex="3">Sign up</a>
	        	</div>
						<div class="col-xs-12">
							<br>
							<p class="text-center">Forgot your password? Reset it <a href="/forgot-password">here</a>.</p>
						</div>
	      	</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php
  include("./layout/footer.php");
?>
