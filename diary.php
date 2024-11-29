<?php

require_once './php/essentials.php';
include_once './php/nocache.php';
require_once './php/identify/user.php';
$headings = array($user['user_name']);
$navbox = TRUE;
$urls = array("user.php?user_id=$user_id");
require_once './php/identify/business.php';
$headings[] = $business['business_name'];
$urls[] = "business.php$unique_id";

$where_clause = "WHERE user_id=$user_id AND business_id=$business_id";

if (array_key_exists('bookday', $_GET) and preg_match('/^\d\d?$/', $_GET['bookday'])) {
  $book = (int) $_GET['bookday'];
  $bookparam = "&amp;bookday=$book";
} else {
  $book = 6;
  $bookparam = '';
  do {
    $result = $db_connection->query("SELECT bookday FROM businesses $where_clause");
    if (! $result) break;
    $row = $result->fetch_row();
    if (! $row) break;
    $book = $row[0];
    unset($row, $result);
  } while (FALSE);
}
if ($book > 28) $book = 1;
// $book is the first day of the accounting month.
$todaymonth = idate('m');
if ($book > idate('d')) $todaymonth --;
$yearview = FALSE;
if (array_key_exists('month', $_GET) and preg_match('/^(\d{4})-(\d\d)$/', $_GET['month'], $mon_array)) {
  $year  = (int) $mon_array[1];
  $month = (int) $mon_array[2];
} elseif (array_key_exists('year', $_GET) and preg_match('/^(\d{4})$/', $_GET['year'], $mon_array)) {
  $year  = (int) $mon_array[1];
  $month = 4;
  $yearview = TRUE;
} else {
  $year  = idate('Y');
  $month = $todaymonth;
}
unset($mon_array);
if ($yearview) {
  $after  = mktime(12, 0, 0, $month, $book, $year - 1);
  $before = mktime(12, 0, 0, $month, $book - 1, $year);
} else {
  $after  = mktime(12, 0, 0, $month, $book, $year);
  $before = mktime(12, 0, 0, $month + 1, $book - 1, $year);
}
if ($yearview) {
  $prevmonthname = ($year - 2) . ' ~ ' . ($year - 1);
  $thismonthname = ($year - 1) . ' ~';
  $nextmonthname = $year . ' ~ ' . ($year + 1);
} else {
  if ($book == 1) {
    $thismonthname = date('F', mktime(12, 0, 0, $month, 15, $year));
    $prevmonthname = date('F', mktime(12, 0, 0, $month - 1, 15, $year));
    $nextmonthname = date('F', mktime(12, 0, 0, $month + 1, 15, $year));
  } else {
    $thismonthname = date('M', mktime(12, 0, 0, $month, 15, $year)) .
          ' / ' . date('M', mktime(12, 0, 0, $month + 1, 15, $year));
    $prevmonthname = date('M', mktime(12, 0, 0, $month - 1, 15, $year)) .
          ' / ' . date('M', mktime(12, 0, 0, $month, 15, $year));
    $nextmonthname = date('M', mktime(12, 0, 0, $month + 1, 15, $year)) .
          ' / ' . date('M', mktime(12, 0, 0, $month + 2, 15, $year));
  }
}
if ($yearview) {
  if ($book == 1)
    $headings[] = date('M Y ~ ', $after) . date('M Y', $before);
  else
    $headings[] = date('j M Y ~ ', $after) . date('j M Y', $before);
}
elseif ($book == 1)
  $headings[] = "$thismonthname $year";
elseif ($month == 12)
  $headings[] = date('j M Y ~ ', $after) . date('j M Y', $before);
else
  $headings[] = date('j M ~ ', $after) . date('j M Y', $before);

define('WIDE_PAGE', TRUE);
require_once './php/header_one.php';
echo '  <link rel="stylesheet" type="text/css" href="stylesheets/income.php" />', "\n";
if (is_int(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) and
    is_int(strpos($_SERVER['HTTP_USER_AGENT'], 'Windows')) ) $tick = 'a';
else $tick = '&#10003;';
echo '  <link rel="stylesheet" type="text/css" href="stylesheets/expenses.css" />', "\n";
echo '  <script type="text/javascript" src="javascript/spawn.js"></script>', "\n";
require_once './php/header_two.php';

$where_clause .= " AND (date IS NULL OR (date>='" . date('Y-m-d', $after) .
                "' AND date<='" . date('Y-m-d', $before) . "'))";
if ($book != 1) {
  if ($month == 12)
    $headings[count($headings)-1] = date('M Y', mktime(12, 0, 0, $month, 15, $year)) .
                        ' / ' . date('M Y', mktime(12, 0, 0, $month + 1, 15, $year));
  else
    $headings[count($headings)-1] = $thismonthname . " $year";
}

$could_be_blank = TRUE;

?>
  <form accept-charset="UTF-8" name="frm_main" method="post" action="action.php" onsubmit="disable()">

<?php

insert_hidden_fields('diary.php?' . htmlspecialchars($_SERVER['QUERY_STRING']));
array_push($open_xhtml_tags, 'form', 'div', 'div', 'div');

?>
<div style="display: table; width: 100%;">
<div style="display: table-row;">
<div style="display: table-cell; width: 50%; padding-right: 4px;">
  <h3 style="margin: 0 0 0.5em 0;">Income</h3>

<?php

$result = $db_connection->query("SELECT *,DATE_FORMAT(date, '%a %e %b') AS " .
                  "date_formatted FROM jobs $where_clause ORDER BY date ASC");
if (! $result)
  trigger_error("I've encountered a problem while trying to load your jobs." .
    "&nbsp; The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);
$even = FALSE;
if ($job = $result->fetch_assoc()) {
  ?>
  <table id="tblincome">
    <thead>
      <tr>
        <th scope="col">Title</th>
        <th scope="col" class="date">Date</th>
        <th scope="col" class="fee">Fee</th>
        <th scope="col" class="paid">Paid</th>
        <th class="note"><img src="images/sticky-notes.png" alt="Notes" title="Notes" />
      </tr>
    </thead>

    <tbody>
<?php
  array_push($open_xhtml_tags, 'table', 'tbody');

  do {
    echo '    <tr class="', $job['fee'] < 0 ? ($even ? 'negeven' : 'negodd') : ($even ? 'even' : 'odd'), "\">\n";
    echo '      <td><a href="commission.php', "$unique_id&amp;job_id={$job['job_id']}",
            '">', htmlify(truncate($job['job_title'])), "</a></td>\n";
    echo '      <td class="date">', $job['date_formatted'], "</td>\n";
    echo '      <td class="fee">',  $job['fee'           ], "</td>\n";
    echo '      <td class="paid">', ($job['date_paid'] ? $tick : ' '), "</td>\n";

    if (empty($job['notes'])) {
      echo "      <td class=\"note\">&nbsp;</td>\n";
    } else {
      echo '      <td class="note">
        <img src="images/sticky-note-text.png" alt="note" title="';
      echo htmlentities(truncate($job['notes'], 512), ENT_COMPAT, 'UTF-8'), "\" />
      </td>\n";
    }

    echo "    </tr>\n";
    $even = ! $even;
  }
  while ($job = $result->fetch_assoc());

} else {
  echo '<p>There are no jobs in ', $thismonthname, ($yearview ? " $year" : ''),
       " for the business &lsquo;$business_name&rsquo;.</p>\n";
}

$result->free();
unset($result, $job);

close_xhtml_tag('table');

$result = $db_connection->query("SELECT SUM(fee) FROM jobs $where_clause");
if ($result and ($total = $result->fetch_row())) {
  ?>
  <h3>Total Income</h3>

  <p>Total income shown above: <span style="font-weight: bold;">&pound;&#8197;<?php echo (empty($total[0]) ? '0' : $total[0]); ?></span></p>
<?php
}

?>
  <p id="job_spawn" class="noprint" style="margin-top: 1em;"><a onclick="return newjob();"
   href="commission.php<?php echo $unique_id; ?>&amp;job_id=newj">Create a new job</a></p>

  <div id="job_cage" style="visibility: hidden; position: absolute;">
<?php

$new = TRUE;
$job_id = uniqid('job');
$job['job_title'] = 'New Job';
$job['client_id'] = NULL;
$saveall = TRUE;
require './php/forms/job.php';

?>
  </div>
</div>

<div style="display: table-cell; width: 50%; padding-left: 4px;">
  <h3 style="margin: 0 0 0.5em 0;">Expenses</h3>

<?php

$result = $db_connection->query("SELECT *,DATE_FORMAT(date, '%a %e %b') AS " .
              "date_formatted FROM expenses $where_clause ORDER BY date ASC");
if (! $result)
  trigger_error("I've encountered a problem while trying to search your expenses." .
    "&nbsp; The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);
$even = FALSE;
if ($expense = $result->fetch_assoc()) {
  ?>
  <table id="tblexpenses">
    <thead>
      <tr>
        <th scope="col">Title</th>
        <th scope="col" class="date">Date</th>
        <th scope="col" class="cost">Cost</th>
        <th class="note"><img src="images/sticky-notes.png" alt="Notes" title="Notes" /></th>
      </tr>
    </thead>

    <tbody>
<?php
  array_push($open_xhtml_tags, 'table', 'tbody');

  do {
    echo '      <tr class="';
    echo $expense['cost'] < 0 ? ($even ? 'negeven' : 'negodd') : ($even ? 'even' : 'odd');
    echo ($expense['cat_code'] > 30 && $expense['cat_code'] < 48 ? ' disallowable' : '');
    echo "\">\n";
    echo '        <td><a href="expense.php', $unique_id, "&amp;expense_id={$expense['expense_id']}", '">',
         htmlify(truncate($expense['expense_title'])), "</a></td>\n";
    echo '        <td class="date">', $expense['date_formatted'], "</td>\n";
    echo '        <td class="cost">';
    echo ($expense['cost'] ? '&pound; ' . $expense['cost'] : '');
    echo ($expense['cost'] and $expense['mileage'] ? ' / ' : '');
    echo ($expense['mileage'] ? $expense['mileage'] . ' m' : '');
    echo "</td>\n";
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
  echo '<p>There are no expenses in ', $thismonthname, ($yearview ? " $year" : ''),
       " for the business &lsquo;$business_name&rsquo;.</p>\n";
}

$result->free();
unset($result, $expense);

close_xhtml_tag('table');

$result = $db_connection->query("SELECT SUM(cost),SUM(mileage) FROM expenses $where_clause AND (cat_code IS NULL OR cat_code<31)");
if ($result and ($total = $result->fetch_row())) {
  $total[1] = round($total[1], 1);
  ?>

  <h3>Total Expenses</h3>

  <p>Total allowable costs (except mileage and vehicles and equipment): <span style="font-weight: bold;">&pound;&#8197;<?php echo empty($total[0]) ? '0' : $total[0]; ?></span></p>

  <p>Total allowable mileage: <span style="font-weight: bold;"><?php echo (empty($total[1]) ? '0' : $total[1]) . ' mile' . ($total[1] == 1 ? '' : 's'); ?></span></p>
<?php
}

$result = $db_connection->query("SELECT SUM(cost) FROM expenses $where_clause AND cat_code>47");
if ($result and ($total = $result->fetch_row())) {
  ?>

  <p>Total vehicles and equipment: <span style="font-weight: bold;">&pound;&#8197;<?php echo empty($total[0]) ? '0' : $total[0]; ?></span></p>
<?php
}

$open_xhtml_tags[] = 'div';

?>
  <p id="expense_spawn" class="noprint" style="margin-top: 1em;"><a onclick="return newexpense();"
   href="expense.php<?php echo $unique_id; ?>&amp;expense_id=newe">Create a new expense</a></p>

  <div id="expense_cage" style="visibility: hidden; position: absolute;">
<?php

$new = TRUE;
$expense_id = uniqid('exp');
$expense['expense_title'] = 'New Expense';
$expense['job_id'] = NULL;
$saveall = TRUE;

require './php/forms/expense.php';

close_xhtml_tag('div', CLOSE_FIRST_XHTML_TAG);

if ($yearview) {
  $todaylink = date('md') > '0405' ? idate('Y') + 1 : date('Y');
  $prevmonth = $year - 1;
  $nextmonth = $year + 1;
  $prevhref = "href=\"diary.php{$unique_id}&amp;year={$prevmonth}{$bookparam}\"";
  $thishref = "href=\"diary.php{$unique_id}&amp;year={$todaylink}{$bookparam}\"";
  $nexthref = "href=\"diary.php{$unique_id}&amp;year={$nextmonth}{$bookparam}\"";
} else {
  $todaylink = date('Y-m', mktime(12, 0, 0, $todaymonth, 15, idate('Y')));
  $prevmonth = date('Y-m', mktime(12, 0, 0, $month-1, 15, $year));
  $nextmonth = date('Y-m', mktime(12, 0, 0, $month+1, 15, $year));
  $prevhref = "href=\"diary.php$unique_id".($prevmonth==$todaylink?'':"&amp;month=$prevmonth")."$bookparam\"";
  $thishref = "href=\"diary.php{$unique_id}{$bookparam}\"";
  $nexthref = "href=\"diary.php$unique_id".($nextmonth==$todaylink?'':"&amp;month=$nextmonth")."$bookparam\"";
}

?>

<div class="pagenumbers">
  <p>
    <span class="pagerow">
      <span class="pagecell"><?php
echo "<a class=\"arrow\" $prevhref>&larr;</a><br />\n<a $prevhref>$prevmonthname";
if (array_key_exists('month', $_GET) or ($yearview and $year != $todaylink)) { ?></a></span>
      <span class="pagecell"><?php
echo "<a class=\"arrow\" $thishref>&loz;</a><br />\n<a $thishref>Today"; } ?></a></span>
      <span class="pagecell"><?php
echo "<a class=\"arrow\" $nexthref>&rarr;</a><br />\n<a $nexthref>$nextmonthname"; ?></a></span>
    </span>
  </p>
  <p>
    <span class="pagerow">
      <span class="pagecell"><a <?php
if ($yearview) {
  $monthlink = date('Y-m', mktime(12, 0, 0, $todaymonth, 15, $todaymonth < 4 ? $year : $year-1 ));
  echo "href=\"diary.php{$unique_id}&amp;month={$monthlink}{$bookparam}\">View by month";
} else {
  echo "href=\"diary.php{$unique_id}&amp;year=" . ( $month < 4 ? $year : $year+1 ) . "{$bookparam}\">View by year";
}
?></a></span>
    </span>
  </p>
</div>

<?php include './php/footer.php';
