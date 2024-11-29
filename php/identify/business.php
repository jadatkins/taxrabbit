<?php

if (empty($_REQUEST['business_id']) and $_REQUEST['business_id'] != '0')
  trigger_error('No business ID was specified.', E_USER_ERROR);
if (($business_id = ((int) $_REQUEST['business_id'])) <= 0)
  trigger_error($_REQUEST['business_id'] . ' is not a valid business ID.', E_USER_ERROR);

$result = $db_connection->query("SELECT business_name,bookdate,final FROM businesses"
                      . " WHERE user_id=$user_id AND business_id=$business_id")
  or trigger_error("I couldn't retrieve the name of business number " . $business_id .
  " for $user_name.&nbsp; The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);
$business = $result->fetch_assoc()
  or trigger_error("There is no business number $business_id for $user_name.", E_USER_ERROR);
$result->free();
$business_name = htmlify(truncate($business['business_name']));
define('BOOKDATE', $business['bookdate']);
define('FINALDATE', empty($business['final']) ? '1999-12-31' : $business['final']);
unset($result);

$unique_id .= "&amp;business_id=$business_id";
