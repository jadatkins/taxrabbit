<?php

if (get_magic_quotes_gpc()) $headings = array_map('stripslashes', $headings);
$urls = array_map('htmlspecialchars', $urls);

//array_pop($headings);
$temp = 0;
if (count($more_clients)     ) $temp += 8;
if (count($more_realcontacts)) $temp += 4;
if (count($more_jobs)        ) $temp += 2;
if (count($more_expenses)    ) $temp += 1;
$navbox = TRUE;
switch ($temp) {
  case 8:
    $headings[] = count($more_clients) > 1 ? 'New Clients' : 'New Client';
    break;
  case 4:
    $headings[] = count($more_realcontacts) > 1 ? 'New Contacts' : 'New Contact';
    break;
  case 2:
    $headings[] = count($more_jobs) > 1 ? 'New Jobs' : 'New Job';
    break;
  case 1:
    $headings[] = count($more_expenses) > 1 ? 'New Expenses' : 'New Expense';
    break;
  default:
    $headings[] = 'New Records';
    $saveall = TRUE;
}
unset($temp);

require_once './php/header_one.php';
require_once './php/header_two.php';

echo '<p>', $moreinfo_sentence, "</p>\n\n";

?>
  <form accept-charset="UTF-8" name="frm_main" method="post" action="action.php" onsubmit="disable()">

<?php

$new = TRUE;
foreach ($more_clients as $client) {
  $user_id = $client['user_id'];
  $business_id = $client['business_id'];
  $client_id = $client['client_id'];
  $client['client_name'] = 'New Client';
  $client['parent_id'] = NULL;
  require './php/forms/client.php';
}
foreach ($more_realcontacts as $realcontact) {
  $user_id = $realcontact['user_id'];
  $realcontact_id = $realcontact['realcontact_id'];
  $realcontact['fullname'] = 'New Contact';
  require './php/forms/contact.php';
}
foreach ($more_jobs as $job) {
  $user_id = $job['user_id'];
  $business_id = $job['business_id'];
  $job_id = $job['job_id'];
  $job['job_title'] = 'New Job';
  $job['client_id'] = NULL;
  require './php/forms/job.php';
}
foreach ($more_expenses as $expense) {
  $user_id = $expense['user_id'];
  $business_id = $expense['business_id'];
  $expense_id = $expense['expense_id'];
  $expense['expense_title'] = 'New Expense';
  require './php/forms/expense.php';
}

echo "\n";
insert_hidden_fields($_POST['goto']);

?>
  </form>
<?php include './php/footer.php';
