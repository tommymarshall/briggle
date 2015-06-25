<?php

//-----------------------------------------------------------
//
//	Briggle Web Application
//	Copyright Tommy Marshall | 2011
//
//-----------------------------------------------------------

require 'installer-config.php';

if (isset($_GET['step']))
$step = $_GET['step'];
else
$step = 0;


?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
  <meta charset="UTF-8">
  <title><?php echo $setting['name'] ?> Installation</title>
  <link href="installer-styles.css" rel="stylesheet" type="text/css" />
 </head>
 <body>
  <div id="container">
  <div id="header">
    <a href="index.php">
     <?php echo $setting['name'] ?> Installation
    </a>
   </div>
	<div id="main">
	<div class="pad">
<?php

// Requirements check
if (!isset($_GET['step'])) {
	if (!is_writable($setting['config']['folder']))
		$msg .= '<p>WARNING! I can not write to the <code>'.$setting['config']['folder'].'</code> directory or does not exist. You will have to either change the permissions on your installation directory or create this folder manually.';
	if (phpversion() < $setting['requirements']['php'])
		$msg .= '<p>WARNING! The minimum PHP version is <code>'.$setting['requirements']['php'].'</code>, your version is <code>'.phpversion().'</code>. Please visit http://php.net for additional information on installing a more recent version.</p>';
	if (file_exists($setting['config']['folder'] . $setting['config']['file']))
		$msg .= '<p><strong>WARNING!</strong> The <code>'.$setting['config']['folder'] . $setting['config']['file'].'</code> file already exists. Running this installation process will overwrite your current settings.</p>';
	if($msg)
		echo '<div class="requirement error">'.$msg.'</div>';
}

switch($step) {
	case 0:

?>
	<h1>Pre-Installation Notes</h1>

<p>Welcome to the installation process for the <?php echo $setting['name'] ?> application (Version <?php echo $setting['version']; ?>). Before we begin please ensure that you have the below information:</p>
<ul style="list-style:disc;margin: 10px 40px;">
  <li>Database name</li>
  <li>Database username</li>
  <li>Database password</li>
  <li>Database host</li>
</ul>
<p>If you have all the information ready yourself, then you're ready to go. Hit the "Install <?php echo $setting['name'] ?>" link to the right to continue.</p>
<br />
<p><a href="?step=1" class="large green button" style="float:right"> <span>Install <?php echo $setting['name'] ?> &rsaquo;</span> </a></p>
<?php
	break;
	case 1:
	?>
	<h1>Step 1: Database Information</h1>
<form method="post" action="?step=2" name="form">
  <p>Enter your database connection settings. These settings will be inserted into <code><?php echo $setting['config']['folder'] . $setting['config']['file'] ?></code> and will be used by the application.</p>
  <table style="width: 100%;">
    <tr>
      <th class="col1">Database Name</th>
      <td class="col2"><input name="db_name" type="text" size="20" value="<?php echo strtolower($setting['name']) ?>" /></td>
      <td class="col3">The name of the database to use.</td>
    </tr>
    <tr>
      <th class="col1">Username</th>
      <td class="col2"><input name="db_user" type="text" size="20" /></td>
      <td class="col3">Your MySQL username.</td>
    </tr>
    <tr>
      <th class="col1">Password</th>
      <td class="col2"><input name="db_pass" type="password" size="20" /></td>
      <td class="col3">Your MySQL password.</td>
    </tr>
    <tr>
      <th class="col1">Database Host</th>
      <td class="col2"><input name="db_host" type="text" size="20" value="localhost" /></td>
      <td class="col3">Most likely won't need to change this value.</td>
    </tr>
  </table><br />
 	<a href="#" onclick="document['form'].submit()" class="large green button" style="float:right;"> <span>Generate Configuration File &rsaquo;</span> </a>
</form>
<?php
	break;
	case 2:

	echo '<h1>Step 2: Generating Configuration File</h1>';
	$db_name = trim($_POST['db_name']);
    $db_user = trim($_POST['db_user']);
    $db_pass = trim($_POST['db_pass']);
    $db_host = trim($_POST['db_host']);

	$handle = fopen($setting['config']['folder'] . $setting['config']['file'], 'w');
	$uniqueid = uniqid();
	$sub_folder = substr($_SERVER['REQUEST_URI'],0, strpos($_SERVER['REQUEST_URI'],"/install"));
	$install_dir = $_SERVER['SERVER_NAME'].$sub_folder;

$input = "<?php

//-----------------------------------------------------------
//
//	Briggle Web Application
//	Generated ".date('F j, Y H:i:s')."
//
//-----------------------------------------------------------

//-----------------------------------------------------------
//	Database Credentials
//-----------------------------------------------------------
define('DB_HOST','{$db_host}');
define('DB_USER','{$db_user}');
define('DB_PASS','{$db_pass}');
define('DB_NAME','{$db_name}');

//-----------------------------------------------------------
//	Directories and Folders
//-----------------------------------------------------------
define('BRIGGLE_DIR','http://{$install_dir}/');
define('BRIGGLE_ASSETS','http://{$install_dir}/bc-content/assets/');
define('BRIGGLE_THEMES','http://{$install_dir}/bc-content/themes/');
define('BRIGGLE_INC','http://{$install_dir}/bc-includes/');
define('UPLOAD_DIR','{$sub_folder}/bc-content/uploads/');

//-----------------------------------------------------------
//	Misc
//-----------------------------------------------------------
define('VERSION','{$setting['version']}');
define('SALT','{$uniqueid}');

?>";

fwrite($handle, $input);
fclose($handle);
if (file_exists($setting['config']['folder'] . $setting['config']['file']))
	echo '<h3>Configuration file created!</h3><p>The file <code>'.$setting['config']['folder'] . $setting['config']['file'].'</code> has been created successfully. To continue the installation, please continue the installation. </p><a href="?step=4" class="large green button" style="float:right;"> <span>Add Administrator &rsaquo;</span> </a> ';
else
	echo '<h3>ERROR!</h3><p>Configuration file was not created. Please check the the folder <code>'.$setting['config']['folder'].'</code> is created and the permissions to the  folder are set to <code>777</code>. After checking, click the link button below to regenerate the configuration file again..</p> <a href="?step=1" class="large red button" style="float:left"> <span>&lsaquo; Regenerate Configuration</span> </a>';

	break;
	case 4:
	?>
	<h1>Step 4: Add Administrator Account</h1>
<form method="post" action="?step=5" name="form">
  <p>Enter your database connection settings. These settings will be inserted into the database and be used as your login credentials.</p>
  <table style="width: 100%;">
    <tr>
      <th class="col1">Name</th>
      <td class="col2"><input name="name" type="text" size="20" value="" /></td>
      <td class="col3">Your full name.</td>
    </tr>
    <tr>
      <th class="col1">Login</th>
      <td class="col2"><input name="login" type="text" size="20" /></td>
      <td class="col3">Will be used to log into the <?php echo $setting['name']; ?> application.</td>
    </tr>
    <tr>
      <th class="col1">Email</th>
      <td class="col2"><input name="email" type="text" size="20" /></td>
      <td class="col3">Your email address.</td>
    </tr>
    <tr>
      <th class="col1">Password</th>
      <td class="col2"><input name="password" type="password" size="20" /></td>
      <td class="col3">Your password. (Will be encrypted)</td>
    </tr>
  </table><br />
 	<a href="#" onclick="document['form'].submit()" class="large green button" style="float:right;"> <span>Add Account &rsaquo;</span> </a>
</form>
<?php
	break;
	case 5:

if (file_exists($setting['config']['folder'] . $setting['config']['file'])) {

	echo '<h1>Step 5: Create Database Tables and Administator</h1>';
	require $setting['config']['folder'] . $setting['config']['file'];

	connect();

	if (mysql_error() != null) {
		echo '<h3>ERROR!</h3>';
		echo '<p>This is probably due to incorrect database credentials. Please go back to the previous step and enter your database details.</p>';
		echo '<a href="?step=1" class="large red button" style="float:left"> <span>&lsaquo; Regenerate Configuration</span> </a>';
	} else {

	$name = trim($_POST['name']);
    $login = trim($_POST['login']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

	/* Generates random password */
    $encrypted_password	=	md5($password.SALT);
	$file_content = file('installer-sql.sql');
    $query = "";
    foreach($file_content as $sql_line){
      if(trim($sql_line) != "" && strpos($sql_line, "--") === false){
        $query .= $sql_line;

        if(preg_match("/;[\040]*\$/", $sql_line)){
          if (strpos($query, '{LOGIN}'))
         	$query = str_replace("{LOGIN}", $login, $query);
          if (strpos($query, '{EMAIL}'))
         	$query = str_replace("{EMAIL}", $email, $query);
          if (strpos($query, '{PASSWORD}'))
         	$query = str_replace("{PASSWORD}", $encrypted_password, $query);
          if (strpos($query, '{NAME}'))
         	$query = str_replace("{NAME}", $name, $query);

          $result = mysql_query($query) or die('<h3>ERROR!</h3><p>'.mysql_error().'. You may be trying to overwrite a database whose tables already exist. Please delete the tables in <code>'.DB_NAME.'</code>.</p><a href="?step=1" class="large red button" style="float:left"> <span>&lsaquo; Regenerate Configuration</span> </a>');
          $query = "";
        }

      }
    }
    echo '<h3>Congratulations! '.$setting['name'].' has been successfully Installed!</h3>';

    /* Start Administrator Login Display */
    echo '<p><strong>Your login credentials are below:</strong></p>';
    echo '<p style="margin-left: 25px;font-size:15px;">
   		Login: '.$email.' or '.$login.'<br />
   		Password: '.$password.'</p>';
    /* End */

    echo '<p>Please delete this installation directory.</p>';
 	echo '<p><a href="'.$setting['after_install'].'" class="large green button" style="float:right"> <span>'.$setting['finished'].' &rsaquo;</span> </a></p>';
    }
  } else {
 	echo '<p>Configuration file not created. You may have to fill in the database information manually. To do this, simply open phpMyAdmin or another database manager and execute the MYSQL code located in <code>installer-sql.sql</code>.</p>';
  }

	break;
}

?>

	 </div>
	</div>
 <br class="clr" />
  </div>
 <br class="clr" />
   <div id="footer">
    <div class="footer_inner">
	 Copyright Tommy Marshall | 2011
	</div>
   </div>

 </body>
</html>
