<?php

require_once './php/essentials.php';
include_once './php/nocache.php';
require_once './php/identify/user.php';
$headings = array($user['user_name']);
$navbox = TRUE;
$urls = array("user.php?user_id=$user_id");
require_once './php/identify/business.php';
array_push($headings, $business['business_name'], 'Expenses');
$urls[] = "business.php$unique_id";

define('WIDE_PAGE', TRUE);
require_once './php/header_one.php';
echo '  <link rel="stylesheet" type="text/css" href="stylesheets/expenses.css" />', "\n";
require_once './php/header_two.php';

$result = $db_connection->query('SELECT * FROM cats');
if (! $result)
  trigger_error("I couldn't retrieve the category codes from the " .
    "database.&nbsp; The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);
$cats = array('');
while ($row = $result->fetch_assoc()) {
  $cats[] = $row;
}
$result->free();
unset($result, $row);

$result = $db_connection->query('SELECT * FROM cat_descriptions');
if (! $result)
  trigger_error("I couldn't retrieve the category abbreviations from the " .
    "database.&nbsp; The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);
$cat_descriptions = array();
while ($row = $result->fetch_assoc()) {
  $cat_descriptions[intval($row['descr_id'])] = $row['abbrev'];
}
$result->free();
unset($result, $row);

$allowable = array(0 => TRUE);
$cat_abbrev = array(0 => '');
reset($cats);
while ($cat = next($cats)) {
  $allowable[intval($cat['cat_code'])] = (bool) $cat['allowable'];
  $cat_abbrev[intval($cat['cat_code'])]
      = $cat_descriptions[intval($cat['descr_id'])];
}
unset($cats, $cat_descriptions);

require './php/out_criteria.php';

function category_column($cat_code) {
  global $allowable, $cat_abbrev;
  $cat_code = intval($cat_code);
  return ($allowable[$cat_code] ? '' : '<i>(') . $cat_abbrev[$cat_code] . ($allowable[$cat_code] ? '' : ')</i>');
}

require './php/criteria_dates.php';

$result = $db_connection->query("SELECT *,DATE_FORMAT(date, '%e %M %Y') AS date_formatted " .
                          "FROM expenses $where_clause $order_clause");
if (! $result)
  trigger_error("I've encountered a problem while trying to search your expenses." .
    "&nbsp; The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);
$even = FALSE;
if ($expense = $result->fetch_assoc()) {
  ?>
  <table id="tblexpenses">
    <thead>
      <tr>
        <th scope="col" class="cc">Cat</th>
        <th scope="col">Title</th>
        <th scope="col">Job</th>
        <th scope="col" class="date">Date</th>
        <th scope="col" class="cost">Cost</th>
        <th scope="col" class="cost">Miles</th>
        <th class="note"><img src="images/sticky-notes.png" alt="Notes" title="Notes" /></th>
      </tr>
    </thead>

    <tbody>
<?php
  array_push($open_xhtml_tags, 'table', 'tbody');

  do {
    echo '      <tr class="', $expense['cost'] < 0 ? ($even ? 'negeven' : 'negodd') : ($even ? 'even' : 'odd'), "\">\n";
    echo '        <td class="cc">', category_column($expense['cat_code']), "</td>\n";
    echo '        <td><a href="expense.php', "$unique_id&amp;expense_id={$expense['expense_id']}", '">', htmlify(truncate($expense['expense_title'])), "</a></td>\n";
    echo '        <td>';
    if (! empty($expense['job_id'])) {
      do {
        $jobres = $db_connection->query("SELECT job_title FROM jobs WHERE user_id=" .
            $user_id . " AND business_id=$business_id AND job_id=" . $expense['job_id']);
        if ($jobres === FALSE) break;
        if (($job = $jobres->fetch_row()) === NULL) break;
        if ($job[0] == '') break;
        echo htmlify(truncate($job[0], 25));
        $exp_job_title = htmlify($job[0]);
        echo "<a href=\"commission.php$unique_id&amp;job_id={$expense['job_id']}\">";
        echo '<img class="joblink" src="images/receipt-text-arrow.png" alt="',
             $exp_job_title, '" title="', $exp_job_title, '" /></a>';
        $jobres->free();
      } while (FALSE);
      unset($jobres, $exp_job_title);
    }
    echo "  </td>\n";
    echo '        <td class="date">', $expense['date_formatted'], "</td>\n";
    echo '        <td class="cost">', $expense['cost'          ], "</td>\n";
    echo '        <td class="cost">', $expense['mileage'       ], "</td>\n";
    if (empty($expense['notes'])) {
      echo "        <td class=\"note\">&nbsp;</td>\n";
    } else {
      echo '        <td class="note">
          <img src="images/sticky-note-text.png" alt="note" title="';
      echo htmlentities(truncate($expense['notes'], 512), ENT_COMPAT, 'UTF-8'), "\" />
        </td>\n";
    }
    echo "      </tr>\n";
    $even = ! $even;
  }
  while ($expense = $result->fetch_assoc());

} else {
  echo "  <p>There are no expenses matching the criteria you specified.</p>\n";
}

$result->free();
unset($result, $expense);

close_xhtml_tag('table');

?>

  <p class="noprint" style="margin: 1em 0 0 0;"><a href="expense.php<?php echo $unique_id; ?>&amp;expense_id=newe">Create a new expense</a></p>
</div>

<div style="float: left;">
<?php

$result = $db_connection->query("SELECT SUM(cost),SUM(mileage) FROM expenses $where_clause");
if ($result and ($total = $result->fetch_row())) {
  $total[1] = round($total[1], 1);
  ?>

  <h3>Totals</h3>

  <p>Total costs (excluding mileage): <span style="font-weight: bold;">&pound;&#8197;<?php echo empty($total[0]) ? '0' : $total[0]; ?></span></p>

  <p>Total mileage: <span style="font-weight: bold;"><?php echo (empty($total[1]) ? '0' : $total[1]) . ' mile' . ($total[1] == 1 ? '' : 's'); ?></span></p>
<?php
}

close_xhtml_tag('div');

include './php/footer.php';
