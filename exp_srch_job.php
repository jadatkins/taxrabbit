<?php

require_once './php/essentials.php';
include_once './php/nocache.php';
require_once './php/identify/user.php';
$headings = array($user['user_name']);
$navbox = TRUE;
$urls = array("user.php?user_id=$user_id");
require_once './php/identify/business.php';
array_push($headings, $business['business_name'], 'Expenses');
array_push($urls, "business.php$unique_id", "search_out.php$unique_id");
require_once './php/forms/join.php';
$saveall = TRUE;
require './php/identify/expense.php';
$headings[] = html_entity_decode($expense['expense_title'], ENT_COMPAT, 'UTF-8');
$urls[] = "expense.php$unique_id&amp;expense_id=$expense_id";
$headings[] = "Related income";

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
  <p>Enter some criteria to find the job that the expense &lsquo;<?php echo $expense['expense_title'];
?>&rsquo; should be associated with.</p>

  <form accept-charset="UTF-8" name="frm_main" method="get" action="exp_find_job.php" onsubmit="disable()">

<?php insert_hidden_fields("expense.php$unique_id&amp;expense_id=$expense_id"); ?>
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
    <input type="hidden" name="business_id" value="<?php echo $business_id; ?>" />
    <input type="hidden" name="expense_id" value="<?php echo $expense_id; ?>" />

<?php require './php/forms/search_in.php'; ?>
  </form>
<?php include './php/footer.php';
