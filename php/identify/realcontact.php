<?php

if (empty($realcontact_id)) {
  if (empty($_GET['realcontact_id'])) {
    if ($realcontact_id == '0' or $_GET['realcontact_id'] == '0')
      trigger_error('0 is not a valid contact ID.', E_USER_ERROR);
    else trigger_error('No contact ID was specified.', E_USER_ERROR);
  }
  $realcontact_id = $_GET['realcontact_id'];
}

if (($intid = (int) $realcontact_id) <= 0) $new = TRUE;
else {$new = FALSE; $realcontact_id = $intid;}
unset($intid);

if ($new) {
  $realcontact['fullname'] = 'New Contact';
} else {
  $result = $db_connection->query("SELECT *,CONCAT_WS(' ',forenames,surname) AS fullname" .
    " FROM realcontacts WHERE user_id=$user_id AND realcontact_id=$realcontact_id")
    or trigger_error("I couldn't retrieve the details of contact $numero $realcontact_id for "
    . $user_name . ".&nbsp; The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);
  $realcontact = $result->fetch_assoc()
    or trigger_error("There is no contact $numero $realcontact_id for $user_name.", E_USER_ERROR);
  $result->free();
  foreach ($realcontact as $k => &$v) {
    if ($v === NULL) unset($realcontact[$k]);
    elseif ($k == 'notes') $v = htmlentities($v, ENT_COMPAT, 'UTF-8');
    else $v = htmlify($v);
  }
  unset($result, $v);    // It is necessary to unset $v.
}
