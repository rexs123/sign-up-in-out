<?php
ob_start();
session_start();
/*
  CONFIG.php
  VERSION 1.0
*/
/* SITE CONFIGURATION */
define('BASEURL','https://your-site-url-here.org/');      // BASE SITE URL
define('SITEEMAIL','your-site-email-here.org');           // SITE NO REPLY EMAIL ADDRESS
define('MAILNAME','your-site-name-here');                 // SITE MAIL NAME (THE NAME SHOWN IN THE EMAILS SENT OUT)

/* PHP DATE SETTINGS (Remove to disable / use server settings) */
date_default_timezone_set('America/Toronto');

/* DATABASE CONFIGURATION */
define("DBHOST", "your-database-host-here");
define("DBUSER", "your-database-user-here");
define("DBPASS", "your-database-password-here");
define("DATABS", "your-database-here");

/*  MAIL CONFIG OPTIONS  */
$config['smtp'] = false;
define("MAILHOST", "SMTP HOST");          // Primary mail server host
define("MAILAUTH", true);                 // BOOLEAN true of false
define("MAILUSER", "username@host.org");  // SMTP username
define("MAILPASS", "password");           // SMTP authentication password
define("MAILENC", "tls");                 // Enable TLS encryption, `ssl` also accepted
define("MAILPORT", 25);                   // SMTP TCP port to connect to

/* GOOGLE RECAPTCHA */
define("GKEY", "");           // SITE KEY (PUBLIC VIEWABLE KEY)
define("GSKEY", "");          // SECERT KEY (PRIVATE KEY)

/*                                                                        */
    #	DO NOT EDIT     #	DO NOT EDIT     #	DO NOT EDIT     #	DO NOT EDIT
/*                                                                         */
$conn = new mysqli(DBHOST, DBUSER, DBPASS, DATABS);
if ($conn->connect_error) {
    die("Oop's, Somethings broken!");
}

define('ROOT_PATH', dirname(__DIR__) . '/');
define("APP_ROOT", realpath( dirname( __FILE__ ) ).'/' );
include(APP_ROOT.'classes/phpmailer/phpmailer.php');
include(APP_ROOT.'classes/user.class.php');
include(APP_ROOT.'classes/roles.class.php');
include(APP_ROOT.'classes/curator.class.php');

//User class
$user = new User($conn);
//Role Class
$role = new Role($conn);
//Curator / Utils class
$role = new Curator());
/*                                                                        */
    #	DO NOT EDIT     #	DO NOT EDIT     #	DO NOT EDIT     #	DO NOT EDIT
/*                                                                         */
