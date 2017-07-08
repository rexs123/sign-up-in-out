<?php
ob_start();
session_start();
/*
  CONFIG.php
  VERSION 1.0
*/
/* SITE CONFIGURATION */

define('BASEURL','https://your-site-url-here.org/');
define('SITEEMAIL','your-site-email-here.org');
define('MAILNAME','your-site-name-here');

//Date
date_default_timezone_set('America/Toronto');

/* DATABASE CONFIGURATION */
define("DBHOST", "your-database-host-here");
define("DBUSER", "your-database-user-here");
define("DBPASS", "your-database-password-here");
define("DATABS", "your-database-here");

/* GOOGLE RECAPTCHA */
//SITE KEY (PUBLIC VIEWABLE KEY)
define("GKEY", "");

//SECERT KEY (PRIVATE KEY)
define("GSKEY", "");

/*
#	DO NOT EDIT
*/
$conn = new mysqli(DBHOST, DBUSER, DBPASS, DATABS);
if ($conn->connect_error) {
    die("Oop's, Somethings broken!");
}

define('ROOT_PATH', dirname(__DIR__) . '/');
define("APP_ROOT", realpath( dirname( __FILE__ ) ).'/' );
include(APP_ROOT.'classes/phpmailer/phpmailer.php');
include(APP_ROOT.'classes/user.class.php');
include(APP_ROOT.'classes/roles.class.php');

//User class
$user = new User($conn);
//Role Class
$role = new Role($conn);
