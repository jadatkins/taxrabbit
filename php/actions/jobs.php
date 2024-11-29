<?php

foreach ($_POST['jobs'] as $job_id => &$job) {
  if (! empty($job['readonly']))
    continue;

  action_clean_input(
    $job,
    array('user_id' => 'users', 'business_id' => 'businesses',
      'client_id' => 'clients', 'realcontact_id' => 'realcontacts'),
    array('job_title', 'date', 'date_paid', 'fee', 'notes')
  );

  action_isnew(
    $job, $job_id, 'job_id', 'job', 'jobs',
    /* array_intersect_key($job, array('user_id' => 0, 'business_id' => 1)) */
    array('user_id' => $job['user_id'], 'business_id' => $job['business_id'])
  );

  if (is_completely_blank($job, array('client_id', 'job_title', 'date', 'date_paid', 'fee', 'notes')))
    continue;

  $job_name = '';
  $job_name_ybox = '';
  action_name_or_title(
    $job, $job_id, $job_name, $job_name_ybox,
    array('user_id' => $job['user_id'], 'business_id' => $job['business_id'], 'job_id' => $job_id),
    'job', 'jobs', 'job_title', 'title'
  );

  // validation for all jobs, new and old
  action_check_id($job, $job_name, 'user', 'users', TRUE);
  action_check_id($job, $job_name, 'business', 'businesses', TRUE);
  $realcontact_doesnt_exist = ! action_check_id($job, $job_name, 'realcontact', 'realcontacts', FALSE);
  if (! array_key_exists('client_id', $job)) {
    $client_doesnt_exist = FALSE;
  } elseif ($job['client_id'] === '') {
    $client_doesnt_exist = FALSE;
    $job['client_id'] = NULL;
  } else {
    $client_doesnt_exist = ! action_check_id($job, $job_name, 'client', 'clients', FALSE);
    if ($client_doesnt_exist) {
      $client_id = $job['client_id'];
      unset($job['client_id']);
    }
  }

  action_fix_date($job, 'date', 'date', $job['new']);
  action_fix_date($job, 'date_paid', 'date of payment');
  if ((! empty($job['date'])) or (! empty($job['date_paid']))) {
    $_REQUEST['business_id'] = $job['business_id'];
    require_once './php/identify/business.php';
  }
  if (! empty($job['date'])) {
    if (strcmp($job['date'], FINALDATE) <= 0)
      trigger_error("You can't create jobs with dates before " .
          date('j F Y', mktime(9, 0, 0, substr(FINALDATE,5,2), substr(FINALDATE,8,2)+1, substr(FINALDATE,0,4)))
          . '.', E_USER_ERROR);
    if (strcmp($job['date'], (idate('Y')+5).substr(BOOKDATE, 4)) > 0)
      trigger_error("You can't create jobs with dates after " .
          date('j F Y', mktime(9, 0, 0, substr(BOOKDATE,5,2), substr(BOOKDATE,8,2), idate('Y')+5))
          . '.', E_USER_ERROR);
  }
  if (! empty($job['date_paid'])) {
    if (strcmp($job['date_paid'], substr(FINALDATE,0,4).'-01-01') < 0)
      trigger_error("You can't create jobs with payment dates before ".substr(FINALDATE,0,4).'.', E_USER_ERROR);
    if (strcmp($job['date_paid'], (idate('Y')+5).'-12-31') > 0)
      trigger_error("You can't create jobs with payment dates after " . idate('Y')+5 . '.', E_USER_ERROR);
  }

  action_fix_decimal($job, 'fee', 'fee');
  action_fix_decimal($job, 'allowance', 'allowance for expenses');
  // end of validation

  action_do_sql(
    $job, $job_id, 'job_id', 'jobs',
    array('user_id', 'business_id', 'job_id'),
    array('client_id', 'realcontact_id', 'job_title', 'date', 'date_paid', 'fee', 'allowance', 'notes'),
    $job_name, $job_name_ybox
  );

  // Deal with the situation where the client doesn't exist
  if ($client_doesnt_exist) {
    $job_id = $job['job_id'];
    $need_more_information = TRUE;
    $moreinfo_sentence = "Please enter the details for the new client that you wish to file $job_name under.";
    $more_clients[] = array('user_id' => $job['user_id'], 'business_id' => $job['business_id'], 'client_id' => $client_id);
    $hiddenfields .= "    <input type=\"hidden\" name=\"jobs[$job_id][user_id]\" value=\"{$job['user_id']}\" />
    <input type=\"hidden\" name=\"jobs[$job_id][business_id]\" value=\"{$job['business_id']}\" />
    <input type=\"hidden\" name=\"jobs[$job_id][client_id]\" value=\"$client_id\" />\n";
  }
}
