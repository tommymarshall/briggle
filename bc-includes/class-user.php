<?php

defined('_GO_') or die("Direct access disallowed.");

$user = new Access();

class Access {

    private $values = array(
        'ID'        => 'ID',
        'login' => 'login',
        'email' => 'email',
        'name'      => 'name',
        'notes' => 'notes',
        'type'  => 'type',
        'hash'  => 'hash',
        'created'   => 'created',
        'modified'  => 'modified',
        'pass'  => 'pass'
    );
    private $_remTime = 2592000;
    private $_remCookieDomain = '';
    private $_displayErrors = true;
    private $_ID;
    private $_data;

    public  $errors;

    public function __construct()
     {
        $db = Database::obtain();

        $this->_remCookieDomain = $this->_remCookieDomain == '' ? $_SERVER['HTTP_HOST'] : $this->_remCookieDomain;

        if( !isset($_SESSION) )
         {
            session_start();
         }

        if ( !empty($_SESSION['SessionValue']) )
         {
            $this->loadUser( $_SESSION['SessionValue'] );
         }

        if ( isset($_COOKIE['ckSavePass']) && !$this->is_loaded() )
         {
            $u = unserialize(base64_decode($_COOKIE['ckSavePass']));
            $this->login($u['user'], $u['pass']);
         }
     }

    public function login( $user , $pass , $remember = false , $loadUser = true )
    {
        global $setting;
        $db = Database::obtain();

        if ($_SESSION['token'] != $_POST['token'] )
         {
            $this->errors[] = 'Invalid submission';
            return false;
         }

        if ( isset($_POST['guest-login']) )
         {
            $pass   = $db->escape($_POST['guest']);

            if ( trim($_POST['guest']) == '' )
             {
                $this->errors[] = 'Must enter a password';
                return false;
             }

            if ( $setting['password'] == '' )
             {
                $this->errors[] = 'Guest account is not currently set up';
                return false;
             }


            if ( trim($_POST['guest']) == $setting['password'])
             {
                 $res = $db->query("SELECT * FROM `users` WHERE `{$this->values['login']}` = 'guest' LIMIT 1");
             }
            else
             {
                $this->errors[] = 'Incorrect password';
                return false;
             }
         }
        else
         {

            $user       = $db->escape($user);
            $original   = $db->escape($pass);
            $pass       = md5($original.SALT);

            $res = $db->query("SELECT * FROM `users` WHERE (`{$this->values['login']}` = '$user' OR `{$this->values['email']}` = '$user') AND `{$this->values['pass']}` = '$pass' LIMIT 1");

         }

        if ( $db->affected_rows == 0)
         {
            $this->errors[] = 'Incorrect username/password';
            return false;
         }

        $this->_data = $db->fetch($res);

        if ( $this->_data['type'] < 0 )
         {
            $this->errors[] = 'That account is currently inactive';
            return false;
         }

        if ( $loadUser )
         {
            $this->_ID = $this->_data[$this->values['ID']];
            $_SESSION['SessionValue'] = $this->_ID;

            if ( $remember == "on" )
             {
              $cookie = base64_encode(serialize(array('user'=>$user,'pass'=>$original)));
              $a = setcookie('ckSavePass', $cookie,time()+$this->_remTime, '/', $this->_remCookieDomain);
             }
         }

        return true;
    }

    public function logout( $redirectTo = '' )
     {
        setcookie('ckSavePass', '', time()-3600,'/',$this->_remCookieDomain);
        $_SESSION['SessionValue'] = '';
        $this->_data = '';

        if ( $redirectTo != '' && !headers_sent())
         {
            header('Location: '.$redirectTo );
            exit;
         }
     }

    public function user_exists( $login , $email , $val = 0)
     {
        $db = Database::obtain();
        $login = preg_replace("/[^a-zA-Z]/", "", ($login));

        if ( $val === 0)
         {
            $r = $db->query_first("SELECT * FROM `users` WHERE `login` = '{$login}' OR `email` = '{$email}';");
         }
        else
         {
            $r = $db->query_first("SELECT * FROM `users` WHERE (`login` = '{$login}' OR `email` = '{$email}') AND `ID` != '{$val}';");
         }

        return $r;
     }

    public function is_loaded()
     {
        return empty( $this->_ID ) ? false : true;
     }

    public function get($property)
     {
        if ( empty($this->_ID) ) return false;
        if ( !isset($this->_data[$property]) ) return false;

        return $this->_data[$property];
     }

    public function loadUser( $id )
     {
        $db = Database::obtain();

        $this->_data = $db->query_first("SELECT * FROM `users` WHERE `{$this->values['ID']}` = '".$db->escape($id)."' LIMIT 1");
        if ( $db->affected_rows == 0 )
         {
          return false;
         }

        $this->_ID = $id;
        $_SESSION['SessionValue'] = $this->_ID;

        return true;
     }
}
