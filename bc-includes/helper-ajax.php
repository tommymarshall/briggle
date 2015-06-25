<?php

define('_GO_',1);
//-----------------------------------------------------------
//  Load Required Classes/Functions
//-----------------------------------------------------------
require 'configuration.php';
require 'class-database.php';
require 'class-user.php';
require 'functions.php';

switch ($_REQUEST['action'])
 {

    case 'get_user':

        $id = (int) $_REQUEST['id'];

        $data = get_user($id);
        $data['name'] = stripslashes($data['name']);
        $data['notes'] = stripslashes($data['notes']);

        if ( !$data )
         {
            return false;
         }
        else
         {
            echo json_encode($data);
         }

    break;

    case 'delete_comment':

        $id = (int) $_REQUEST['id'];
        $p_id = (int) $_REQUEST['p_id'];

        if ( delete_comment($id , $p_id) )
         {
            $data['result'] = 'true';
         }
        else
         {
            $data['result'] = 'false';
         }

        echo json_encode($data);

    break;

    default:
        echo '<big>Error! The following fields were sent:</big><br />';
        echo '<pre class="debug">';
        print_r($_REQUEST);
        echo '</pre>';
    break;

 }
