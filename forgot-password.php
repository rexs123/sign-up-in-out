<?php
/*
  FORGOT-PASSWORD.php
  VERSION 1.0
*/
$pageTitle = 'Forgot Password';
include("./system/config.php");
include("./layout/header.php");
include("./layout/navbar.php");
//if logged in redirect to users page
if( $user->is_logged_in() ){ header('Location: ./clientarea'); }
//if form has been submitted process it
if(isset($_POST['submit'])){
	//email validation
	if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
	    $error[] = 'Please enter a valid email address';
	} else {
		$stmt = $conn->prepare('SELECT email FROM users WHERE email = ?');
		$stmt->bind_param("s", $_POST['email']);
		$stmt->execute();
		$stmt->bind_result($row);
		while ($stmt->fetch()) {
			if (!empty($row)) {
				$email = $row;
				break; //stop the while loop
			}
		}
		if (!isset($email)) {
			$error[] = 'No email address has been found';
		}
		$stmt->close();
	}
	//if no errors have been created carry on
	if(!isset($error)){
		//create the activation code
		$token = md5(uniqid(rand(),true));
		$stmt = $conn->prepare("UPDATE users SET resetToken = ?, resetComplete='No' WHERE email = ?");
		$stmt->bind_param("ss", $token, $email);
		$stmt->execute();
		//send email
		$to = $row;
		$siteurl = SITEURL;
		$message = file_get_contents(ROOT_PATH.'templates/emails/forgot-password.html');
		$message = str_replace('%token%', $token, $message);
		$message = str_replace('%baseurl%', BASEURL, $message);
		$mail = new PHPMailer();

		if($config['smtp']) {
			$mail->isSMTP();                          // Set mailer to use SMTP
			$mail->Host = MAILHOST;                   // Specify main and backup SMTP servers
			$mail->SMTPAuth = MAILAUTH;               // Enable SMTP authentication
			$mail->Username = MAILUSER;               // SMTP username
			$mail->Password = MAILPASS;               // SMTP password
			$mail->SMTPSecure = MAILENC;              // Enable TLS encryption, `ssl` also accepted
			$mail->Port = MAILPORT;                   // TCP port to connect to
		}

		$mail->SetFrom(SITEEMAIL, MAILNAME);
		$mail->AddAddress($to);
		$mail->Subject = "Password Reset";
		$mail->MsgHTML($message);
		$mail->IsHTML(true);
		if(!$mail->Send()) {
		 echo "Mailer Error: " . $mail->ErrorInfo;
		}
		//redirect to index page
		header('Location: sign-in?action=reset');
		exit;
	}

}
?>
<div id="forgot-password">
	<div class="container">
		<h2 class="header">Forgotten Password</h2>
		<p>Enter your email</p>
		<div class="row">
		    <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
				<form role="form" method="post" action="" autocomplete="off">
					<?php
					//check for any errors
					if(isset($error)){
						foreach($error as $error){
							echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';
						}
					}
					if(isset($_GET['action'])){
						//check the action
						switch ($_GET['action']) {
							case 'active':
								echo '<div class="alert alert-success" role="alert">Your account is now active you may now log in.</div>';
								break;
							case 'reset':
								echo '<div class="alert alert-success" role="alert">Please check your inbox for a reset link.</div>';
								break;
						}
					}
					?>
					<div class="form-group">
						<input type="email" name="email" id="email" class="form-control input-lg" placeholder="Email" value="" tabindex="1">
					</div>
					<div class="row">
						<div class="col-xs-6 col-md-6 col-md-offset-3"><input type="submit" name="submit" value="Send Reset Link" class="btn btn-primary btn-block btn-lg" tabindex="2"></div>
						<div class="col-xs-12">
							<br>
							<p class="text-center">Remeber your password? Sign in <a href="./sign-in">here</a>.</p>
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
