<?php
include("./system/config.php");
  $user->signout();
  header('Location: ./');
  exit;
?>
