<?php

// $refs is an associative array detailing relationships that
// the current table has with other tables. The keys of $refs
// are the names of the columns in the current table that refer
// to records in other tables, and the values of $refs are
// the names of those other tables. $fields is a list of text
// fields in the current table (including date, currency etc).
// $record is the current record, i.e. an entry in the array
// $_POST['<current table>'].
function action_clean_input(&$record, $refs, $fields) {
  foreach ($refs as $fk => $table) {
    if (!array_key_exists($fk, $record)) continue;
    if (preg_match('/^\d+$/', $record[$fk])) {
      settype($record[$fk], 'integer');
    } else {
      // Replace id aliases from new records in other tables with the actual ids.
      if ($fk == 'parent_id') {$hk = 'client_id';} else {$hk = $fk;}
      if (array_key_exists($table, $_POST) and
          array_key_exists($record[$fk], $_POST[$table]) and
          array_key_exists($hk, $_POST[$table][$record[$fk]]))
        $record[$fk] = $_POST[$table][$record[$fk]][$hk];
  /* e.g. if (array_key_exists(-3, $POST['users']) and
              array_key_exists('user_id', $POST['users'][-3]))
        $record['user_id'] = $POST['users'][-3]['user_id'];  */
    }
  }
  foreach ($fields as $fk) {
    if (array_key_exists($fk, $record)) treat_input_string($record[$fk]);
  }
}

// Check whether the record already exists, or if it needs to
// be created. $criteria includes all but the last part of the
// primary key (which would be $id_name => $id) $singular is a
// string indicating what a record is called (e.g. 'expense'
// for an expense).
function action_isnew(&$record, $id, $id_name, $singular, $table, $criteria) {
  global $db_connection;
  $criteria[$id_name] = $id;
  if (array_key_exists($id_name, $record)) unset($record[$id_name]);
  foreach ($criteria as $v) {
    if (! is_numeric($v) or $v < 0) return ($record['new'] = TRUE);
  }
  $record[$id_name] = $id;
  $result = $db_connection->query('SELECT EXISTS (SELECT * FROM ' . $table . sql_where($criteria) . ')');
  if ($result === FALSE) trigger_error(
    "I was just trying to check whether $singular $numero $id exists already, or
     whether I'd have to create it, and something went wrong.&nbsp; Here's the
     error message:<br />\n" . $db_connection->error, E_USER_ERROR);
  $row = $result->fetch_row();
  $result->free();
  return ($record['new'] = !$row[0]);
}

// If $userfields is the field names of ALL fields which the user can enter
// (typically, everything except the primary key) then this is TRUE only if the
// user has left the form completely blank.
function is_completely_blank($record, $userfields) {
  if (! $record['new']) return FALSE;
  foreach ($userfields as $field) {
    if (! empty($record[$field])) return FALSE;
  }
  return TRUE;
}

// Returns a string. $criteria is an array such as array(
// 'user_id' => 5, 'business_id' => 2, 'client_id' = 41).
// One way to call this is thus: $where = sql_where(
// array_intersect_key( $record, array_flip($primarykey) ) );
function sql_where($criteria) {
  if (empty($criteria)) return '';
  $a = ' WHERE ';
  foreach ($criteria as $key => $value) {
    $a .= $key . '=' . wrap_for_sql($value) . ' AND ';
  }
  return substr($a, 0, -5);
}

// e.g. action_name_or_title(
//   $job, $job_id, $jobtitle, $jobtitle_ybox,
//   array('user_id' => 2, 'business_id' => 1, 'job_id' => 34),
//   'job', 'jobs', 'job_title', 'title'
// )
function action_name_or_title(
    &$record, $record_id, &$recordname, &$fname_ybox,
    $primarykey,
    $singular, $table, $fieldname, $n_or_t
  ) {
  global $db_connection, $numero;
  $human = "$singular $n_or_t";

  // If record_name was supplied, check that it's not empty.
  if (array_key_exists($fieldname, $record) and $record[$fieldname] == '')
    friendly_error('You must provide a '
      . ($record['new'] ? $human : "$n_or_t for $singular $numero $record_id")
      . '.&nbsp; Please go back and try again.', TRUE);

  if ($record['new']) {
    // validation specifically for creating new records
    if (! array_key_exists($fieldname, $record))
      trigger_error("You've asked me to create a new $singular, but I didn't catch what the
     $n_or_t was supposed to be.&nbsp; There must be something wrong with the page
     containing the $singular details form that you've just come from.", E_USER_ERROR);
  }

  // Get the record name, for validation error messages and for "yellow box" messages.
  if (array_key_exists($fieldname, $record)) {
    $recordname = "the $singular &lsquo;" . htmlify(truncate($record[$fieldname])) . '&rsquo;';
    $fname_ybox = "the $singular ‘"
      . $db_connection->real_escape_string(truncate($record[$fieldname])) . "’";
  } else {
    $recordname = "$singular $numero $record_id";
    $recordname_ybox = "$singular No. $record_id";
    do {
      $result = $db_connection->query("SELECT $fieldname FROM $table" . sql_where($primarykey));
      if ($result === FALSE) break;
      if (($row = $result->fetch_row()) === NULL) break;
      if ($row[0] == '') break;
      $recordname = "the $singular &lsquo;" . htmlify(truncate($row[0])) . '&rsquo;';
      $fname_ybox = "the $singular ‘"
        . $db_connection->real_escape_string(truncate($row[0])) . "’";
      $result->free();
    } while (FALSE);
  }
}

// e.g. action_check_id($job, "the job 'article on teenage pregnancy'", 'business', 'businesses', TRUE);
//      action_check_id($job, "the job 'article on teenage pregnancy'", 'realcontact', 'realcontacts', FALSE);
// Set $required = TRUE iff the thing_id is part of $record's primary key.
// Throws an error if any required fields were missing. Otherwise, returns a
// boolean indicating whether the thing referred to exists in the database.
function action_check_id($record, $recordname, $thing, $things, $required) {
  global $db_connection, $numero;
  $thing_id = $thing . '_id';

  if ($required and empty($record[$thing_id]))
    trigger_error("I can't record the details for $recordname because no $thing_id was given.", E_USER_ERROR);

  // If the $thing did exist, $record[$thing_id] would have been changed to the
  // $thing's real $thing_id already (in the function action_clean_input).
  if (!array_key_exists($thing_id, $record)) return TRUE;
  if (!is_int($record[$thing_id]) or $record[$thing_id] < 0) return FALSE;

  // This is a bit of a hack
  $where_clause = 'WHERE ';
  if ($thing != 'user') {
    $where_clause .= "user_id={$record['user_id']} AND ";
    if ($thing != 'business' and $thing != 'realcontact') 
      $where_clause .= "business_id={$record['business_id']} AND ";
  }
  $where_clause .= "$thing_id={$record[$thing_id]}";

  $result = $db_connection->query("SELECT EXISTS (SELECT * FROM $things $where_clause)");
  if ($result === FALSE)
    trigger_error("I was just trying to check whether $thing $numero $record[$thing_id] exists,
     and something went wrong.&nbsp; Here's the error message:<br />\n" . $db_connection->error, E_USER_ERROR);
  $row = $result->fetch_row();
  $result->free();
  return $row[0];
}

function action_fix_date(&$record, $field, $name = 'date', $required = FALSE) {
  do {
    if (!array_key_exists($field, $record)) break;
    if ($record[$field] == '') {$record[$field] = NULL; break;}
    if (!empty($record['user_id'])) $user_id = $record['user_id'];
    else $user_id = $GLOBALS['user_id'];
    require_once './php/intestines/dates.php';
    digest_date($record[$field], $user_id, $name);
    return;
  } while (FALSE);
  
  if ($required)
    friendly_error("You must fill in the $name.", TRUE);
}

function action_fix_decimal(&$record, $field, $name, $ante = 18, $post = 2) {
  if (!array_key_exists($field, $record)) return;
  if ($record[$field] == '') {$record[$field] = NULL; return;}
  require_once './php/intestines/decimals.php';
  digest_decimal($record[$field], $name);
}

function action_fix_email(&$record, $field) {
  if (!array_key_exists($field, $record)) return;
  if ($record[$field] == '') {$record[$field] = NULL; return;}
  require_once './php/intestines/email.php';
  digest_email($record[$field]);
}


define('GUID_EXPIRY', 900);  // maximum new_record guid age in seconds

// $id is the record_id specified in $_POST (by being the key
// whose value is the array $record). $id_name is a string such
// as 'business_id'. $primarykey and $otherfields are both non-
// associative (i.e., with numeric indeces) arrays.
function action_do_sql(&$record, $id, $id_name, $table, $primarykey, $otherfields, $recordname, $fname_ybox) {
  global $db_connection;
  $new = $record['new'];

  if (!($table == 'users' and $new) and AUTHUID != $record['user_id'])
    {header('HTTP/1.1 403 Forbidden'); die();}

  // Prepare the lists of column names and values for the SQL statement.
  foreach (($new ? array_merge($primarykey, $otherfields) : $otherfields) as $field) {
    if (!array_key_exists($field, $record)) {continue;}
    $cols[] = $field;
    $values[] = wrap_for_sql($record[$field]);
  }

  if (isset($record['guid'])) {
    if (preg_match('/^\d+$/', $record['guid']))
      settype($record['guid'], 'integer');
    else
      unset($record['guid']);
  }

  // check whether the record is *really* new, or a duplicate
  if ($new and isset($record['guid'])) do {
    if ($table == 'users') {
      $where_clause = "type='users'";
    } else {
      $where_clause = "user_id={$record['user_id']} AND type='$table'";
    }
    $result = $db_connection->query("SELECT record_id FROM new_records WHERE "
          . "$where_clause AND guid={$record['guid']}");
    if (! $result) break;
    if ($row = $result->fetch_row()) {
      // it's a duplicate
      $record[$id_name] = intval($row[0]);
      $new = false;
      $_POST['goto'] = str_replace($id_name.'='.$id, $id_name.'='.$record[$id_name], $_POST['goto']);
      $db_connection->query("UPDATE new_records SET ts=CURRENT_TIMESTAMP WHERE "
          . "$where_clause AND guid={$record['guid']}");
      if ($table == 'users')
        trigger_error('You just tried to create the same user twice.&nbsp; You can\'t do that.</p>
  <p>Why don\'t you:</p>
  <ul>
    <li><a href="./user.php?user_id=' . intval($row[0]) . '">Log in</a> to the account that you just created</li>
    <li>Or <a href="./profile.php?user_id=dupe">create another user</a></li>
    <li>Or contact us for help (details should be below)</li>
  </ul>
  <p>&nbsp;', E_USER_ERROR);
    } else {
      // it really is new, so tidy up old guids
      if ($result = $db_connection->query('SELECT EXISTS(SELECT * FROM '
          . 'new_records WHERE TIMESTAMPDIFF(SECOND,ts,NOW()) < '.GUID_EXPIRY.')')
          and $row = $result->fetch_row() and ! $row[0]) {
        $db_connection->query('TRUNCATE TABLE new_records');
      } else {
        $db_connection->query('DELETE FROM new_records WHERE TIMESTAMPDIFF(SECOND,ts,NOW()) > ' . GUID_EXPIRY);
      }
    }
    unset($result, $row);
  } while (false);

  // Finally, put the data into the database.
  if ($new) {
    if (!$db_connection->query("INSERT INTO $table (" . implode(',', $cols)
                              . ') VALUES(' . implode(',', $values) . ')')) {
      if ($db_connection->errno == 1062) {
        if ($table == 'users')
          friendly_error('There is already a user with the username / email address
     &lsquo;' . $record['email'] . '&rsquo;.<br />Please email
  <script type="text/javascript" language="javascript">
    <!--
    insert_detail(true);
    // -->
  </script>
  <noscript>
    <img src="images/contact.png" alt="me" style="position: relative; top: 1px;" />
  </noscript>
or call <a href="tel:+441179112858">0117 911 2858</a> if you can\'t remember your password.', TRUE);
        if ($table == 'clients')
          friendly_error("You can't use &lsquo;{$record['abbrev']}&rsquo; as the abbreviation, "
              . "because there is already another client with that abbreviation.", TRUE);
      } else {
        trigger_error("I wasn't able to create $recordname.&nbsp; Here's the "
            . "error message:<br />\n" . $db_connection->error, E_USER_ERROR);
      }
    }
    $record[$id_name] = $db_connection->insert_id;
    $db_connection->query("INSERT INTO messages (user_id,tie,msg) "
          . "VALUES({$record['user_id']}," . yellowboxtie($record['user_id']) . ",'I have created $fname_ybox.')");
    // insert new guid into new_records
    if (isset($record['guid']))
      $db_connection->query("INSERT INTO new_records (user_id,type,guid,record_id) "
          . "VALUES({$record['user_id']},'$table',{$record['guid']},{$record[$id_name]})");
    // And fix the $_POST[goto] URL.
    $_POST['goto'] = str_replace($id_name.'='.$id, $id_name.'='.$record[$id_name], $_POST['goto']);
  } else {
    if (empty($cols)) return;
    $settings = '';
    for ($i = 0; $i < count($cols); $i++)
      $settings .= $cols[$i] . '=' . $values[$i] . ', ';
    $settings = substr($settings, 0, -2);
    if (!$db_connection->query("UPDATE $table SET " . $settings
          . sql_where(array_intersect_key($record, array_flip($primarykey))))) {
      if ($db_connection->errno == 1062) {
        if ($table == 'users')
          friendly_error('There is already a user with the username / email address
     &lsquo;' . $record['email'] . '&rsquo;.<br />Please email
  <script type="text/javascript" language="javascript">
    <!--
    insert_detail(true);
    // -->
  </script>
  <noscript>
    <img src="images/contact.png" alt="me" style="position: relative; top: 1px;" />
  </noscript>
or call 0117 911 2858 if you can\'t remember your password.', TRUE);
        if ($table == 'clients')
          friendly_error("You can't use &lsquo;{$record['abbrev']}&rsquo; as the abbreviation, "
              . "because there is already another client with that abbreviation.", TRUE);
      } else {
        trigger_error("I wasn't able to update $recordname.&nbsp; Here's the "
            . "error message:<br />\n" . $db_connection->error, E_USER_ERROR);
      }
    }
    $db_connection->query("INSERT INTO messages (user_id,tie,msg) "
          . "VALUES({$record['user_id']}," . yellowboxtie($record['user_id']) . ",'I have updated $fname_ybox.')");
  }
}
