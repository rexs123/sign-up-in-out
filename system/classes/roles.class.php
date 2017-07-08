<?php
class Role {

    private $_db;

    function __construct($conn){

    	$this->_db = $conn;
    }

	private function get_user($username){
		$username = $_SESSION['username'];
		$stmt = $this->_db->prepare('SELECT role FROM users WHERE username = ?');
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$stmt->bind_result($row);
		while ($stmt->fetch()) {
			return $row;
		}
	}

	public function member() {
		$role = $this->get_user($username);
    return ($role == 0 ? true : false);
	}

	public function admin() {
		$role = $this->get_user($username);
		return ($role == 0 || $role = 1 ? true : false);
		}
	}
  
}
?>
