<?php

defined('_GO_') or die("Direct access disallowed.");

function settings()
 {
    $db = Database::obtain();
    $s = $db->fetch_array("SELECT * FROM settings;");

    foreach ($s as $key => $val)
     {
        $setting[$val['key']] = $val['value'];
     }

    return $setting;
 }
$setting = settings();

function clean( $text )
 {
    $text = strip_tags(strtolower($text));
    $code_entities_match = array(' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','.','*','+','~','`','=');
    $code_entities_replace = array('-','-','','','','','','','','','','','','','','','','','','','','','','','');
    $text = str_replace($code_entities_match, $code_entities_replace, $text);

    return substr($text,1);
 }

function forward_to( $url )
 {
    header('Location: '.$url);

    // Due to a funky setting (aka, intentional bug), we have to exit
    // so the session stays intact and keep the notification.
    exit;
 }

function user_logged_in()
 {
    global $user;

    if ( $user->is_loaded() )
     {
    return true;
     }
    else
     {
    return  false;
     }
 }

function get_ext( $f )
 {
    return strtolower(end(explode(".", $f)));
 }

function delete_post( $id )
 {
    $db = Database::obtain();
    $r = $db->query("DELETE FROM `posts` WHERE `ID` = '{$id}'");
    $r = $db->query("DELETE FROM `comments` WHERE `p_ID` = '{$id}'");
    $r = $db->query("DELETE FROM `uploads` WHERE `p_ID` = '{$id}'");

    return $r;
 }

function delete_upload( $id )
 {
    $db = Database::obtain();
    $r = $db->query("DELETE FROM `uploads` WHERE `ID` = '{$id}'");

    return $r;
 }

function delete_comment( $id , $p_id , $mod = true )
 {
    $db = Database::obtain();
    $r = $db->query("DELETE FROM `comments` WHERE `ID` = '{$id}'");

    if ($r && $mod)
     {
        add_comment_count( $p_id , "-1");
     }

    return $r;
 }

function delete_author( $id )
 {
    $db = Database::obtain();

    $r = $db->query("DELETE FROM `users` WHERE `ID` = '{$id}'");

    if ( $r )
     {
        $r = $db->query("DELETE FROM `posts` WHERE `u_ID` = '{$id}'");
        $r = $db->query("DELETE FROM `comments` WHERE `u_ID` = '{$id}'");
        $r = $db->query("DELETE FROM `uploads` WHERE `p_ID` = '{$id}'");
     }

    return $r;
 }

function add_comment_count( $id , $var = "+1" )
 {
    $db = Database::obtain();

    return $db->query("UPDATE `posts` SET `comments` = `comments` ".$var." WHERE `ID` = '{$id}';");
 }

function add_notification( $t )
 {
    session_start();
    $_SESSION['notification'] = stripslashes($t);
 }

function get_notification()
 {
    session_start();
    if ( isset($_SESSION['notification']) )
     {
        echo '<div id="notification" class="hide"><span>'.$_SESSION['notification'].'</span><a href="javascript:;" class="close-notify" onclick="close_notice()">X</a></div>';
        unset($_SESSION['notification']);
     }
 }

function send_invite( $author , $pass)
 {
    global $setting;

    $subject = 'Website Change Reqest';

    $headers = "From: no-reply@".$_SERVER['SERVER_NAME'] . "\r\n";
    $headers .= "Reply-To: no-reply@" . $_SERVER['SERVER_NAME'] . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    $message = '<html><body>';
    $message .= '<h2>Account created at '.$setting['company'].'!</h2>';
    $message .= '<p>Dear '.$author['name'].',</p>';
    $message .= '<p>You have been added as an author to '.$setting['company'].' and may start posting immediately. To do this just log in using either your Email or Login name at the web.</p>';
    $message .= '<p>
        <strong>Website - </strong> <a href="'.BRIGGLE_DIR.'">'.BRIGGLE_DIR.'</a><br />
        <strong>Email - </strong> '.$author['email'].'<br />
        <strong>Login - </strong> '.$author['login'].'<br />
        <strong>Password - </strong> '.$pass.'</p>';
    $message .= '<p>Cheers!</p>';
    $message .= '</body></html>';


    return mail( $author['email'] , "Invitation to join ".BRIGGLE_DIR , $message , $headers );
 }

function send_notifications( $id , $by , $title )
 {
    global $user, $setting;
    $db = Database::obtain();

    $notify_email = $db->fetch_array("SELECT * FROM `users` WHERE `notify` = '1';");

    $author = $user->get('name');
    $message = $author.' just wrote a new post on '.$setting['company'].' titled "'.$title.'", available at '.BRIGGLE_DIR.'view/'.$id;

    $headers = "From: no-reply@".$_SERVER['SERVER_NAME']."\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    foreach ($notify_email as $to)
     {
        if ( $to['ID'] != $by )
         {
            mail ( $to['email'] , 'New Post on '.$setting['company'] , $message , $headers );
         }
     }
 }

function format_title( $str )
 {
    $str = stripslashes($str);

    if ( strlen($str) > 65 )
     {
        return substr($str,0,strpos(wordwrap($str, 65), "\n")).'...';
     }
    else
     {
        return $str;
     }
 }

function format_post( $str )
 {
    $str = stripslashes(str_replace(array("\r\n", "\r", "\n"), "<br />", $str));

    if( $str=='' || !preg_match('/(ftp|http|www\.|@)/i', $str) )
     {
        return $str;
     }

    $str = preg_replace("/([ \t]|^)www\./i", "\\1http://www.", $str);
    $str = preg_replace("/([ \t]|^)ftp\./i", "\\1ftp://ftp.", $str);
    $str = preg_replace("/(http:\/\/[^ )\r\n!]+)/i", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $str);
    $str = preg_replace("/(https:\/\/[^ )\r\n!]+)/i", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $str);
    $str = preg_replace("/(ftp:\/\/[^ )\r\n!]+)/i", "<a href=\"\\1\">\\1</a>", $str);
    $str = preg_replace("/([-a-z0-9_]+(\.[_a-z0-9-]+)*@([a-z0-9-]+(\.[a-z0-9-]+)+))/i", "<a href=\"mailto:\\1\">\\1</a>", $str);

    return $str;
 }

function rmkdir( $path, $mode = 0755 )
 {
    $path = rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", $path), "/");
    $e = explode("/", ltrim($path, "/"));

    if(substr($path, 0, 1) == "/")
     {
        $e[0] = "/".$e[0];
     }

    $c = count($e);
    $cp = $e[0];

    for($i = 1; $i < $c; $i++)
     {
        if(!is_dir($cp) && !mkdir($cp, $mode))
         {
            return false;
         }
        $cp .= "/".$e[$i];
     }

    return mkdir($path, $mode);
 }

function get_message( $msg , $type , $close = false , $lead = true )
 {

    switch ($type)
     {
        case "success":

            echo '<div class="msg success">';

            if ($close == true)
             {
                echo '<a href="javascript:;" class="close-message">x</a>';
             }

            echo $msg.'</div>';

        break;

        case "error":

            echo '<div class="msg error">';

            if ($close == true)
             {
                echo '<a href="javascript:;" class="close-message">x</a>';
             }

            if (is_array($msg))
             {
                $count = count($msg);

                if ( $lead )
                 {
                    echo 'The following error'.( $count == 1 ? '' : 's' ).' occured: ';
                 }

                if ( $count == 1 )
                 {
                    echo '<strong>'.$msg['0'].'</strong>.';
                 }
                else
                 {
                    foreach ($msg as $m)
                     {
                        ++$i;
                        if ( $i != $count )
                         {
                            echo ' <strong>'.$m.'</strong>';
                         }
                        else
                         {
                            echo ' and <strong>'.$m.'</strong>.';
                         }
                        if ( ($i + 1) != $count && $i != $count )
                         {
                            echo ', ';
                         }
                     }
                 }
             }
            else
             {
                echo $msg;
             }

            echo '</div>';

        break;

        case "info":

            echo '<div class="msg info">';

            if ($close == true)
             {
                echo '<a href="javascript:;" class="close-message">x</a>';
             }

            echo $msg.'</div>';

        break;

        case "warning":

            echo '<div class="msg warning">';

            if ($close == true)
             {
                echo '<a href="javascript:;" class="close-message">x</a>';
             }

            echo $msg.'</div>';


        break;

        default:

         echo $msg;

        break;
     }
 }

function get_execution_time( $s )
 {
    return number_format((microtime(true) - $s) , 4);
 }

function get_memory() {
    return number_format(memory_get_usage() / 1024 / 1024, 2);
}

function get_themes()
 {
    $folders = scandir('bc-content/themes/');

    foreach ($folders as $folder)
     {
        if ( !strstr(".", $folder ) && !strstr("..", $folder ) )
         {
            if ( is_dir('bc-content/themes/' . $folder) )
         {
            $array[] = $folder;
         }
         }
     }

    return $array;
 }

function get_copyright( $start = '2011' )
 {
    global $setting;

    $start_year = intval($start);
    $current_year = intval(date('Y'));
    $s = 'Copyright '.$setting['company'].' | ';
    if ($current_year > $start_year)
        $s .= $start_year .'-'. $current_year;
    else
        $s .= $start_year;
    return $s;
 }

function get_next_id( $t = "posts" )
 {
    $db = Database::obtain();
    $r = $db->query_first("SHOW TABLE STATUS LIKE '{$t}';");

    return $r['Auto_increment'];
 }

function get_var( $col = "" , $table = "" , $where = ""  )
 {
    $db = Database::obtain();

    $r = $db->query("SELECT `{$col}` FROM `{$table}` WHERE {$where};");
    return $r[$col];
 }

function get_user( $id )
 {
    $db = Database::obtain();

    return $db->query_first("SELECT * FROM `users` WHERE `ID` = {$id};");
 }

function get_type( $type )
 {
    switch ($type)
     {
        case 3:
            return 'Administrator';
        break;

        case 2:
            return 'Editor';
        break;

        case 1:
            return 'Author';
        break;

        default:
            return 'Guest';
        break;
     }
 }

function get_users( $where = "" )
 {
    $db = Database::obtain();

    if ($where != "")
     {
        $where = " AND {$where}";
     }
    return $db->fetch_array("SELECT * FROM `users` WHERE `type` > 0{$where};");
 }

function get_post( $id )
 {
    $db = Database::obtain();

    return $db->query_first("SELECT `posts`.*, `users`.`name` AS 'author' FROM `posts`,`users` WHERE `posts`.`ID` = '{$id}' AND `users`.`ID` = `posts`.`u_ID`;");

 }

function get_posts( $p = 1 )
 {
    global $setting;
    $db = Database::obtain();

    $p = ( $p-1 ) * $setting['per_page'];
    $l = $setting['per_page'];

    return $db->fetch_array("SELECT `posts`.*, `users`.`name` AS 'author' FROM `posts`,`users` WHERE `users`.`ID` = `posts`.`u_ID` ORDER BY `posts`.`ID` DESC LIMIT {$p},{$l};");
 }

function get_comments( $id )
 {
    $db = Database::obtain();

    return $db->fetch_array("SELECT `comments`.*, `users`.`name` AS 'author' FROM `comments`,`users` WHERE `comments`.`p_ID` = '{$id}' AND `users`.`ID` = `comments`.`u_ID` ORDER BY `comments`.`date` DESC;");
 }

function get_post_author( $id )
 {
    $db = Database::obtain();
    $r = $db->query_first("SELECT `u_ID` FROM `posts` WHERE `ID` = '{$id}';");

    return $r['u_ID'];
 }

function get_uploads( $id = NULL )
 {
    $db = Database::obtain();
    if ( $id )
     {
        $r = $db->fetch_array("SELECT * FROM `uploads` WHERE `p_ID` = '{$id}'");
     }
    else
     {
        $r = $db->fetch_array("SELECT * FROM `uploads` WHERE `p_ID` = '{$id}'");
     }

    return $r;
 }

function get_num_of( $table_name )
 {
    $db = Database::obtain();
    $r = $db->query_first("SELECT COUNT(1) as 'count' FROM `{$table_name}`");
    return $r['count'];
 }

function get_pages( $k , $p , $s = true)
 {
    global $setting;

    $last = ceil( get_num_of( $k ) / $setting['per_page'] );

    $output .= '<div class="pagination">';
    if ( $s )
     {
        $k = '';
     }
    else
     {
        $k = $k.'/';
     }

    if ( $p > 1 )
     {
        $output .= '<a href="'.BRIGGLE_DIR.$k.'/page/1'.'">First</a>';
        $output .= '<a href="'.BRIGGLE_DIR.$k.'/page/'.($p-1).'">Prev</a>';
     }
    else
     {
        $output .= '<span>First</span>';
        $output .= '<span>Prev</span>';
     }

    for ( $i = 1; $i <= $last; $i++ )
     {
        if ( $i == $p )
         {
            $output .= '<span class="current">'.$i.'</span>';
         }
        else
         {
            $output .= '<a href="'.BRIGGLE_DIR.$k.'/page/'.$i.'">'.$i.'</a>';
         }
     }

    if ( $p < $last )
     {
        $output .= '<a href="'.BRIGGLE_DIR.$k.'page/'.($p+1).'">Next</a>';
        $output .= '<a href="'.BRIGGLE_DIR.$k.'page/'.$last.'">Last</a>';
     }
    else
     {
        $output .= '<span>Next</span>';
        $output .= '<span>Last</span>';
     }

    $output .= '</div>';

    echo $output;
 }

function execution_queries( $v = "Generated %s database queries." )
 {
    $db = Database::obtain();
    return sprintf($v, $db->num_queries);
 }

function relative( $time = false , $limit = 86400 , $format = 'M jS' )
 {
    if (empty($time) || (!is_string($time) && !is_numeric($time))) $time = time();
    elseif (is_string($time)) $time = strtotime($time);

    $now = time();
    $relative = '';

    if ($time === $now)
     {
         $relative = 'right now';
     }
    elseif ($time > $now)
     {
        $relative = date($format, $time);
     }
    else
     {
        $diff = $now - $time;
        if ($diff >= $limit)
         {
            $relative = date($format, $time);
         }
        elseif ($diff < 60)
         {
            $relative = 'less than a minute ago';
         }
        elseif (($minutes = ceil($diff/60)) < 60)
         {
            $relative = $minutes.' minute'.(((int)$minutes === 1) ? '' : 's').' ago';
         }
        else
         {
            $hours = ceil($diff/3600);
            $relative = $hours.' hour'.(((int)$hours === 1) ? '' : 's').' ago';
         }
    }

    return $relative;
 }
