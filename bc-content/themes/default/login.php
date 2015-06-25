<!DOCTYPE HTML>
<html lang="en-US">
 <head>
  <meta charset="UTF-8">
  <title><?php echo $setting['company']; ?></title>
  <link href="<?php echo BRIGGLE_THEMES; ?>default/login.css" rel="stylesheet" type="text/css" />
 </head>
 <body>
<form method="post" name="normal-login" action="<?php echo BRIGGLE_DIR; ?>">
<?php $token = $_SESSION['token'] = uniqid(); ?>
<input type="hidden" name="token" value="<?php echo $token; ?>" />
<div id="login">
 
 <div class="form-header">
  <h1><?php echo $setting['company']; ?> Login</h1>
 </div>
 
 <?php if ( !empty($user->errors) ) get_message($user->errors , "error"); ?>
 
 <div class="col">
   
 <div class="form">
  <div class="form-entry">
   <label for="user">Email or Login</label>
   <input type="text" id="user" name="user" class="text" value="<?php echo htmlentities($_POST['user']); ?>" id="username" />
  </div>
  
  <div class="form-entry">
   <label for="pass">Password</label>
   <input type="password" name="pass" class="text" />
  </div>

  <div class="form-entry">
   <input type="checkbox" name="remember" /> <span class="remember">Stay Logged In</span> <input type="submit" name="normal-login" class="large green button" value="Submit" />
  </div>
  
 </div>
 </div>
</form>

<form method="post" name="guest-login" action="<?php echo BRIGGLE_DIR; ?>">
<input type="hidden" name="token" value="<?php echo $token; ?>" />
 <div class="col">

 <div class="form">
  <p>Alternatively, if you do not have an account with <?php echo $setting['company']; ?>, you may browse as a guest by entering a valid password below.</p>
  <div class="form-entry">
   <label for="guest">Password</label>
   <input type="password" id="guest" name="guest" class="text" value="<?php echo htmlentities($_POST['guest']); ?>" id="guest" />
  </div>

  <div class="form-entry">
   <label>&nbsp;</label>
   <input type="submit" name="guest-login" class="large green button" value="Visit as Guest" />
  </div>
  
 </div>
 </div>
</form>
 
 <br class="clr" />
</div>
<br class="clr" />
<div id="footer">
   <p><?php echo get_copyright(); ?></p>
</div>

 </body>
</html>