<?php

function btn_rc_go_nowhere() {
  global $user_id, $realcontact_id;
  if (array_key_exists('HTTP_REFERER', $_SERVER) and
      !empty($_SERVER['HTTP_REFERER'])
      and substr($_SERVER['HTTP_REFERER'], -10) != 'action.php') {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
  } else {
    $_POST['goto'] = "contact.php?user_id=$user_id&realcontact_id=$realcontact_id";
  }
}

if     (array_key_exists('btn_contact_chg', $_POST))
  $realcontact_id = key($_POST['btn_contact_chg']);
elseif (array_key_exists('btn_contact_new', $_POST))
  $realcontact_id = key($_POST['btn_contact_new']);
elseif (array_key_exists('btn_contact_del', $_POST))
  $realcontact_id = key($_POST['btn_contact_del']);
else
  $realcontact_id = key($_POST['btn_contact_gto']);

$realcontact = $_POST['realcontacts'][$realcontact_id];

if (array_key_exists('user_id', $realcontact))
  $user_id = $realcontact['user_id'];

// replace $realcontact_id (if alias) with the real ID number from auto_increment
if (array_key_exists('realcontact_id', $realcontact))
  $realcontact_id = $realcontact['realcontact_id'];
// see action_clean_input() in /php/actions/common.php


if (array_key_exists('btn_contact_chg', $_POST)) {
  $_POST['goto'] = "contact_clients.php?user_id=$user_id&realcontact_id=$realcontact_id";
}

if (array_key_exists('btn_contact_new', $_POST)) {
  if ($realcontact['business_id'] == '') {
    btn_rc_go_nowhere();
  } else {
    $need_more_information = TRUE;
    $moreinfo_sentence = "Please enter the details for the new client that you wish to associate with $realcontactname.";
    $client_id = 'ncli' . alphanum_counter();
    $metaco_id = 'meta' . alphanum_counter();
    $more_clients[] = array('user_id' => $realcontact['user_id'],
      'business_id' => $realcontact['business_id'], 'client_id' => $client_id);
    $hiddenfields .= "
    <input type=\"hidden\" name=\"metacontacts[$metaco_id][user_id]\" value=\"{$realcontact['user_id']}\" />
    <input type=\"hidden\" name=\"metacontacts[$metaco_id][business_id]\" value=\"{$realcontact['business_id']}\" />
    <input type=\"hidden\" name=\"metacontacts[$metaco_id][client_id]\" value=\"$client_id\" />
    <input type=\"hidden\" name=\"metacontacts[$metaco_id][realcontact_id]\" value=\"$realcontact_id\" />\n";

    if (substr($_POST['goto'], 0, 14) != 'commission.php')
      $_POST['goto'] = "contact.php?user_id=$user_id&realcontact_id=$realcontact_id";
  }
}

if (array_key_exists('btn_contact_del', $_POST)) {

  if (array_key_exists('metacontacts', $realcontact)) {
    $client_key = explode(' ', $realcontact['metacontacts'][0], 3);

    // Get the client name, for SQL error messages and for "yellow box" messages.
    $client_name = "client $numero {$client_key[2]}";
    $client_name_ybox = "Client No. {$client_key[2]}";
    do {
      $result = $db_connection->query("SELECT client_name FROM clients WHERE " .
        "user_id={$client_key[0]} AND business_id={$client_key[1]} AND client_id={$client_key[2]}");
      if ($result === FALSE) break;
      if (($row = $result->fetch_row()) === NULL) break;
      if ($row[0] == '') break;
      $client_name = "the client &lsquo;{$row[0]}&rsquo;";
      $client_name_ybox = "The client ‘"
        . $db_connection->real_escape_string(truncate($row[0])) . "’";
    } while (FALSE);
    $result->free();

    $result = $db_connection->query("DELETE FROM metacontacts WHERE user_id={$client_key[0]} AND " .
        "business_id={$client_key[1]} AND client_id={$client_key[2]} AND realcontact_id=$realcontact_id");
    if (!$result)
      trigger_error("I wasn't able to dissociate $client_name from $realcontactname.&nbsp; Here's the error message:<br />\n" . $db_connection->error, E_USER_ERROR);
    $db_connection->query("INSERT INTO messages (user_id,tie,msg) VALUES($user_id,"
          . yellowboxtie($user_id)
          . ",'$client_name_ybox is no longer associated with $realctname_ybox.')");
  }

  btn_rc_go_nowhere();
}

if (array_key_exists('btn_contact_gto', $_POST)) {
  if (array_key_exists('metacontacts', $realcontact)) {
    $client_key = explode(' ', $realcontact['metacontacts'][0], 3);
    $_POST['goto'] = "client.php?user_id={$client_key[0]}&business_id={$client_key[1]}&client_id={$client_key[2]}";
  }
  else btn_rc_go_nowhere();
}
