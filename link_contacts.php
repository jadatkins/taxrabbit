<?php

$headings = array_key_exists('headings', $_POST) ? $_POST['headings'] : array();
 $navbox  = array_key_exists( 'navbox',  $_POST) ? $_POST[ 'navbox' ] : TRUE;
  $urls   = array_key_exists(  'urls',   $_POST) ? $_POST[  'urls'  ] : array();

require_once './php/essentials.php';
include_once './php/nocache.php';

if (empty($_POST)) redirect('');

foreach (array('user_id', 'business_id', 'client_id') as $part) {
  if (!array_key_exists($part, $_POST))
    friendly_error("No $part was specified. There must be something wrong with the page you've just come from.", TRUE);
  if (preg_match('/^\d+$/', $_POST[$part]))
    settype($_POST[$part], 'integer');
  else
    trigger_error("&lsquo;{$_POST[$part]}&rsquo; isn't a valid $part.", E_USER_ERROR);
}

if (! defined('AUTHUID') and ($user_id = $_POST['user_id']) > 0)
  require_once './php/identify/authenticate.php';
if (AUTHUID != $_POST['user_id']) die;

if (!array_key_exists('realcontacts', $_POST)) $_POST['realcontacts'] = array();
foreach ($_POST['realcontacts'] as &$realcontact_id) {
  if (! preg_match('/^\d+$/', $realcontact_id))
    trigger_error("&lsquo;$realcontact_id&rsquo; isn't a valid realcontact_id.", E_USER_ERROR);
}

if (array_key_exists('business_name', $_POST)) {
  $thebusiness = "the business &lsquo;{$_POST['business_name']}&rsquo;";
  $business_ybox = "the business ‘"
      . $db_connection->real_escape_string(truncate($_POST['business_name'])) . "’";
} else {
  $thebusiness = "business $numero {$_POST['business_id']}";
  $business_ybox = "business No. {$_POST['business_id']}";
}
if (array_key_exists('client_name', $_POST)) {
  $theclient = "the client &lsquo;{$_POST['client_name']}&rsquo;";
  $client_ybox = "the client ‘"
      . $db_connection->real_escape_string(truncate($_POST['client_name'])) . "’";
} else {
  $theclient = "client $numero {$_POST['client_id']}";
  $client_ybox = "client No. {$_POST['client_id']}";
}

$result = $db_connection->query('SELECT realcontact_id FROM metacontacts WHERE user_id=' . $_POST['user_id'] .
              " AND business_id={$_POST['business_id']} AND client_id={$_POST['client_id']}");
if ($result === FALSE) trigger_error(
  "I was just trying to check which contacts from $thebusiness were already linked to $theclient, and something went wrong.&nbsp; Here's the
   error message:<br />\n" . $db_connection->error, E_USER_ERROR);
$existing = array();
while ($mini = $result->fetch_row()) {
  $existing[] = $mini[0];
}
$result->free();
unset($result);

$insert = array_diff($_POST['realcontacts'], $existing);
$delete = array_diff($existing, $_POST['realcontacts']);

// magic line that makes it work
$insert = unserialize(serialize($insert));

foreach ($delete as $realcontact_id) {
  $result = $db_connection->query("DELETE FROM metacontacts WHERE user_id={$_POST['user_id']} AND business_id={$_POST['business_id']}" . 
                " AND client_id={$_POST['client_id']} AND realcontact_id=$realcontact_id");
  if ($result === FALSE) trigger_error(
    "I didn't manage to dissociate the deselected contacts from $theclient.&nbsp; Here's the
     error message:<br />\n" . $db_connection->error, E_USER_ERROR);
}
unset($result, $realcontact_id);

foreach ($insert as $realcontact_id) {
  $result = $db_connection->query("INSERT INTO metacontacts (user_id,business_id,client_id,realcontact_id)" .
                "VALUES({$_POST['user_id']},{$_POST['business_id']},{$_POST['client_id']},$realcontact_id)");
  if ($result === FALSE) trigger_error(
    "I couldn't link the contacts you specified with $theclient.&nbsp; Here's the
     error message:<br />\n" . $db_connection->error, E_USER_ERROR);
}
unset($result, $realcontact_id);

$db_connection->query("INSERT INTO messages (user_id,tie,msg) VALUES({$_POST['user_id']},"
    . yellowboxtie($user_id) . ",'I\'ve recorded which contacts are linked to $client_ybox.')");

redirect($_POST['goto']);
