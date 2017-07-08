<?php
include("./system/config.php");
$id = trim($_GET['x']);
$active = trim($_GET['y']);
if(is_numeric($id) && !empty($active)){
	$stmt = $conn->prepare("UPDATE users SET active = 'Yes' WHERE id = ? AND active = ?");
	$stmt->bind_param("is", $id, $active); //id is number active is boolean
	$stmt->execute();
	if($stmt->affected_rows == 1){
		header('Location: sign-in?action=active');
		exit;
	} else {
		echo "Your account could not be activated.";
	}
	$stmt->close();
	$conn->close();
}
?>
