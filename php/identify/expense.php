<?php

if (empty($expense_id)) {
  if (empty($_GET['expense_id'])) {
    if ($expense_id == '0' or $_GET['expense_id'] == '0')
      trigger_error('0 is not a valid expense ID.', E_USER_ERROR);
    else trigger_error('No expense ID was specified.', E_USER_ERROR);
  }
  $expense_id = $_GET['expense_id'];
}

if (($intid = (int) $expense_id) <= 0) $new = TRUE;
else {$new = FALSE; $expense_id = $intid;}
unset($intid);

if ($new) {
  $expense['expense_title'] = 'New Expense';
  $expense['job_id'] = NULL;
} else {
  $result = $db_connection->query("SELECT *,"
    . "DAYNAME(date) AS weekday FROM expenses WHERE user_id="
    . $user_id . " AND business_id=$business_id AND expense_id=$expense_id")
    or trigger_error("I couldn't retrieve the details of expense $numero $expense_id for " .
      "$business_name.&nbsp; The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);
  $expense = $result->fetch_assoc() or trigger_error(
    "There is no expense $numero $expense_id for $business_name.", E_USER_ERROR);
  $result->free();

  foreach ($expense as $k => &$v) {
    // Leave $expense['job_id'] intact if it is NULL
    if ($k == 'job_id') continue;
    if ($v === NULL) unset($expense[$k]);
    elseif ($k == 'notes') $v = htmlentities($v, ENT_COMPAT, 'UTF-8');
    else $v = htmlify($v);
  }
  unset($result, $v);    // It is necessary to unset $v.
}
