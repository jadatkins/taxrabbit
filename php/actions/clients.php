<?php

foreach ($_POST['clients'] as $client_id => &$client) {
  action_clean_input(
    $client,
    array('user_id' => 'users', 'business_id' => 'businesses', 'parent_id' => 'clients'),
    array('abbrev', 'client_name', 'notes')
  );
  //if (array_key_exists('parent_id', $client) and is_string($client['parent_id'])
  //    and array_key_exists($client['parent_id'], $_POST['clients']) and
  //    array_key_exists('client_id', $_POST['clients'][$client['parent_id']]))
  //  $client['parent_id'] = $_POST['clients'][$client['parent_id']]['client_id'];

  action_isnew(
    $client, $client_id, 'client_id', 'client', 'clients',
    array('user_id' => $client['user_id'], 'business_id' => $client['business_id'])
  );

  $client_name = '';
  $client_name_ybox = '';
  action_name_or_title(
    $client, $client_id, $client_name, $client_name_ybox,
    array('user_id' => $client['user_id'], 'business_id' => $client['business_id'], 'client_id' => $client_id),
    'client', 'clients', 'client_name', 'name'
  );

  action_check_id($client, $client_name, 'user', 'users', TRUE);
  action_check_id($client, $client_name, 'business', 'businesses', TRUE);

  // Check whether the parent exists
  $nesting_error = ($client['parent_id'] == $client_id);
  if ($client['parent_id'] === '') {
    $parent_doesnt_exist = FALSE;
    $client['parent_id'] = NULL;
  } else {
    $parent_doesnt_exist = ! action_check_id(
      array('user_id' => $client['user_id'], 'business_id' => $client['business_id'], 'client_id' => $client['parent_id']),
      $client_name, 'client', 'clients', FALSE
    );
    if ($parent_doesnt_exist) {
      $parent_id = $client['parent_id'];
      unset($client['parent_id']);
    } else {
      // parent does exist. check for nested clients
      $row = array($client['parent_id']);
      while (! $nesting_error) {
        $result = $db_connection->query("SELECT parent_id FROM clients WHERE client_id={$row[0]}");
        if ($result === FALSE) break;
        if (($row = $result->fetch_row()) === NULL) break;
        if (empty($row[0])) break;
        $nesting_error = ($row[0] == $client_id);
        $result->free();
      }
      unset($result, $row);
    }
  }
  if ($nesting_error) friendly_error("A client can't belong to itself!", TRUE);

  // Check for abbrev in the same sort of way as we check for client_name
  if (array_key_exists('abbrev', $client) and $client['abbrev'] == '')
    friendly_error('You must provide an abbreviation for ' . $client_name
      . '.&nbsp; Please go back and try again.', TRUE);
  if ($client['new'] and ! array_key_exists('abbrev', $client))
      trigger_error("You've asked me to create a new client, but I didn't catch what the
     abbreviation was supposed to be.&nbsp; There must be something wrong with
     the page containing the client details form that you've just come from.", E_USER_ERROR);

  action_do_sql(
    $client, $client_id, 'client_id', 'clients',
    array('user_id', 'business_id', 'client_id'),
    array('parent_id', 'abbrev', 'client_name', 'notes'),
    $client_name, $client_name_ybox
  );

  // Deal with the situation where the parent doesn't exist
  if ($parent_doesnt_exist) {
    $client_id = $client['client_id'];
    $need_more_information = TRUE;
    $moreinfo_sentence = "Please enter the details for the new client that you wish to contain $client_name.";
    $more_clients[] = array('user_id' => $client['user_id'], 'business_id' => $client['business_id'], 'client_id' => $parent_id);
    $hiddenfields .= "    <input type=\"hidden\" name=\"clients[$client_id][user_id]\" value=\"{$client['user_id']}\" />
    <input type=\"hidden\" name=\"clients[$client_id][business_id]\" value=\"{$client['business_id']}\" />
    <input type=\"hidden\" name=\"clients[$client_id][parent_id]\" value=\"$parent_id\" />\n";
    $new_client_exclude[$parent_id] = $client_id;  // see /php/forms/client.php
  }
}
