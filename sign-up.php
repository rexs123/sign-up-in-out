<?php
/*
  SIGN-UP.php
  VERSION 1.0
*/
$pageTitle = "Sign-up";
include("./system/config.php");
include("./layout/header.php");
include("./layout/navbar.php");
//if logged in redirect to users page
if($user->is_logged_in()){
  header('Location: '. BASEURL .'./clientarea');
}
if($_SERVER["REQUEST_METHOD"] == "POST") {
  $recaptcha = $_POST['g-recaptcha-response'];
  if(!empty($recaptcha)){
    $google_url = "https://www.google.com/recaptcha/api/siteverify";
    $ip = $_SERVER['REMOTE_ADDR'];
    $url = $google_url."?secret=".GSKEY."&response=".$recaptcha."&remoteip=".$ip;
    $res = getCurlData($url);
    $res = json_decode($res, true);
    if($res['success']){
      if(isset($_POST['submit'])){
        if(strlen($_POST['username']) < 3){
          $error[] = 'Username is too short.';
        } else {
          $stmt = $conn->prepare('SELECT username FROM users WHERE username = ?');
          $stmt->bind_param("s", $_POST['username']);
          $stmt->execute();
          $stmt->bind_result($row);
          while ($stmt->fetch()) {
            if(!empty($row)){
              $error[] = 'Username provided is already in use.';
              break;
            }
          }
          $stmt->close();
        }
        if(strlen($_POST['password']) < 3){
          $error[] = 'Password is too short.';
        }
        if(strlen($_POST['passwordConfirm']) < 3){
          $error[] = 'Confirm password is too short.';
        }
        if($_POST['password'] != $_POST['passwordConfirm']){
          $error[] = 'Passwords do not match.';
        }
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
          $error[] = 'Please enter a valid email address';
        } else {
          $stmt = $conn->prepare('SELECT email FROM users WHERE email = ?');
          $stmt->bind_param("s", $_POST['email']);
          $stmt->execute();
          $stmt->bind_result($row);
          while ($stmt->fetch()) {
            if(!empty($row)){
              $error[] = 'Email provided is already in use.';
              break;
            }
          }
          $stmt->close();
        }
        if(!isset($error)){
          $hashedpassword = $user->password_hash($_POST['password'], PASSWORD_BCRYPT);
          $activasion = md5(uniqid(rand(),true));
          $discord = md5(uniqid(rand(),true));
          try {
            $stmt = $conn->prepare('INSERT INTO users (username, password, email, active, ip, discord) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->bind_param("ssssss", $_POST['username'], $hashedpassword, $_POST['email'], $activasion, $ip, $discord);
            $stmt->execute();
            $stmt->close();
            $id = $conn->insert_id;
            $to = $_POST['email'];
            $message = file_get_contents(ROOT_PATH.'templates/emails/sign-up.html');
            $message = str_replace('%activasion%', $activasion, $message);
            $message = str_replace('%baseurl%', BASEURL, $message);
            $message = str_replace('%userid%', $id, $message);
            $mail = new PHPMailer();
            $mail->SetFrom(SITEEMAIL, MAILNAME);
            $mail->AddAddress($to);
            $mail->Subject = "Sign-up activation for GetBukkit.org";
            $mail->MsgHTML($message);
            $mail->IsHTML(true);
            if(!$mail->Send()) {
             echo "Mailer Error: " . $mail->ErrorInfo;
            }

            header('Location: ./sign-in/a/joined');
            exit;
          } catch(Exception $e) {
            $error[] = "Oh no, something is broken :(!";
          }
        }
      }
    }else{
      $error[] = "Please re-enter your reCAPTCHA.";
    }
  }else{
    $error[] = "Please re-enter your reCAPTCHA.";
  }
}
//check for any errors
if(isset($error)){
  foreach($error as $error){
    $message = "<div class='alert alert-danger'>$error $msg</div>";
  }
}
if(isset($_GET['action']) && $_GET['action'] == 'joined'){
  $message = "<div class='alert alert-success'>Registration successful, please check your email to activate your account.</div>";
}
?>
</div></div></div>
<div id="sub-header">
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<h1>Signing up to GetBukkit</h1>
			</div>
		</div>
	</div>
</div>
<div class="container">
	<div class="row">
		<? if(!$role->premium()){ echo $ccAdvert->ccAdvert("top", null); } ?>
	</div>
</div>
<div id="sign-up">
  <div class="container">
    <div class="row">
      <div class="col-xs-12 col-sm-8 col-sm-offset-2">
        <?=$message;?>
        <form role="form" method="POST" action="">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="username" id="username" class="form-control input-lg" placeholder="User Name" value="<?php if(isset($error)){ echo $_POST['username']; } ?>" tabindex="1" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="email" name="email" id="email" class="form-control input-lg" placeholder="Email Address" value="<?php if(isset($error)){ echo $_POST['email']; } ?>" tabindex="2">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-xs-6">
              <div class="form-group">
                <input type="password" class="form-control input-lg" name="password" id="xpassword" placeholder="Password" tabindex ="3" required>
              </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6">
              <div class="form-group">
                <input type="password" class="form-control input-lg" name="passwordConfirm" id="passwordConfirm" placeholder="Confirm Password" tabindex = "4" required>
              </div>
            </div>
          </div>
          <div class="col-md-12 text-center recaptcha">
            <div class="text-center g-recaptcha" data-sitekey="<?php echo GKEY; ?>" tabindex="5"></div>
          </div>
          <input type="submit" name="submit" value="Sign up" id="signupr" class="btn btn-primary btn-block btn-lg" tabindex="6">
        </form>
      </div>
    </div>
  </div>
</div>
<?php
  include("./layout/footer.php");
?>
