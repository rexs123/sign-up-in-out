<?php
include('password.php');
class User extends Password{

    private $_db;

    function __construct($conn){
    	parent::__construct();

    	$this->_db = $conn;
    }

  //get the hash
	private function gethash($username){
		$stmt = $this->_db->prepare('SELECT password FROM users WHERE username = ? AND active="Yes"');
    $stmt->bind_param("s", $username);
		$stmt->execute();
    $stmt->bind_result($row);

		while ($stmt->fetch()) {
			return $row;
		}
	}

  //get client ip
  private function ip() {
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP)) {
      $ip = $client;
    } elseif(filter_var($forward, FILTER_VALIDATE_IP)) {
      $ip = $forward;
    } else {
      $ip = $remote;
    }
    return $ip;
  }

  //login
	public function signin($username, $password){

		$hashed = $this->gethash($username);

		if($this->password_verify($password, $hashed) == 1){
		    $_SESSION['loggedin'] = true;

        $stmt = $this->_db->prepare("UPDATE users SET `ip` = ? WHERE username = ?");
        $ip = $this->ip();
        $stmt->bind_param("ss", $ip, $username);
        $stmt->execute();

		    return true;
		}
	}

  //logout
	public function signout(){
		session_destroy();
	}

  //is logged in
	public function signedin(){
		if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
			return true;
		}
	}

  public function username() {
    return $_SESSION["username"];
  }


}
?>
