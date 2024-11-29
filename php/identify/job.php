<?php

if (empty($job_id)) {
  if (empty($_GET['job_id'])) {
    if ($job_id == '0' or $_GET['job_id'] == '0')
      trigger_error('0 is not a valid job ID.', E_USER_ERROR);
    else trigger_error('No job ID was specified.', E_USER_ERROR);
  }
  $job_id = $_GET['job_id'];
}

if (($intid = (int) $job_id) <= 0) $new = TRUE;
else {$new = FALSE; $job_id = $intid;}
unset($intid);

if ($new) {
  $job['job_title'] = 'New Job';
  $job['client_id'] = NULL;
} else {
  $result = $db_connection->query("SELECT *," .
      "DAYNAME(date) AS dateday," .
      "DAYNAME(date_paid) AS paidday FROM jobs WHERE user_id=" .
      $user_id . " AND business_id=$business_id AND job_id=$job_id")
    or trigger_error("I couldn't retrieve the details of job $numero $job_id for " .
      "$business_name.&nbsp; The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);
  $job = $result->fetch_assoc() or trigger_error(
    "There is no job $numero $job_id for the business &lsquo;$business_name&rsquo;.", E_USER_ERROR);
  $result->free();

  foreach ($job as $k => &$v) {
    // Leave $job['client_id'] intact if it is NULL
    if ($k == 'client_id') continue;
    if ($v === NULL) unset($job[$k]);
    elseif ($k == 'notes') $v = htmlentities($v, ENT_COMPAT, 'UTF-8');
    else $v = htmlify($v);
  }
  unset($result, $v);    // It is necessary to unset $v.
}
