<?php

if (empty($client_id)) {
  if (empty($_GET['client_id'])) {
    if ($client_id == '0' or $_GET['client_id'] == '0')
      trigger_error('0 is not a valid client ID.', E_USER_ERROR);
    else trigger_error('No client ID was specified.', E_USER_ERROR);
  }
  $client_id = $_GET['client_id'];
}

if (($intid = (int) $client_id) <= 0) $new = TRUE;
else {$new = FALSE; $client_id = $intid;}
unset($intid);

if ($new) {
  $client['client_name'] = 'New Client';
  $client['parent_id'] = NULL;
} else {
  $result = $db_connection->query('SELECT * FROM clients WHERE user_id=' .
    $user_id . " AND business_id=$business_id AND client_id=$client_id")
    or trigger_error("I couldn't retrieve the details of client $numero $client_id for " .
      "$business_name.&nbsp; The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);
  $client = $result->fetch_assoc() or trigger_error(
    "There is no client $numero $client_id for $business_name.", E_USER_ERROR);
  $result->free();

  // We need to leave $client['parent_id'] intact if it is NULL, otherwise, in
  // php/forms/client.php, calling draw_client_dropdown() generates a Notice.
  foreach ($client as $k => &$v) {
    if ($k == 'parent_id') continue;
    if ($v === NULL) unset($client[$k]);
    elseif ($k == 'notes') $v = htmlentities($v, ENT_COMPAT, 'UTF-8');
    else $v = htmlify($v);
  }
  unset($result, $v);    // It is necessary to unset $v.
}
