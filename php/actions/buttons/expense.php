<?php

function btn_expense_go_nowhere() {
  global $user_id, $business_id, $expense_id;
  if (array_key_exists('HTTP_REFERER', $_SERVER) and
      !empty($_SERVER['HTTP_REFERER'])
      and substr($_SERVER['HTTP_REFERER'], -10) != 'action.php') {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
  } else {
    $_POST['goto'] = "expense.php?user_id=$user_id&business_id=$business_id&expense_id=$expense_id";
  }
}

$temp = array_keys($_POST['expenses']);
$expense_id = $temp[0];
$expense = $_POST['expenses'][$expense_id];
unset($temp);

if (array_key_exists('user_id', $expense)) $user_id = $expense['user_id'];
if (array_key_exists('business_id', $expense)) $business_id = $expense['business_id'];

// replace $expense_id (if alias) with the real ID number from auto_increment
if (array_key_exists('expense_id', $expense))
  $expense_id = $expense['expense_id'];
// see action_clean_input() in /php/actions/common.php


if (array_key_exists('btn_expense_chg', $_POST)) {
  $_POST['goto'] = "exp_srch_job.php?user_id=$user_id&business_id=$business_id&expense_id=$expense_id";
}

if (array_key_exists('btn_expense_new', $_POST)) {
  $need_more_information = TRUE;
  $moreinfo_sentence = "Please enter the details for the new job that you wish to link $expense_name to.";
  $job_id = 'njob' . alphanum_counter();
  $more_jobs[] = array('user_id'     => $expense['user_id'],
                       'business_id' => $expense['business_id'],
                       'job_id'      => $job_id);
  $hiddenfields .= "
    <input type=\"hidden\" name=\"expenses[$expense_id][user_id]\" value=\"{$expense['user_id']}\" />
    <input type=\"hidden\" name=\"expenses[$expense_id][business_id]\" value=\"{$expense['business_id']}\" />
    <input type=\"hidden\" name=\"expenses[$expense_id][job_id]\" value=\"$job_id\" />\n";

  // see /php/forms/join.php
  $existing_related = array('key' => $expense_id,
                            'name' => $expense['expense_title']);

  $_POST['goto'] = "expense.php?user_id=$user_id&business_id=$business_id&expense_id=$expense_id";
}