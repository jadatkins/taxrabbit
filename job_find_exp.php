<?php

$headings = array_key_exists('headings', $_REQUEST) ? $_REQUEST['headings'] : array();
 $navbox  = array_key_exists( 'navbox',  $_REQUEST) ? $_REQUEST[ 'navbox' ] : TRUE;
  $urls   = array_key_exists(  'urls',   $_REQUEST) ? array_map('htmlspecialchars', $_REQUEST['urls']) : array();
if (get_magic_quotes_gpc()) $headings = array_map('stripslashes', $headings);

require_once './php/essentials.php';
include_once './php/nocache.php';

if (empty($_REQUEST)) redirect('');

foreach (array('user_id', 'business_id', 'job_id') as $part) {
  if (!array_key_exists($part, $_REQUEST))
    friendly_error("No $part was specified. There must be something wrong with the page you've just come from.", TRUE);
  if (preg_match('/^\d+$/', $_REQUEST[$part])) {
    settype($_REQUEST[$part], 'integer');
    $$part = $_REQUEST[$part];
  }
  else
    trigger_error("&lsquo;{$_REQUEST[$part]}&rsquo; isn't a valid $part.", E_USER_ERROR);
}

require './php/identify/authenticate.php';
$business_name = htmlify(truncate($_REQUEST['headings'][1]));
require './php/identify/job.php';
require_once './php/header_one.php';
echo '  <link rel="stylesheet" type="text/css" href="stylesheets/expenses.css" /> 
';
require './php/out_criteria.php';
require_once './php/header_two.php';

?>
  <p style="margin-bottom: 1em;">Which expenses are related to the job &lsquo;<?php echo $job['job_title'] ?>&rsquo;?</p>

  <form accept-charset="UTF-8" name="frm_main" method="post" action="action.php">

<?php

$open_xhtml_tags[] = 'form';

insert_hidden_fields(htmlspecialchars($_REQUEST['goto']));

$result = $db_connection->query("SELECT *,DATE_FORMAT(date, '%e %M %Y') " .
                "AS date_formatted FROM expenses $where_clause $order_clause");
if (! $result)
  trigger_error("I've encountered a problem while trying to search your expenses." .
    "&nbsp; The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);

?>
  <div style="display: inline-block; position: relative;">
<?php
$open_xhtml_tags[] = 'div';

if ($expense = $result->fetch_assoc()) {
  $noexpenses = FALSE;

  array_push($open_xhtml_tags, 'table', 'tbody');
  ?>
    <table id="tblexpenses">
      <thead>
        <tr>
          <th>&nbsp;</th>
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

  $even = FALSE;
  do {
    echo '        <tr class="', $expense['cost'] < 0 ? ($even ? 'negeven' : 'negodd') : ($even ? 'even' : 'odd'), "\">\n";
    echo "          <td>\n";
    echo '            <input type="hidden" name="expenses[', $expense['expense_id'], '][user_id]" value="', $user_id, "\" />\n";
    echo '            <input type="hidden" name="expenses[', $expense['expense_id'], '][business_id]" value="', $business_id, "\" />\n";
    echo '            <input type="hidden" name="expenses[', $expense['expense_id'], '][job_id]" value="', ($expense['job_id'] == $job_id) ? '' : $expense['job_id'], '" />', "\n";
    echo '            <input type="checkbox" name="expenses[', $expense['expense_id'], '][job_id]" value="', $job_id;
    if ($expense['job_id'] == $job_id) echo '" checked="checked';
    echo '" id="chk', $expense['expense_id'], "\" />\n";
    echo "          </td>\n";
    echo '          <td><label for="chk', $expense['expense_id'];
    echo '">', htmlify(truncate($expense['expense_title'])), "</label></td>\n";
    echo '          <td>';
    if (empty($expense['job_id'])) {
      echo '&nbsp;';
    } else {
      do {
        $jobres = $db_connection->query("SELECT job_title FROM jobs WHERE user_id=" .
            $user_id . " AND business_id=$business_id AND job_id=" . $expense['job_id']);
        if ($jobres === FALSE) break;
        if (($job = $jobres->fetch_row()) === NULL) break;
        if ($job[0] == '') break;
        echo htmlify(truncate($job[0]));
        $jobres->free();
      } while (FALSE);
      unset($jobres);
    }
    echo "</td>\n";
    echo '          <td class="date">', $expense['date_formatted'], "</td>\n";
    echo '          <td class="cost">', $expense['cost'          ], "</td>\n";
    echo '          <td class="cost">', $expense['mileage'       ], "</td>\n";
    if (empty($expense['notes'])) {
      echo "          <td class=\"note\">&nbsp;</td>\n";
    } else {
      echo '          <td class="note">
            <img src="images/sticky-note-text.png" alt="note" title="';
      echo htmlentities($expense['notes'], ENT_COMPAT, 'UTF-8'), "\" />
        </td>\n";
    }
    echo "        </tr>\n";
    $even = ! $even;
  } while ($expense = $result->fetch_assoc());

  close_xhtml_tag('table');

} else {
  $noexpenses = TRUE;
  echo '    <p style="margin-top: 0;">There are no expenses matching the criteria you specified.</p>', "\n";
}

$result->free();
unset($result, $expense);

?>

      <p style="height: 1.9em;">&nbsp;</p>

      <div class="savebutton">
    	<script type="text/javascript">
        // <!-- [CDATA[
          document.write('<a href="javascript:history.go(-2)">Cancel</a>');
        // ]] -->
        </script>
<?php if (!$noexpenses) echo "\n", '        <input type="submit" name="btn_save" value="Save" />', "\n"; ?>
      </div>
<?php include './php/footer.php';
