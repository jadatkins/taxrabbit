<?php

require_once './php/essentials.php';
include_once './php/nocache.php';
require_once './php/identify/user.php';
$headings = array($user['user_name']);
$navbox = TRUE;
$urls = Array("user.php?user_id=$user_id");

if (empty($_GET['business_id'])) {
  if ($_GET['business_id'] == '0') trigger_error('0 is not a valid business ID.', E_USER_ERROR);
  else trigger_error('No business ID was specified.', E_USER_ERROR);
}
$business_id = $_GET['business_id'];
if (($intid = (int) $business_id) <= 0) $new = TRUE;
else {$new = FALSE; $business_id = $intid;}
unset($intid);

$unique_id .= "&amp;business_id=$business_id";

if ($new) {
  $headings[] = 'New Business';
} else {
  $result = $db_connection->query("SELECT *,DAYNAME(birth)"
      . " AS birth_weekday, DAYNAME(death) AS death_weekday,"
      . " DAYOFMONTH(bookdate) AS book_day, MONTH(bookdate) AS book_month"
      . " FROM businesses WHERE user_id=$user_id AND business_id=$business_id")
    or trigger_error("I couldn't retrieve the details of business $numero $business_id"
      ." for $user_name.&nbsp; The error message is:<br />\n" . $db_connection->error, E_USER_ERROR);
  $business = $result->fetch_assoc() or trigger_error(
    "I'm sorry, there is no business $numero $business_id for $user_name.", E_USER_ERROR);
  $result->free();

  $business_name = htmlify(truncate($business['business_name']));
  foreach ($business as $k => &$v) {
    if ($v === NULL) unset($business[$k]);
    elseif ($k == 'descr'  ) $v = htmlentities($v, ENT_COMPAT, 'UTF-8');
    elseif ($k == 'address') $v = htmlentities($v, ENT_COMPAT, 'UTF-8');
    else $v = htmlify($v);
  }
  unset($v);    // This line is necessary

  array_push($headings, $business['business_name'], 'Business Details');
  $urls[] = "business.php$unique_id";
}

require_once './php/header_one.php';
require_once './php/header_two.php';

?>
  <form accept-charset="UTF-8" name="frm_main" method="post" action="action.php" onsubmit="disable()">

<?php

$open_xhtml_tags[] = 'form';
insert_hidden_fields("business.php$unique_id");
require './php/forms/business.php';

include './php/footer.php';
