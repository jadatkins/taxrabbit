<?php

require_once './php/essentials.php';
include_once './php/nocache.php';
require_once './php/identify/user.php';
$headings = array($user['user_name']);
$navbox = TRUE;
$urls = array("user.php?user_id=$user_id");
require_once './php/identify/business.php';
array_push($headings, $business['business_name'], 'Income');
array_push($urls, "business.php$unique_id", "search_in.php$unique_id");
require_once './php/forms/join.php';
$saveall = TRUE;
require './php/identify/job.php';
$headings[] = html_entity_decode($job['job_title'], ENT_COMPAT, 'UTF-8');
$urls[] = "commission.php$unique_id&amp;job_id=$job_id";
$headings[] = "Related expenses";

require_once './php/header_one.php';
?>
  <style type="text/css">
  /* <![CDATA[ */
    ul { list-style-type: none; }
  /* ]]> */
  </style>
<?php
require_once './php/header_two.php';

?>
  <p>Enter some criteria to find the expenses that the job &lsquo;<?php echo $job['job_title'];
?>&rsquo; should be associated with.</p>

  <form accept-charset="UTF-8" name="frm_main" method="get" action="job_find_exp.php" onsubmit="disable()">

<?php insert_hidden_fields("commission.php$unique_id&amp;job_id=$job_id"); ?>
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
    <input type="hidden" name="business_id" value="<?php echo $business_id; ?>" />
    <input type="hidden" name="job_id" value="<?php echo $job_id; ?>" />

<?php require './php/forms/search_out.php'; ?>
  </form>
<?php include './php/footer.php';
