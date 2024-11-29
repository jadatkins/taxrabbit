<?php

foreach ($_POST['realcontacts'] as $realcontact_id => &$realcontact) {
  action_clean_input(
    $realcontact,
    array('user_id' => 'users'),
    array('prefix', 'forenames', 'surname', 'phone', 'email', 'notes')
  );

  action_isnew(
    $realcontact, $realcontact_id, 'realcontact_id', 'realcontact', 'realcontacts',
    array('user_id' => $realcontact['user_id'])
  );

  $name_supplied = array_key_exists('forenames', $realcontact) or array_key_exists('surname', $realcontact);

  // If name was supplied, check that it's not empty.
  if ($name_supplied and
      !(
        array_key_exists('forenames', $realcontact) && $realcontact['forenames'] != ''
      or
        array_key_exists( 'surname',  $realcontact) && $realcontact[ 'surname' ] != ''
      )
    )
    friendly_error('You must provide a '
      . ($realcontact['new'] ? 'contact name' : "name for contact No. $realcontact_id")
      . '.&nbsp; Please go back and try again.', TRUE);

  if ($realcontact['new']) {
    // validation specifically for creating new realcontacts
    if (!$name_supplied)
      trigger_error("You've asked me to create a new contact, but I didn't catch what the
     name was supposed to be.&nbsp; There must be something wrong with the page
     containing the contact details form that you've just come from.", E_USER_ERROR);
  }

  // Get the realcontact name, for validation error messages and for "yellow box" messages.
  if ($name_supplied) {
    $namespacer = (empty($realcontact['forenames']) || empty($realcontact['surname'])) ? '' : ' ';
    $realcontactname = 'the contact &lsquo;' . $realcontact['forenames']
                    . $namespacer . $realcontact['surname'] . '&rsquo;';
    $realctname_ybox = "the contact ‘" . $db_connection->real_escape_string(
        truncate($realcontact['forenames'].$namespacer.$realcontact['surname'])
      ) . "’";
  } else {
    $realcontactname = "contact $numero $realcontact_id";
    $realcontactname_ybox = "contact No. $realcontact_id";
    do {
      $result = $db_connection->query("SELECT CONCAT_WS(' ',forenames,surname)"
              . ' FROM realcontacts WHERE user_id=' . $realcontact['user_id']);
      if ($result === FALSE) break;
      if (($row = $result->fetch_row()) === NULL) break;
      if ($row[0] == '') break;
      $row[0] = trim($row[0], " ");
      $realcontactname = "the contact &lsquo;{$row[0]}&rsquo;";
      $realctname_ybox = 'the contact ‘'
        . $db_connection->real_escape_string(truncate($row[0])) . "’";
    } while (FALSE);
    $result->free();
  }

  // validation for all realcontacts, new and old
  action_check_id($realcontact, $realcontactname, 'user', 'users', TRUE);
  action_fix_email($realcontact, 'email');

  action_do_sql(
    $realcontact, $realcontact_id, 'realcontact_id', 'realcontacts',
    array('user_id', 'realcontact_id'),
    array('forenames', 'surname', 'phone', 'email', 'notes'),
    $realcontactname, $realctname_ybox
  );
}
