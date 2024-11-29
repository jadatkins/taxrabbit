<?php

require_once './php/essentials.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
  define('RESPONSE_CODE', 405);
  header('HTTP/1.1 405 Method Not Allowed');
  header('Allow: POST');
  trigger_error("The {$_SERVER['REQUEST_METHOD']} method is not allowed for the requested URL.", E_USER_ERROR);
}

$headings = array_key_exists('headings', $_POST) ? $_POST['headings'] : array();
 $navbox  = array_key_exists( 'navbox',  $_POST) ? $_POST[ 'navbox' ] : TRUE;
  $urls   = array_key_exists(  'urls',   $_POST) ? $_POST[  'urls'  ] : array();

include_once './php/nocache.php';

require './php/actions/common.php';

$need_more_information = FALSE;
$more_clients = array();
$more_realcontacts = array();
$more_jobs = array();
$more_expenses = array();
$hiddenfields = '';

if (array_key_exists('user', $_POST) and is_array($_POST['user'])) {
  require './php/actions/users.php';
  redirect($_POST['goto']);
  exit;
}
if (array_key_exists('btn_delete', $_POST))
  require './php/actions/delete.php';
$tables = array(
  'businesses', 'clients', 'realcontacts',
  'jobs', 'expenses', 'metacontacts'
);
foreach ($tables as $table) {
  if (array_key_exists($table, $_POST) and is_array($_POST[$table])) {
    foreach ($_POST[$table] as $a_record) {
      if (!array_key_exists('user_id', $a_record))
        continue;
      if (!defined('AUTHUID') and intval($user_id = $a_record['user_id']) > 0)
        require_once './php/identify/authenticate.php';
      if (AUTHUID != $a_record['user_id']) {
        define('RESPONSE_CODE', 403);
        header('HTTP/1.1 403 Forbidden');
        trigger_error("It looks like you're trying to update records for several users at once.", E_USER_ERROR);
        die;
      }
    }
    require "./php/actions/$table.php";
  }
}
unset($tables, $table, $a_record);

require './php/actions/buttons.php';

if ($need_more_information) {
  require './php/actions/generate.php';
} else {
  redirect($_POST['goto']);
}
