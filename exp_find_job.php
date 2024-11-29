<?php

$headings = array_key_exists('headings', $_REQUEST) ? $_REQUEST['headings'] : array();
 $navbox  = array_key_exists( 'navbox',  $_REQUEST) ? $_REQUEST['navbox'] : TRUE;
  $urls   = array_key_exists(  'urls',   $_REQUEST) ? array_map('htmlspecialchars', $_REQUEST['urls']) : array();
if (get_magic_quotes_gpc()) $headings = array_map('stripslashes', $headings);

require_once './php/essentials.php';
include_once './php/nocache.php';

if (empty($_REQUEST)) redirect('');

foreach (array('user_id', 'business_id', 'expense_id') as $part) {
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
require './php/identify/expense.php';
require_once './php/header_one.php';
echo '  <link rel="stylesheet" type="text/css" href="stylesheets/income.php" /> 
';
require './php/in_criteria.php';
require_once './php/header_two.php';

?>
  <p style="margin-bottom: 1em;">Which job is the expense &lsquo;<?php echo $expense['expense_title'] ?>&rsquo; related to?</p>

  <form accept-charset="UTF-8" name="frm_main" method="post" action="action.php">

<?php insert_hidden_fields(htmlspecialchars($_REQUEST['goto'])); ?>
    <input type="hidden" name="expenses[<?php echo $expense_id; ?>][user_id]" value="<?php echo $user_id; ?>" />
    <input type="hidden" name="expenses[<?php echo $expense_id; ?>][business_id]" value="<?php echo $business_id; ?>" />

    <div style="display: inline-block; position: relative;">
    <table id="tblincome">
      <thead>
        <tr>
          <th>&nbsp;</th>
          <th scope="col" class="cc">Cl.</th>
          <th scope="col">Title</th>
          <th scope="col" class="date">Date</th>
          <th scope="col" class="fee">Fee</th>
          <th class="note"><img src="images/sticky-notes.png" alt="Notes" title="Notes" /></th>
        </tr>
      </thead>

      <tbody>
        <tr class="odd" style="border-bottom: thin solid Black;"> 
          <td><input type="radio" name="expenses[<?php echo $expense_id; ?>][job_id]" value=""<?php
  echo is_null($expense['job_id']) ? ' checked="checked"' : ''; ?> id="radionull" /></td>
          <td class="cc">&mdash;</td> 
          <td><label for="radionull">None</label></td> 
          <td class="date">&mdash;</td> 
          <td style="text-align: center;">&mdash;</td> 
          <td class="notes">&nbsp;</td> 
        </tr>
<?php
array_push($open_xhtml_tags, 'form', 'div', 'table', 'tbody');

$result = $db_connection->query("SELECT *,DATE_FORMAT(date, '%e %M %Y') " .
                "AS date_formatted FROM jobs $where_clause $order_clause");
if (! $result)
  trigger_error("I've encountered a problem while trying to search your jobs." .
    "&nbsp; The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);

$even = TRUE;
while ($job = $result->fetch_assoc()) {
  echo '        <tr class="', $job['fee'] < 0 ? ($even ? 'negeven' : 'negodd') : ($even ? 'even' : 'odd'), "\">\n";
  echo '          <td><input type="radio" name="expenses[', $expense_id, '][job_id]" value="', $job['job_id'];
  if ($expense['job_id'] == $job['job_id']) echo '" checked="checked';
  echo '" id="radio', $job['job_id'], "\" /></td>\n";
  echo '          <td class="cc">';
  do {
    $clientres = $db_connection->query("SELECT abbrev FROM clients WHERE user_id=" .
        $user_id . " AND business_id=$business_id AND client_id=" . $job['client_id']);
    if ($clientres === FALSE) break;
    if (($client = $clientres->fetch_row()) === NULL) break;
    echo $client[0];
    $clientres->free();
  } while (FALSE);
  unset($clientres, $client);
  echo "</td>\n";
  echo '          <td><label for="radio', $job['job_id'];
  echo '">', htmlify(truncate($job['job_title'])), "</label></td>\n";
  echo '          <td class="date">', $job['date_formatted'], "</td>\n";
  echo '          <td class="fee">',  $job['fee'           ], "</td>\n";
  if (empty($job['notes'])) {
    echo "      <td class=\"note\">&nbsp;</td>\n";
  } else {
    echo '      <td class="note">
      <img src="images/sticky-note-text.png" alt="note" title="';
    echo htmlentities($job['notes'], ENT_COMPAT, 'UTF-8'), "\" />
    </td>\n";
  }
  $even = ! $even;
}

$result->free();
unset($result, $job);

close_xhtml_tag('table');

?>

      <p style="height: 1.9em;">&nbsp;</p>

      <div class="savebutton">
    	<script type="text/javascript">
        // <!-- [CDATA[
          document.write('<a href="javascript:history.go(-2)">Cancel</a>');
        // ]] -->
        </script>

        <input type="submit" name="btn_save" value="Save" />
      </div>
<?php include './php/footer.php';
