<?php

defined('_GO_') or die("Direct access disallowed.");

$t = new Template();

class Template
 {
    private $_scripts;
    private $_styles;
    private $_title;
    private $_sub_title;
    private $_action;
    private $_page;

    public function add_script($s,$v)
     {
        $this->_scripts[] = array($s => $v);
     }

    public function add_style($s,$v)
     {
        $this->_styles[] = array($s => $v);
     }

    public function get_title()
     {
        return $this->_title;
     }

    public function get_sub_title()
     {
        return $this->_sub_title;
     }

    public function get_login_form()
     {
        global $user, $rt;
        $token = $_SESSION['token'] = uniqid();

        echo '
        <form method="post" name="normal-login" id="small-login" action="'.BRIGGLE_DIR.$rt.'">
        <input type="hidden" name="token" value="'.$token.'" />';
        if ( !empty($user->errors) )
         {
            get_message($user->errors , "error" , true );
         }
        echo '
        <div class="col">
        Email or Login<br />
        <input type="text" name="user" class="text" value="tommy" /><br />
        </div>
        <div class="col">
        Password<br />
        <input type="password" name="pass" class="text" value="test" /><br />
        </div>
        <span class="remember"><input type="checkbox" name="remember" /> Stay Logged In</span>
        <input type="submit" name="normal-login" class="large green button" value="Submit" />
        </form>';
     }

    public function get_greeting()
     {
        global $user;

        return 'Hello, '.$user->get('name').'.';
     }

    public function get_toolbar()
     {
        global $user;

        if ($user->get('type') == 3)
         {
            // Administrator
            return '<a href="?logout">Logout</a><a href="'.BRIGGLE_DIR.'settings">Settings</a><a href="'.BRIGGLE_DIR.'account">My Account</a><a href="'.BRIGGLE_DIR.'authors">Authors</a><br /><a href="'.BRIGGLE_DIR.'write" class="large green button">Write Post</a>';
         }
        elseif ($user->get('type') == 2)
         {
            // Editor
            return '<a href="?logout">Logout</a><a href="'.BRIGGLE_DIR.'account">My Account</a><a href="'.BRIGGLE_DIR.'authors">Authors</a><br /><a href="'.BRIGGLE_DIR.'write" class="large green button">Write Post</a>';
         }
        elseif ($user->get('type') == 1)
         {
            // Author
            return '<a href="?logout">Logout</a><a href="'.BRIGGLE_DIR.'account">My Account</a><a href="'.BRIGGLE_DIR.'authors">Authors</a><br /><a href="'.BRIGGLE_DIR.'write" class="large green button">Write Post</a>';
         }
        else
         {
            // Guest
            return '<a href="?logout">Logout</a>';
         }
     }

    public function head()
     {
        global $page, $controller, $setting, $t;

        $this->_title = $controller->_title;
        $this->_sub_title = $controller->_sub_title;
        $this->_action = $controller->_action;
        $this->_page = ucfirst($page);

        if ( file_exists('./bc-content/themes/'.$setting['theme'].'/header.php') )
         {
            include './bc-content/themes/'.$setting['theme'].'/header.php';
         }
        else
         {
            include './bc-content/themes/default/header.php';
         }
     }

    public function foot()
     {
        global $t, $start, $db;

        if ( file_exists('./bc-content/themes/'.$setting['theme'].'/footer.php') )
         {
            include './bc-content/themes/'.$setting['theme'].'/footer.php';
         }
        else
         {
            include './bc-content/themes/default/footer.php';
         }

        echo "\n".'<!-- Execution Time: '.get_execution_time($start).' seconds. Memory Usage: '.get_memory().' MB. Database Queries: '.$db->num_queries.' -->';
     }

    public function get_sidebar()
     {
        global $controller;
        return $controller->get_sidebar();
     }

    public function get_scripts()
     {
        if (!empty($this->_scripts))
         {
            $s .= "\n<!-- Start Scripts -->\n";
            foreach ($this->_scripts as $var)
             {
                foreach ($var as $k => $v)
                 {
                    if ($k == 'js')
                     {
                        $s .= "<script src=\"".BRIGGLE_ASSETS."js/{$v}.js\"></script>\n";
                     }

                    if ($k == 'css')
                     {
                        $s .= "<link href=\"".BRIGGLE_ASSETS."css/{$v}.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" title=\"no title\" />\n";
                     }

                    if ($k == 'custom')
                     {
                        $s .= "<script type=\"text/javascript\">\n/* <![CDATA[ */{$v}\n/* ]]> */\n</script>\n";
                     }
                 }

             }
            $s .= "<!-- End Scripts -->\n\n";
            return $s;
         }
     }

    public function get_styles()
     {
        if (!empty($this->_styles))
         {
            $s .= "\n<!-- Start Styles -->\n";
            foreach ($this->_styles as $var)
             {
                foreach ($var as $k => $v)
                 {
                    if ($k == 'css')
                     {
                        $s .= "<link href=\"".BRIGGLE_ASSETS."css/{$v}.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
                     }

                    if ($k == 'custom')
                     {
                        $s .= "<style type=\"text/css\">\n{$v}\n</style>\n";
                     }
                 }

             }
            $s .= "<!-- End Styles -->\n\n";
            return $s;
         }
     }
 }
