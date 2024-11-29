<?php

foreach ($_POST['metacontacts'] as $metact_id => &$metact) {
  action_clean_input(
    $metact,
    array('user_id' => 'users', 'business_id' => 'businesses',
      'client_id' => 'clients', 'realcontact_id' => 'realcontacts'),
    array()
  );

  // Get the client name, for validation error messages and for "yellow box" messages.
  $metact['new'] = FALSE;
  $client_name = '';
  $client_name_ybox = '';
  action_name_or_title(
    $metact, $metact_id, $client_name, $client_name_ybox,
    array('user_id' => $metact['user_id'], 'business_id' => $metact['business_id'], 'client_id' => $metact['client_id']),
    'client', 'clients', 'client_name', 'name'
  );
  unset($metact['new']);

  // Get the realcontact name, for validation error messages and for "yellow box" messages.
  $realcontactname = "contact $numero {$metact['realcontact_id']}";
  $realcontactname_ybox = "contact No. {$metact['realcontact_id']}";
  do {
    $result = $db_connection->query("SELECT CONCAT_WS(' ',forenames,surname) FROM realcontacts"
                . " WHERE user_id={$metact['user_id']} AND realcontact_id={$metact['realcontact_id']}");
    if ($result === FALSE) break;
    if (($row = $result->fetch_row()) === NULL) break;
    if ($row[0] == '') break;
    $realcontactname = "the contact &lsquo;{$row[0]}&rsquo;";
    $realctname_ybox = 'the contact ‘'
      . $db_connection->real_escape_string(truncate($row[0])) . "’";
    $result->free();
  } while (FALSE);
  unset($result);

  // check that all related records exist
  foreach (array('user' => 'users', 'business' => 'businesses', 'client' => 'clients',
      'realcontact' => 'realcontacts') as $singular => $plural) {
    if (! action_check_id($metact, 'this association', $singular, $plural, TRUE))
      trigger_error("I can't associate $realcontactname with $client_name, " .
        "because $singular $numero {$metact[$singular.'_id']} doesn't exist.", E_USER_ERROR);
  }

  // Finally, put the data into the database.
  $success = $db_connection->query('INSERT INTO metacontacts (user_id,business_id,client_id,realcontact_id) ' .
      "VALUES({$metact['user_id']},{$metact['business_id']},{$metact['client_id']},{$metact['realcontact_id']})");
  if ($db_connection->errno != 1062) {
    if (! $success)
      trigger_error("I wasn't able to associate $realcontactname with $client_name.&nbsp; "
              . "Here's the error message:<br />\n" . $db_connection->error, E_USER_ERROR);
    $db_connection->query("INSERT INTO messages (user_id,tie,msg) VALUES({$metact['user_id']},"
          . yellowboxtie($metact['user_id']) . ",'I have associated $realctname_ybox with $client_name_ybox.')");
  }
  unset($success);
}
