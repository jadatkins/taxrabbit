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

do {

  if (! array_key_exists('del_table', $_POST)) break;
  if (! array_key_exists('del_usrid', $_POST)) break;
  if (! array_key_exists('del_busid', $_POST)) break;
  if (! array_key_exists('del_recid', $_POST)) break;
  if (preg_match('/^(\d+)|(\d+)|(\d+)$/',
          "{$_POST['del_usrid']}|{$_POST['del_busid']}|{$_POST['del_recid']}",
          $information)
  ) {
    $del_busid = intval($_POST['del_busid']);
    $del_recid = intval($_POST['del_recid']);
  } else {
    break;
    die;
  }

  $user_id = intval($_POST['del_usrid']);
  require_once './php/identify/authenticate.php';
  if (AUTHUID != $_POST['del_usrid']) die;

  switch ($singular = $_POST['del_table']) {
    case 'job':
      $plural = 'jobs';
      break;
    case 'expense':
      $plural = 'expenses';
      break;
    default:
      break 2;
  }

  $friendlyname = $_POST['friendlyname'];
  treat_input_string($friendlyname);

  if (!$db_connection->query("DELETE FROM $plural WHERE user_id=$user_id AND "
        . "business_id=$del_busid AND {$singular}_id=$del_recid")) {
    trigger_error("I wasn't able to delete the $singular &lsquo;" . htmlify($friendlyname) .
        "&rsquo;.&nbsp; Here's the error message:<br />\n" . $db_connection->error, E_USER_ERROR);
  }
  $db_connection->query("INSERT INTO messages (user_id,tie,msg) VALUES($user_id," .
      yellowboxtie($user_id) . ',' . wrap_for_sql("I have deleted the $singular ‘{$friendlyname}’") . ')');

  if ($singular == 'job') {
    if (!$db_connection->query("UPDATE expenses SET job_id=NULL WHERE user_id=$user_id AND "
          . "business_id=$del_busid AND job_id=$del_recid")) {
      trigger_error("I've deleted the job &lsquo;" . htmlify($friendlyname) .
          "&rsquo;, but I haven't managed to sever its related expenses, so they " .
          "now show as belonging to a job that doesn't exist.&nbsp; Here's " .
          "the error message:<br />\n" . $db_connection->error, E_USER_ERROR);
    }
  }

  redirect($_POST['goto']);

} while (false);

trigger_error("I don't understand what you're trying to do.&nbsp; It sounds" .
    " like you want to delete something, but I couldn't make sense of what " .
    "you're asking.&nbsp; There must be something wrong with the page you've".
    " just come from.&nbsp; Try something else.", E_USER_ERROR);
