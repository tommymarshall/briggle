<?php

defined('_GO_') or die("Direct access disallowed.");

$controller = new Controller();

class Controller {

    // Universal Variables
    public  $_title;
    public  $_sub_title;
    public  $_page;
    public  $errors;

    // Class Specific
    private $_function;
    private $_id;
    private $_extra;
    public  $_posts;
    public  $_uploads;
    public  $_post;
    public  $_comments;
    public  $_comment;
    public  $_authors;
    public  $_author;

    public function __construct()
     {
        global $uri, $user;


        //-----------------------------------------------------------
        //  http://examplesite.com/_function/_id/_extra
        //-----------------------------------------------------------
        if ( isset($uri[1]) )
         {
            $this->_function = preg_replace("/\W/", "", $uri[1]);
         }
        if ( isset($uri[2]) )
         {
            $this->_id = preg_replace("/[^0-9]/", "", $uri[2]);
         }
        if ( isset($uri[3]) )
         {
            $this->_extra = preg_replace("/[^0-9]/", "", $uri[3]);
         }

        if ( ( $this->_function == 'page' && $this->_id ) || empty($this->_function) )
         {
            if ( empty($this->_function) )
             {
                $this->_posts = get_posts( 1 );
             }
            else
             {
                $this->_posts = get_posts( $this->_id );

                if ( empty($this->_posts) )
                 {
                    forward_to(BRIGGLE_DIR);
                 }
             }


            for ( $i = 0; $i < count($this->_posts); $i++ )
             {
                if ( $this->_posts[$i]['uploads'] > 0 )
                 {
                     $this->_posts[$i]['uploads'] = get_uploads( $this->_posts[$i]['ID'] );
                 }
             }

            $this->_page = 'home';

            $this->_title = 'Home';

            $this->_sub_title = '<a href="'.BRIGGLE_DIR.'">Home</a>';

         }
        else
         {
            if ( is_string($this->_function) )
             {
                switch ( $this->_function )
                 {
                    case 'view':
                    $this->view();
                    break;

                    case 'edit':
                    $this->edit();
                    break;

                    case 'write':
                    $this->write();
                    break;

                    case 'authors':
                    $this->authors();
                    break;

                    case 'account':
                    $this->account();
                    break;

                    case 'settings':
                    $this->settings();
                    break;

                    default:
                    forward_to(BRIGGLE_DIR);
                    break;

                 }
             }
            else
             {
                forward_to(BRIGGLE_DIR);
             }
         }
     }

    public function view()
     {
        global $user;
        $db = Database::obtain();

        $this->_page = 'view';

        $this->_post = get_post( $this->_id );

        if ( empty($this->_post) )
         {
            forward_to(BRIGGLE_DIR);
         }

        $this->_post['uploads'] = get_uploads( $this->_id );

        $this->_title = $this->_post['title'];

        $this->_sub_title = '<a href="'.BRIGGLE_DIR.'">Home</a><small>|</small> <a href="'.BRIGGLE_DIR.'view/'.$this->_post['ID'].'">'.format_title($this->_post['title']).'</a>';

        $this->_comments = get_comments( $this->_id );

        if ( $_POST['submit-comment'] )
         {
            if ( $user->get('type') < 1 )
             {
                forward_to(BRIGGLE_DIR);
             }

            if ( trim($_POST['comment']) == "" )
             {
                $this->errors[] = 'You cannot post a blank comment';
                return false;
             }

            $data = array(
                'u_ID'      => $user->get('ID'),
                'p_ID'      => $this->_id,
                'comment'   => htmlentities($_POST['comment']),
                'date'      => 'NOW()',
                'modified'  => 'NOW()',
                'active'    => 1
            );

            $insert = $db->insert("comments",$data);
            add_comment_count($this->_id);

            if ( $insert )
             {
                add_notification('Your comment was successfully added.');
                forward_to(BRIGGLE_DIR.'view/'.$this->_id.'#comment-'.$insert);
             }
            else
             {
                $this->errors[] = 'There was an error submitting the form to the database.';
                return false;
             }
         }

     }

    public function write()
     {
        global $user;
        $db = Database::obtain();

        if ( $user->get('type') < 1 )
         {
            forward_to(BRIGGLE_DIR);
         }

        $this->_page    = 'write';

        $this->_title   = 'Write';

        $this->_sub_title = '<a href="'.BRIGGLE_DIR.'">Home</a><small>|</small> <a href="'.BRIGGLE_DIR.'write">Write</a>';

        if ( $_POST['write-post'] )
         {

            if ( trim($_POST['title']) == "" )
             {
                $this->errors[] = 'You must have a title';
                return false;
             }

            if ( !empty($_FILES['files']) )
             {

                // In case someone attempts to upload an empty file, and reorder keys to start at 0 again
                $_FILES['files']['name']        = array_merge(array_filter($_FILES['files']['name']));
                $_FILES['files']['type']        = array_merge(array_filter($_FILES['files']['type']));
                $_FILES['files']['tmp_name']    = array_merge(array_filter($_FILES['files']['tmp_name']));
                $_FILES['files']['error']       = array_merge(array_filter($_FILES['files']['error']));
                $_FILES['files']['size']        = array_merge(array_filter($_FILES['files']['size']));

                $files_being_uploaded = count($_FILES['files']['name']);

                if ( $files_being_uploaded > 0 )
                 {

                    $relative_path  = UPLOAD_DIR . date("Y/m/",strtotime("now"));
                    $upload_path    = $_SERVER['DOCUMENT_ROOT'].$relative_path;
                    $next_post_id   = get_next_id();

                if ( !file_exists( $upload_path ) )
                 {
                    if ( !rmkdir( $upload_path ) )
                     {
                        echo 'There was an error creating the directory. Check that the permissions for '.UPLOAD_DIR.' is set to writeable (755 or 777).';
                        exit;
                     }
                     }

                for ( $i = 0; $i < $files_being_uploaded; $i++ )
                 {
                        $ext = get_ext($_FILES['files']['name'][$i]);
                    $valid_ext = array("jpg","gif","png","jpeg","bmp");

                    if ( !in_array($ext , $valid_ext) )
                     {
                        $this->errors[] = 'You can only upload images. The file type you attempted to upload was '.$ext;
                        return false;
                     }
                 }

                for ( $i = 0; $i < $files_being_uploaded; $i++ )
                 {

                    $final_name = strtolower($_FILES['files']['name'][$i]);

                    while ( file_exists( $upload_path.$final_name ) )
                     {
                        $final_name = ++$j.'-'.$final_name;
                     }

                    if ( !move_uploaded_file( $_FILES['files']['tmp_name'][$i] , $upload_path . $final_name ) )
                     {
                        $this->errors[] = 'There was an error moving the file to that directory';
                        return false;
                     }
                    else
                     {
                        $data = array(
                            'name'      => $final_name,
                            'directory' => $relative_path . $final_name,
                            'p_ID'      => $next_post_id
                        );

                        $insert_file = $db->insert("uploads",$data);
                     }
                 }
                 }
             }

            $data = array(
                'u_ID'      => $user->get('ID'),
                'title' => htmlentities($_POST['title']),
                'content'   => htmlentities($_POST['content']),
                'uploads'   => $files_being_uploaded,
                'comments'  => 0,
                'date'      => 'NOW()',
                'modified'  => 'NOW()',
                'type'      => (int) $_POST['type']
            );

            $insert = $db->insert("posts",$data);

            if ( $insert )
             {
                send_notifications($insert, $user->get('ID') ,$data['title']);
                add_notification('"'.$data['title'].'" was created.');
                forward_to(BRIGGLE_DIR.'view/'.$insert);
             }
            else
             {
                $this->errors[] = 'There was an error submitting the form to the database.';
                return false;
             }
         }

     }

    public function edit()
     {
        global $user;
        $db = Database::obtain();

        if ( $user->get('type') < 1 )
         {
            forward_to(BRIGGLE_DIR);
         }

        $this->_page    = 'edit';

        $this->_post = get_post( $this->_id );

        if ( empty($this->_post) )
         {
             forward_to(BRIGGLE_DIR);
         }

        $this->_post['uploads'] = get_uploads( $this->_id );

        $this->_title   = 'Edit';

        $this->_sub_title = '<a href="'.BRIGGLE_DIR.'">Home</a><small>|</small> <a href="'.BRIGGLE_DIR.'view/'.$this->_id.'">'.format_title($this->_post['title']).'</a>';

        if ( $user->get('ID') != get_post_author($this->_id) && $user->get('type') < 2 )
         {
            forward_to(BRIGGLE_DIR.'view/'.$this->_id);
         }

        if ( $_POST['delete-post'] )
         {
            if ( delete_post($this->_id) )
             {
                add_notification('"'.$this->_post['title'].'" was deleted.');
                forward_to(BRIGGLE_DIR);
             }
         }

        if ( $_POST['edit-post'] )
         {
            $current_file_count = count($this->_post['uploads']);

            if ( trim($_POST['title']) == "" )
             {
                $this->errors[] = 'You must have a title';
                return false;
             }

            if ( !empty($_POST['current_uploads']))
             {
                foreach ( $_POST['current_uploads'] as $current_upload )
                 {
                    delete_upload((int) $current_upload);
                    $current_file_count -= 1;
                 }
             }

            if ( !empty($_FILES['files']) )
             {

                // In case someone attempts to upload an empty file
                $_FILES['files']['name']        = array_filter($_FILES['files']['name']);
                $_FILES['files']['type']        = array_filter($_FILES['files']['type']);
                $_FILES['files']['tmp_name']    = array_filter($_FILES['files']['tmp_name']);
                $_FILES['files']['error']       = array_filter($_FILES['files']['error']);
                $_FILES['files']['size']        = array_filter($_FILES['files']['size']);

                $files_being_uploaded = count($_FILES['files']['name']);

                if ( $files_being_uploaded > 0 )
                 {

                    $relative_path  = UPLOAD_DIR . date("Y/m/",strtotime("now"));
                    $upload_path    = $_SERVER['DOCUMENT_ROOT'].$relative_path;

                if ( !file_exists( $upload_path ) )
                 {
                    if ( !rmkdir( $upload_path ) )
                     {
                        echo 'There was an error creating the directory. Check that the permissions for '.UPLOAD_DIR.' is set to writeable (755 or 777).';
                        exit;
                     }
                     }

                for ( $i = 0; $i < $files_being_uploaded; $i++ )
                 {
                    // Allow only images
                        $ext = get_ext($_FILES['files']['name'][$i]);
                    $valid_ext = array("jpg","gif","png","jpeg","bmp");

                    if ( !in_array($ext , $valid_ext) )
                     {
                        $this->errors[] = 'You can only upload images.';
                        return false;
                     }
                 }

                for ( $i = 0; $i < $files_being_uploaded; $i++ )
                 {
                    $final_name = strtolower($_FILES['files']['name'][$i]);

                    while ( file_exists( $upload_path.$final_name ) )
                     {
                        $final_name = ++$j.'-'.$final_name;
                     }

                    if ( !move_uploaded_file( $_FILES['files']['tmp_name'][$i] , $upload_path . $final_name ) )
                     {
                        $this->errors[] = 'There was an error moving the file to that directory';
                        return false;
                     }
                    else
                     {
                        $data = array(
                            'name'      => $final_name,
                            'directory' => $relative_path . $final_name,
                            'p_ID'      => $this->_id
                        );

                        $insert_file = $db->insert("uploads",$data);
                     }
                 }
                 }
             }

            $data = array(
                'title' => htmlentities($_POST['title']),
                'content'   => htmlentities($_POST['content']),
                'uploads'   => $current_file_count + $files_being_uploaded,
                'modified'  => 'NOW()'
            );

            $update = $db->update("posts",$data,"`ID` = '".$this->_id."'");

            if ( $update )
             {
                add_notification('"'.$this->_post['title'].'" was sucessfully updated.');
                forward_to(BRIGGLE_DIR.'view/'.$this->_id);
             }
            else
             {
                $this->errors[] = 'There was an error submitting the form to the database.';
                return false;
             }
         }
     }

    public function authors()
     {
        global $user;
        $db = Database::obtain();

        if ( $user->get('type') < 1 )
         {
            forward_to(BRIGGLE_DIR);
         }

        $this->_page    = 'authors';

        $this->_title   = 'Authors';

        $this->_sub_title = '<a href="'.BRIGGLE_DIR.'">Home</a><small>|</small> <a href="'.BRIGGLE_DIR.'authors">Authors</a>';

        $this->_authors = get_users();

        if ( $_POST['delete-author'] )
         {
            $u_id = (int) $_POST['u_ID'];
            delete_author($u_id);

            add_notification( htmlentities($_POST['name']).' was successfully deleted.');
            forward_to(BRIGGLE_DIR.'authors');

         }

        if ( $_POST['add-author'] )
         {
            if ( $user->get('type') < 2 )
             {
                forward_to(BRIGGLE_DIR.'authors');
             }

            if ( !preg_match("/^[a-zA-Z0-9\.]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\.]+$/i",$_POST['email']) )
             {
                $this->errors[] = 'Must enter a valid email';
             }

            /*
             *  Check to make sure the login and email are unique.
            */
            if ( $_POST['add-author'] == 'Add Author' )
             {
                if ( $user->user_exists( $_POST['login'] , $_POST['email'] ) )
                 {
                    $this->errors[] = 'The email or login provided is already in use by another author';
                 }
             }

            /*
            //  If we're editing an author, send the ID of the user we're updating
            //  so we know not to count this ID when checking for duplicates.
            */
            if ( $_POST['add-author'] == 'Edit Author' )
             {
                $u_id = (int) $_POST['u_ID'];

                if ( $user->user_exists( $_POST['login'] , $_POST['email'] , $u_id ) )
                 {
                    $this->errors[] = 'The email or login provided is already in use by another author';
                 }
             }

            if ( !isset($_POST['name']['1']) || isset($_POST['name']['74']) || !preg_match("/^[a-zA-Z]/", $_POST['name']) )
             {
                $this->errors[] = 'Name must be at least 2 characters long';
             }

            if ( !isset($_POST['login']['3']) || isset($_POST['login']['74']) || !preg_match("/^[a-zA-Z\\-\\., \']+$/", $_POST['login']) )
             {
                $this->errors[] = 'Login must be at least 4 characters long and contain only letters and numbers';
             }

            if ( ($_POST['add-author'] != 'Edit Author') && (!isset($_POST['pass']['3']) || isset($_POST['pass']['17'])) )
             {
                $this->errors[] = 'Password must be at least 4 characters long';
             }

            if ( $this->errors )
             {
                return false;
             }

            if ( $_POST['add-author'] == 'Add Author' )
             {

                $data = array(
                    'email' => trim($_POST['email']),
                    'name'      => trim($_POST['name']),
                    'login' => trim($_POST['login']),
                    'pass'      => md5(trim($_POST['pass']).SALT),
                    'notes'     => htmlentities($_POST['notes']),
                    'notify'    => (int) $_POST['notify'],
                    'created'   => 'NOW()',
                    'modified'  => 'NOW()',
                    'type'      => (int) $_POST['type'],
                    'hash'      => ''
                );

                $insert = $db->insert("users",$data);

                if ( $insert )
                 {
                    send_invite( $data , trim($_POST['pass']) );
                    add_notification($data['name'].' was successfully added.');
                    forward_to(BRIGGLE_DIR.'authors');
                 }
                else
                 {
                    $this->errors[] = 'There was an error submitting the form to the database.';
                    return false;
                 }
             }

            if ( $_POST['add-author'] == 'Edit Author' )
             {
                $data = array(
                    'email' => trim($_POST['email']),
                    'login' => trim($_POST['login']),
                    'name'      => trim($_POST['name']),
                    'notes' => htmlentities($_POST['notes']),
                    'notify'    => (int) $_POST['notify'],
                    'modified'  => 'NOW()',
                    'type'      => (int) $_POST['type']
                );

                if ( trim($_POST['pass']) != "" )
                 {
                    $data['pass'] = md5(trim($_POST['pass']).SALT);
                 }

                $update = $db->update("users",$data,"`ID` = '". (int) $_POST['u_ID'] ."'");

                if ( $update )
                 {
                    add_notification($data['name'].' was sucessfully updated.');
                    forward_to(BRIGGLE_DIR.'authors');
                 }
                else
                 {
                    $this->errors[] = 'There was an error submitting the form to the database.';
                    return false;
                 }
             }
         }

     }

    public function account()
     {
        global $user;
        $db = Database::obtain();

        if ( $user->get('type') < 1 )
         {
            forward_to(BRIGGLE_DIR);
         }

        $this->_page = 'account';

        $this->_author = get_user( $user->get('ID') );

        $this->_title = 'My Account';

        $this->_sub_title = '<a href="'.BRIGGLE_DIR.'">Home</a><small>|</small> <a href="'.BRIGGLE_DIR.'account">My Account</a>';


        if ( $_POST['update-account'] )
         {

            if ( !preg_match("/^[a-zA-Z0-9\.]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\.]+$/i",$_POST['email']) )
             {
                $this->errors[] = 'Must enter a valid email';
             }

            if ( $user->user_exists( $_POST['login'] , $_POST['email'] , true ) )
             {
                    $this->errors[] = 'The email or login provided is already in use by another author';
             }

            if ( !isset($_POST['name']['1']) || isset($_POST['name']['74']) || !preg_match("/[^a-zA-Z0-9]/", $_POST['name']) )
             {
                $this->errors[] = 'Name must be at least 2 characters long';
             }

            if ( ($user->get('login') !== trim($_POST['login'])) && (!isset($_POST['login']['3']) || isset($_POST['login']['74']) || !preg_match("/^[A-Za-z\\-\\., \']+$/", $_POST['login'])) )
             {
                $this->errors[] = 'Login must be at least 4 characters long and contain only letters and numbers';
             }

            if ( (trim($_POST['pass'] !== '') && ( !isset($_POST['pass']['3']) || isset($_POST['pass']['17'])) ) )
             {
                $this->errors[] = 'Password must be at least 4 characters long';
             }

            if ( $this->errors )
             {
                return false;
             }

            $data = array(
                'email' => trim($_POST['email']),
                'login' => trim($_POST['login']),
                'name'      => trim($_POST['name']),
                'notes' => htmlentities($_POST['notes']),
                'notify'    => (int) $_POST['notify'],
                'modified'  => 'NOW()'
            );

            if ( trim($_POST['pass']) != '' )
             {
                $data['pass'] = md5(trim($_POST['pass']).SALT);
             }

            $update = $db->update("users",$data,"`ID` = '".$user->get('ID')."'");

            if ( $update )
             {
                add_notification('Your account was successfully updated.');
                forward_to(BRIGGLE_DIR.'account');
             }
            else
             {
                $this->errors[] = 'There was an error submitting the form to the database.';
                return false;
             }
         }

     }


    public function settings()
     {
        global $user;
        $db = Database::obtain();

        if ( $user->get('type') < 3 )
         {
            forward_to(BRIGGLE_DIR);
         }

        $this->_page = 'settings';

        $this->_title = 'Settings';

        $this->_sub_title = '<a href="'.BRIGGLE_DIR.'">Home</a><small>|</small> <a href="'.BRIGGLE_DIR.'settings">Settings</a>';

        $per_page = (int) $_POST['per_page'];
        $company = htmlentities($_POST['company']);
        $private = preg_replace("/[^a-z]/","",$_POST['private']);
        $password = preg_replace("/[^a-zA-Z0-9]/","",$_POST['password']);
        $theme = preg_replace("/[^-a-zA-Z0-9]/","",$_POST['theme']);


        if ( $_POST['update-settings'] )
         {
            $s = $db->query('UPDATE `settings` SET `value` = \''.$db->escape($per_page).'\' WHERE `key` = \'per_page\'');
            $s = $db->query('UPDATE `settings` SET `value` = \''.$db->escape($company).'\' WHERE `key` = \'company\'');
            $s = $db->query('UPDATE `settings` SET `value` = \''.$db->escape($private).'\' WHERE `key` = \'private\'');
            $s = $db->query('UPDATE `settings` SET `value` = \''.$db->escape($password).'\' WHERE `key` = \'password\'');
            $s = $db->query('UPDATE `settings` SET `value` = \''.$db->escape($theme).'\' WHERE `key` = \'theme\'');

            if ( $s )
             {
                add_notification('Settings were successfully updated.');
                forward_to(BRIGGLE_DIR.'settings');
             }
            else
             {
                $this->errors[] = 'There was an error submitting the form to the database.';
                return false;
             }
         }
     }

}
