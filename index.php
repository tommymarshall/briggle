<?php

if (!file_exists('bc-includes/configuration.php')) header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'install');

//-----------------------------------------------------------
//
//	Briggle Web Application
//	Copyright Tommy Marshall | 2011
//
//-----------------------------------------------------------
define('_GO_',1);$start = microtime(true);


//-----------------------------------------------------------
//	Load Configuration, Classes, and Functions
//-----------------------------------------------------------
require 'bc-includes/configuration.php';
require 'bc-includes/class-database.php';
require 'bc-includes/class-user.php';
require 'bc-includes/functions.php';


//-----------------------------------------------------------
//	Get Path, Clean, and Assign
//-----------------------------------------------------------
$rt = clean($_SERVER['PATH_INFO']);
$p = explode("/",rtrim($rt,"/"));
foreach ($p as $u)
  $uri[++$i] = strtolower($u);


//-----------------------------------------------------------
//	Login Check
//-----------------------------------------------------------
if ( isset($_GET['logout']) )
  $user->logout(BRIGGLE_DIR);
if ( !$user->is_loaded() )
 {
  if ( isset($_POST['user']) && isset($_POST['pass']) || isset($_POST['guest-login']) )
   {
    if ( !$user->login($_POST['user'],$_POST['pass'],$_POST['remember']) )
      $errors = true;
    else
      forward_to(BRIGGLE_DIR.$rt);
   }
  if ( $setting['private'] == 'yes' )
   {
 	if ( file_exists('bc-content/themes/'.$setting['theme'].'/login.php') )
      include 'bc-content/themes/'.$setting['theme'].'/login.php';
 	else
      include 'bc-content/themes/default/login.php';
 	exit;
   }
 }


//-----------------------------------------------------------
//	Load Page Controller
//-----------------------------------------------------------
require 'bc-includes/controller.php';


//-----------------------------------------------------------
//	Load Template
//-----------------------------------------------------------
require 'bc-includes/class-template.php';


//-----------------------------------------------------------
//	Load Page/View
//-----------------------------------------------------------
if ( file_exists('bc-pages/'.$controller->_page.'.php') )
  include 'bc-pages/'.$controller->_page.'.php';
else
  forward_to(BRIGGLE_DIR);

