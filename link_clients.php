<?php

$headings = array_key_exists('headings', $_POST) ? $_POST['headings'] : array();
 $navbox  = array_key_exists( 'navbox',  $_POST) ? $_POST[ 'navbox' ] : TRUE;
  $urls   = array_key_exists(  'urls',   $_POST) ? $_POST[  'urls'  ] : array();

require_once './php/essentials.php';
include_once './php/nocache.php';

if (empty($_POST)) redirect('');

foreach (array('user_id', 'realcontact_id') as $part) {
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

if (!array_key_exists('clients', $_POST)) $_POST['clients'] = array();

$busres = $db_connection->query("SELECT business_id,business_name " .
                          "FROM businesses WHERE user_id={$_POST['user_id']}");
if (! $busres)
  trigger_error("I'm having trouble going through your businesses.&nbsp; " .
    "The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);

while ($business = $busres->fetch_assoc()) {

  $business_id = intval($business['business_id']);
  $business_name = htmlify(truncate($business['business_name']));
  $thebusiness = "the business &lsquo;$business_name&rsquo;";

  if (array_key_exists($business_id, $_POST['clients']))
    $post_clients = $_POST['clients'][$business_id];
  else
    $post_clients = array();

  foreach ($post_clients as $client_id) {
    if (! preg_match('/^\d+$/', $client_id))
      trigger_error("&lsquo;$client_id&rsquo; isn't a valid client_id.", E_USER_ERROR);
  }

  if (array_key_exists('contact_name', $_POST)) {
    $thecontact = "the contact &lsquo;{$_POST['contact_name']}&rsquo;";
    $contact_ybox = "the contact ‘"
        . $db_connection->real_escape_string(truncate($_POST['contact_name'])) . "’";
  } else {
    $thecontact = "contact $numero {$_POST['realcontact_id']}";
    $contact_ybox = "contact No. {$_POST['realcontact_id']}";
  }

  $result = $db_connection->query('SELECT client_id FROM metacontacts WHERE user_id=' . $_POST['user_id'] .
                " AND business_id=$business_id AND realcontact_id={$_POST['realcontact_id']}");
  if ($result === FALSE) trigger_error(
    "I was just trying to check which clients from $thebusiness were already linked to $thecontact, and something went wrong.&nbsp; Here's the
     error message:<br />\n" . $db_connection->error, E_USER_ERROR);
  $existing = array();
  while ($mini = $result->fetch_row()) {
    $existing[] = $mini[0];
  }
  $result->free();
  unset($result);

  $insert = array_diff($post_clients, $existing);
  $delete = array_diff($existing, $post_clients);

  // magic line that makes it work
  $insert = unserialize(serialize($insert));

  foreach ($delete as $client_id) {
    $result = $db_connection->query("DELETE FROM metacontacts WHERE user_id={$_POST['user_id']} AND business_id=$business_id" . 
                  " AND client_id=$client_id AND realcontact_id={$_POST['realcontact_id']}");
    if ($result === FALSE) trigger_error(
      "I didn't manage to dissociate the deselected clients from $thecontact.&nbsp; Here's the
       error message:<br />\n" . $db_connection->error, E_USER_ERROR);
  }
  unset($result, $client_id);

  foreach ($insert as $client_id) {
    $result = $db_connection->query("INSERT INTO metacontacts (user_id,business_id,client_id,realcontact_id)" .
                  "VALUES({$_POST['user_id']},$business_id,$client_id,{$_POST['realcontact_id']})");
    if ($result === FALSE) trigger_error(
      "I couldn't link the clients you specified with $thecontact.&nbsp; Here's the
       error message:<br />\n" . $db_connection->error, E_USER_ERROR);
  }
  unset($result, $client_id);

}

$busres->free();
unset($busres);

$db_connection->query("INSERT INTO messages (user_id,tie,msg) VALUES({$_POST['user_id']},"
    . yellowboxtie($user_id) . ",'I\'ve recorded which clients are linked to $contact_ybox.')");

redirect($_POST['goto']);
