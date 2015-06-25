<?php

defined('_GO_') or die("Direct access disallowed.");

$db = Database::obtain(DB_HOST, DB_USER, DB_PASS, DB_NAME);

class Database {

    private static  $_instance;

    private static  $_server;
    private static  $_user;
    private static  $_pass;
    private static  $_database;
    private static  $_last;
    private static  $_error;
    private static  $_linkID = 0;
    private static  $_queryID = 0;

    public static   $debug = false;
    public static   $affected_rows = 0;
    public static   $num_queries = 0;


    private function __construct( $_server = null, $_user = null, $_pass = null, $_database = null )
     {
        $this->_server  = $_server;
        $this->_user    = $_user;
        $this->_pass    = $_pass;
        $this->_database= $_database;

        try {
            $this->connect();
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
     }


    public static function obtain( $_server = null, $_user = null, $_pass = null, $_database = null )
     {
        if ( !self::$_instance )
         {
            self::$_instance = new Database($_server, $_user, $_pass, $_database);
         }

        return self::$_instance;
     }


    public function connect( $new_link = false )
     {
        $this->_linkID = mysql_connect($this->_server, $this->_user, $this->_pass, $new_link);

        if ( !$this->_linkID )
         {
            throw new Exception('Could not connect to Database');
         }

        if ( !mysql_select_db($this->_database, $this->_linkID) )
         {
            throw new Exception("Could not open database: <b>$this->_database</b>.");
         }

        $this->_server  = '';
        $this->_user    = '';
        $this->_pass    = '';
        $this->_database= '';
     }


    public function close()
     {
        if ( !mysql_close($this->_linkID) )
         {
            throw new Exception('Connection close failed.');
         }
     }


    public function escape( $string )
     {
        return mysql_real_escape_string($string);
     }

    public function query( $sql )
     {
        try {
            $this->_queryID = mysql_query($sql, $this->_linkID);
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        $this->_last = $sql;
        $this->num_queries++;
        $this->affected_rows = mysql_affected_rows($this->_linkID);

        return $this->_queryID;
     }

    public function query_first( $query_string )
     {
        $_queryID = $this->query($query_string);
        $out = $this->fetch($_queryID);
        $this->free_result($_queryID);

        return $out;
     }

    public function query_var( $query_string, $var = "ID" )
     {
        $_queryID = $this->query($query_string);
        $out = $this->fetch($_queryID);
        $this->free_result($_queryID);

        return $out[$var];
     }

    public function fetch( $_queryID = -1 )
     {
        if ( $_queryID != -1 )
         {
            $this->_queryID=$_queryID;
         }

        if ( isset($this->_queryID) )
         {
            $record = mysql_fetch_assoc($this->_queryID);
         }

        return $record;
     }

    public function fetch_array( $sql )
     {
        $_queryID = $this->query($sql);
        $out = array();

        while ( $row = $this->fetch($_queryID) )
         {
            $out[] = $row;
         }

        $this->free_result($_queryID);

        return $out;
     }

    public function update( $table, $data, $where = '1' )
     {
        $q="UPDATE `$table` SET ";

        foreach ($data as $key=>$val)
         {
            if (strtolower($val)=='null') $q.= "`$key` = NULL, ";
            elseif (strtolower($val)=='now()') $q.= "`$key` = NOW(), ";
            elseif (preg_match("/^increment\((\-?\d+)\)$/i", $val, $m)) $q.= "`$key` = `$key` + $m[1], ";
            else $q.= "`$key`='".$this->escape($val)."', ";
         }

        $q = rtrim($q, ', ') . ' WHERE '.$where.';';

        return $this->query($q);
     }

    public function insert( $table, $data )
     {
        $q = "INSERT INTO `$table` ";
        $v = ''; $n = '';

        foreach ( $data as $key=>$val )
         {
            $n.="`$key`, ";
            if ( strtolower($val)=='null' ) $v.="NULL, ";
            elseif ( strtolower($val)=='now()' ) $v.="NOW(), ";
            else $v.= "'".$this->escape($val)."', ";
         }

        $q .= "(". rtrim($n, ', ') .") VALUES (". rtrim($v, ', ') .");";

        if ( $this->query($q) )
         {
            return mysql_insert_id($this->_linkID);
         }
        else
         {
            return false;
         }
     }

    public function last()
     {
        return $this->_last;
     }

    private function free_result( $_queryID = -1 )
     {
        if ( $_queryID != -1 )
         {
            $this->_queryID=$_queryID;
         }

        if ( $this->_queryID != 0 && !mysql_free_result($this->_queryID) )
         {
            throw new Exception("Result ID: <b>$this->_queryID</b> could not be freed.");
         }
     }

}
