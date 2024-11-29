<?php

function btn_job_go_nowhere() {
  global $user_id, $business_id, $job_id;
  if (array_key_exists('HTTP_REFERER', $_SERVER) and
      !empty($_SERVER['HTTP_REFERER'])
      and substr($_SERVER['HTTP_REFERER'], -10) != 'action.php') {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
  } else {
    $_POST['goto'] = "commission.php?user_id=$user_id&business_id=$business_id&job_id=$job_id";
  }
}

$temp = array_keys($_POST['jobs']);
$job_id = $temp[0];
$job = $_POST['jobs'][$job_id];
unset($temp);

if (array_key_exists('user_id', $job)) $user_id = $job['user_id'];
if (array_key_exists('business_id', $job)) $business_id = $job['business_id'];

// replace $job_id (if alias) with the real ID number from auto_increment
if (array_key_exists('job_id', $job))
  $job_id = $job['job_id'];
// see action_clean_input() in /php/actions/common.php


if (array_key_exists('btn_job_cli_gto', $_POST)) {
  if (array_key_exists('client_id', $job))
    $_POST['goto'] = "client.php?user_id={$job['user_id']}&business_id={$job['business_id']}&client_id={$job['client_id']}";
  elseif (empty($more_clients))
    btn_job_go_nowhere();
}

if (array_key_exists('btn_job_con_chg', $_POST)) {
  $_POST['goto'] = "job_contact.php?user_id=$user_id&business_id=$business_id&job_id=$job_id";
}

function addmetacontact($client_id, $realcontact_id) {
  global $hiddenfields, $job;
  $metaco_id = 'meta' . alphanum_counter();
  $hiddenfields .= "
    <input type=\"hidden\" name=\"metacontacts[$metaco_id][user_id]\" value=\"{$job['user_id']}\" />
    <input type=\"hidden\" name=\"metacontacts[$metaco_id][business_id]\" value=\"{$job['business_id']}\" />
    <input type=\"hidden\" name=\"metacontacts[$metaco_id][client_id]\" value=\"$client_id\" />
    <input type=\"hidden\" name=\"metacontacts[$metaco_id][realcontact_id]\" value=\"$realcontact_id\" />\n";
}

if (array_key_exists('btn_job_con_new', $_POST)) {
  $need_more_information = TRUE;
  if (empty($moreinfo_sentence))
    $moreinfo_sentence = 'Please';
  else
    $moreinfo_sentence .= "</p>\n<p>Also";
  $moreinfo_sentence .= " enter the new contact details for $job_name.";
  if (empty($moreinfo_sentence))
    $moreinfo_sentence .= '&nbsp; This will replace any contact(s) already associated with said job.';

  $realcontact_id = 'nrco' . alphanum_counter();
  $more_realcontacts[] = array('user_id' => $job['user_id'],
                        'realcontact_id' => $realcontact_id);
  $hiddenfields .= "
    <input type=\"hidden\" name=\"jobs[$job_id][user_id]\" value=\"{$job['user_id']}\" />
    <input type=\"hidden\" name=\"jobs[$job_id][business_id]\" value=\"{$job['business_id']}\" />
    <input type=\"hidden\" name=\"jobs[$job_id][realcontact_id]\" value=\"$realcontact_id\" />\n";

  if (empty($more_clients) and ! empty($job['client_id'])) {
    $client_name = "the job's client";
    do {
      $result = $db_connection->query("SELECT client_name FROM clients WHERE user"
          . "_id=$user_id AND business_id=$business_id AND client_id={$job['client_id']}");
      if (empty($result)) break;
      $row = $result->fetch_row();
      if (empty($row)) break;
      $client_name = $row[0];
    } while (false);
    // see /php/forms/join.php
    $existing_related = array('key' => "$user_id $business_id {$job['client_id']}",
                              'name' => $client_name);
    addmetacontact($job['client_id'], $realcontact_id);
  }
  if (count($more_clients) == 1) {
    foreach ($more_clients as $newclient) {
      if ($newclient['user_id'] == $job['user_id'] and $newclient['business_id'] == $job['business_id']) {
        $moreinfo_sentence .= "</p>\n<p>The new client and contact will be linked to each other, as well as to the job.";
        $client_id = $newclient['client_id'];
        addmetacontact($client_id, $realcontact_id);
        break;
      }
    }
  }

  $_POST['goto'] = "commission.php?user_id=$user_id&business_id=$business_id&job_id=$job_id";
}

if (array_key_exists('btn_job_exp_gto', $_POST)) {
  if (array_key_exists('expenses', $job))
    $_POST['goto'] = "expense.php?user_id=$user_id&business_id=$business_id&expense_id={$job['expenses'][0]}&return=1";
  else
    btn_job_go_nowhere();
}

if (array_key_exists('btn_job_exp_chg', $_POST)) {
  $_POST['goto'] = "job_srch_exp.php?user_id=$user_id&business_id=$business_id&job_id=$job_id";
}

if (array_key_exists('btn_job_exp_new', $_POST)) {
  $need_more_information = TRUE;
  if (empty($moreinfo_sentence)) $moreinfo_sentence = 'Please';
  else $moreinfo_sentence .= "</p>\n<p>Also";
  $moreinfo_sentence .= " enter the details for the new expense that you wish to link to $job_name.";
  $expense_id = 'nexp' . alphanum_counter();
  $more_expenses[] = array('user_id' => $job['user_id'],
                       'business_id' => $job['business_id'],
                        'expense_id' => $expense_id,
                            'job_id' => $job_id );
  $hiddenfields .= "
    <input type=\"hidden\" name=\"expenses[$expense_id][job_id]\" value=\"$job_id\" />\n";
  if (! empty($job['date'])) $related_expense_default_date = $job['date'];

  $_POST['goto'] = "commission.php?user_id=$user_id&business_id=$business_id&job_id=$job_id";
}
