<?php

require_once './php/essentials.php';
include_once './php/nocache.php';
require_once './php/identify/user.php';
$headings = array($user['user_name']);
$navbox = TRUE;
$urls = Array("user.php?user_id=$user_id");
require_once './php/identify/business.php';
array_push($headings, $business['business_name'], 'Income');
$urls[] = "business.php$unique_id";
if (array_key_exists('HTTP_REFERER', $_SERVER) and
    !empty($_SERVER['HTTP_REFERER'])
    and substr(basename($_SERVER['HTTP_REFERER']), 0, 11) == 'find_in.php') {
  $urls[] = $_SERVER['HTTP_REFERER'];
}
else $urls[] = "search_in.php$unique_id";

require './php/identify/job.php';

$headings[] = html_entity_decode($job['job_title'], ENT_COMPAT, 'UTF-8');

require_once './php/header_one.php';
require_once './php/header_two.php';

?>
  <form accept-charset="UTF-8" name="frm_main" method="post" action="action.php" onsubmit="disable()">

<?php

$open_xhtml_tags[] = 'form';
$allowed = array('diary.php', 'find_in.php', 'find_out.php');
if (!empty($_GET['return'])) $allowed[] = 'expense.php';
insert_hidden_fields(goto_address("business.php$unique_id", $allowed));
require './php/forms/job.php';

close_xhtml_tag('form');

include './php/footer.php';
