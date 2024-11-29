<?php

require_once './php/essentials.php';
include_once './php/nocache.php';
require_once './php/identify/user.php';
$headings = array($user['user_name']);
$navbox = TRUE;
$urls = array("user.php?user_id=$user_id");
require_once './php/identify/business.php';
array_push($headings, $business['business_name'], 'Income');
$urls[] = "business.php$unique_id";

define('WIDE_PAGE', TRUE);
require_once './php/header_one.php';
echo '  <link rel="stylesheet" type="text/css" href="stylesheets/income.php" />', "\n";
if (is_int(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) and
    is_int(strpos($_SERVER['HTTP_USER_AGENT'], 'Windows')) ) $tick = 'a';
else $tick = '&#10003;';
require './php/in_criteria.php';
require_once './php/header_two.php';

require './php/criteria_dates.php';

$result = $db_connection->query("SELECT *,DATE_FORMAT(date, '%e %M %Y') " .
                "AS date_formatted FROM jobs $where_clause $order_clause");
if (! $result)
  trigger_error("I've encountered a problem while trying to search your jobs." .
    "&nbsp; The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);
$even = FALSE;
if ($job = $result->fetch_assoc()) {
  ?>
  <table id="tblincome">
    <thead>
      <tr>
        <th scope="col" class="cc">Cl.</th>
        <th scope="col">Title</th>
        <th scope="col" class="date">Date</th>
        <th scope="col" class="fee">Fee</th>
        <th scope="col" class="paid">Paid</th>
        <th class="note"><img src="images/sticky-notes.png" alt="Notes" title="Notes" /></th>
        <th>&nbsp;</th>
        <th scope="col">Contact</th>
        <th scope="col">Telephone</th>
        <th scope="col">Email</th>
      </tr>
    </thead>

    <tbody>
<?php
  array_push($open_xhtml_tags, 'table', 'tbody');

  do {
    echo '    <tr class="', $job['fee'] < 0 ? ($even ? 'negeven' : 'negodd') : ($even ? 'even' : 'odd'), "\">\n";
    echo '      <td class="cc">';
    do {
      $clientres = $db_connection->query("SELECT abbrev FROM clients WHERE user_id=" .
          $user_id . " AND business_id=$business_id AND client_id=" . $job['client_id']);
      if ($clientres === FALSE) break;
      if (($client = $clientres->fetch_row()) === NULL) break;
      echo $client[0];
      $clientres->free();
    } while (FALSE);
    unset($clientres);
    echo "</td>\n";
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

    // get realcontact_id from job
    if (empty($job['realcontact_id'])) $rc_ids = array();
    else $rc_ids = array($job['realcontact_id']);
    $nextclient = $job['client_id'];
    $explanation = '&nbsp;';

    // failing that, get realcontact_ids from client or nearest ancestor
    while (empty($rc_ids) and $nextclient) {
      $rc_res = $db_connection->query("SELECT realcontact_id FROM metacontacts WHERE " .
          "user_id=$user_id AND business_id=$business_id AND client_id=$nextclient");
      if ($rc_res === FALSE) break;
      while (($row = $rc_res->fetch_row()) !== NULL)
        $rc_ids[] = $row[0];
      $rc_res->free();

      $rc_res = $db_connection->query("SELECT abbrev,parent_id FROM clients WHERE " .
          "user_id=$user_id AND business_id=$business_id AND client_id=$nextclient");
      if ($rc_res === FALSE) break;
      if (($row = $rc_res->fetch_assoc()) === NULL) break;
      $explanation = "{$row['abbrev']}: ";
      $nextclient = $row['parent_id'];
    }

    // get all contacts' details
    $contacts = array();
    foreach ($rc_ids as $realcontact_id) {
      $rc_res = $db_connection->query("SELECT CONCAT_WS(' ',forenames,surname) AS fullname,phone,email " .
          "FROM realcontacts WHERE user_id=$user_id AND realcontact_id=" . $realcontact_id);
      if ($rc_res === FALSE) break;
      if (($contact = $rc_res->fetch_assoc()) === NULL) continue;
      foreach ($contact as &$field)
        $field = htmlify(truncate($field));
      $contacts[$realcontact_id] = $contact;
    }

    echo "      <td class=\"expl\">";
    // if no contacts, print blank table cells
    if (empty($contacts)) echo "&nbsp;</td>\n      <td>&nbsp;</td>\n      <td>&nbsp;</td>\n      <td>&nbsp;</td>\n";
    // print explanation (client abbrev)
    else echo "$explanation</td>\n      <td>\n";

    // print fullname
    $notfirst = FALSE;
    foreach ($contacts as $realcontact_id => $contact) {
      if ($notfirst) echo "<br />\n";
      if ($contact['fullname'])
        echo "        <a href=\"contact.php?user_id=$user_id&amp;realcontact_id=$realcontact_id\">{$contact['fullname']}</a>";
      else
        echo '        &nbsp;';
      $notfirst = TRUE;
    }
    if (!empty($contacts)) echo "\n      </td>\n      <td>\n";

    // print phone
    $notfirst = FALSE;
    foreach ($contacts as $realcontact_id => $contact) {
      if ($notfirst) echo "<br />\n";
      if ($contact['phone']) echo "        {$contact['phone']}";
      else echo '        &nbsp;';
      $notfirst = TRUE;
    }
    if (!empty($contacts)) echo "\n      </td>\n      <td>\n";

    // print email
    $notfirst = FALSE;
    foreach ($contacts as $realcontact_id => $contact) {
      if ($notfirst) echo "<br />\n";
      if ($contact['email']) echo "        <a href=\"mailto:{$contact['email']}\">{$contact['email']}</a>";
      else echo '        &nbsp;';
      $notfirst = TRUE;
    }
    if (!empty($contacts)) echo "\n      </td>\n";

    unset($contacts, $nextclient, $rc_res, $row, $rc_ids, $explanation, $realcontact_id);
    echo "    </tr>\n";
    $even = ! $even;
  }
  while ($job = $result->fetch_assoc());

} else {
  echo "<p>There are no jobs matching the criteria you specified.</p>\n";
}

$result->free();
unset($result, $job);

close_xhtml_tag('table');

echo '<p class="noprint" style="margin-top: 1em;"><a href="commission.php', $unique_id, '&amp;job_id=newj">Create a new job</a></p>', "\n";

$result = $db_connection->query("SELECT SUM(fee) FROM jobs $where_clause");
if ($result and ($total = $result->fetch_row()))
  echo '<h3>Total</h3>
<p>Total income for the above list: <span style="font-weight: bold;">&pound;&#8197;', (empty($total[0]) ? '0' : $total[0]), '</span></p>', "\n";

include './php/footer.php';
