<!DOCTYPE HTML>
<html lang="en-US">
 <head>
  <meta charset="UTF-8">
  <title><?php echo $t->get_title(); ?></title>
  <link href="http://fonts.googleapis.com/css?family=Merienda+One" rel="stylesheet" type="text/css" />
  <link href="<?php echo BRIGGLE_THEMES; ?>default/styles.css" rel="stylesheet" type="text/css" />
  <?php echo $t->get_styles(); ?>
 </head>
 <body>
  <?php get_notification(); ?>
  <div id="wrapper">

   <div id="header">

    <div id="logo">
      <a href="<?php echo BRIGGLE_DIR; ?>"><?php echo $setting['company']; ?></a>
    </div>

    <div id="toolbar">
     <?php if ( user_logged_in() ): ?>
	    <span class="greeting"><?php echo $t->get_greeting(); ?></span><br />
   	<?php echo $t->get_toolbar(); ?>
     <?php else: ?>
    	<?php echo $t->get_login_form(); ?>
     <?php endif; ?>
    </div>

   </div>

   <div id="content">
    <div class="pad">
     <h1 class="title"><span class="page"><?php echo $t->get_sub_title(); ?></span></h1>
