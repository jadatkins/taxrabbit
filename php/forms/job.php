<?php

require_once BASEURL . 'php/forms/client_menu.php';
require_once BASEURL . 'php/forms/join.php';
$jsuf = alphanum_counter();

?>
    <script type="text/javascript">
    // <!-- [CDATA[
      function job_enbt_exp<?php echo $jsuf; ?>() {
	if (document.getElementById("expenses<?php echo $jsuf; ?>").selectedIndex == -1) {
	  document.getElementById("btnjobexpgto<?php echo $jsuf; ?>").disabled=true;
	} else {
	  document.getElementById("btnjobexpgto<?php echo $jsuf; ?>").disabled=false;
	}
      }

      function job_enbt_cli<?php echo $jsuf; ?>() {
	sel = document.getElementById("client<?php echo $jsuf; ?>")
	idx = sel.selectedIndex;
	val = sel.options.item(idx).value;
	if (parseInt(val) > 0) {
	  document.getElementById("btnjobcligto<?php echo $jsuf; ?>").disabled=false;
	} else {
	  document.getElementById("btnjobcligto<?php echo $jsuf; ?>").disabled=true;
	}
      }
    // ]] -->
    </script>

    <fieldset class="main">
      <legend><?php echo $job['job_title']; ?></legend>

      <input type="hidden" name="jobs[<?php echo $job_id; ?>][user_id]" value="<?php echo $user_id; ?>" />
      <input type="hidden" name="jobs[<?php echo $job_id; ?>][business_id]" value="<?php echo $business_id; ?>" />
<?php

$read_only = FALSE;
$delete_button = '';

if ($new) { ?>
      <input type="hidden" name="jobs[<?php echo $job_id; ?>][guid]" value="<?php echo mt_rand(); ?>" />
<?php }

elseif (empty($job['date']) or strcmp($job['date'], FINALDATE) >= 0) {
  $delete_button = '

      <div class="deletebutton">
        <input type="submit" name="btn_delete[job' .
$user_id . '-' . $business_id . '-' . $job_id . ']" value="Delete" />
      </div>
'; }

else {
  $read_only = TRUE;
?>
      <input type="hidden" name="jobs[<?php echo $job_id; ?>][readonly]" value="1" />

      <p>You cannot modify this job because it is too far in the past.</p>
<?php }

?>

      <table>
        <tr>
	  <td colspan="2"><label for="title<?php echo $jsuf; ?>">Job title / reference:</label></td>
          <td>
	    <input type="text" id="title<?php
echo $jsuf; ?>" name="jobs[<?php echo $job_id; ?>][job_title]" size="40"<?php
if (empty($could_be_blank)) echo ' required="required"'; ?> maxlength="85"<?php
if (! $new) echo ' value="', $job['job_title'], '"';
if ($read_only) echo ' readonly'; ?> />
	    <input type="submit" class="offscreen" tabindex="-1" />
	  </td>
	</tr>

        <tr>
	  <td colspan="2"><label for="client<?php echo $jsuf; ?>">Client:</label></td>
          <td>
<?php

array_push($open_xhtml_tags, 'fieldset', 'table', 'tr', 'td');

draw_client_dropdown("client$jsuf", "jobs[$job_id][client_id]", $job['client_id'],
		  NULL, 6, NULL, " onchange=\"job_enbt_cli$jsuf()\"", $read_only);

?>
	    <input type="submit" class="button" name="btn_job_cli_gto" id="btnjobcligto<?php echo $jsuf; ?>" value="View" />
<?php close_xhtml_tag('tr'); ?>

        <tr>
	  <td colspan="2"><label for="date1_<?php echo $jsuf; ?>">Date:</label></td>
          <td>
	    <input type="date" id="date1_<?php echo $jsuf; ?>"<?php
if (empty($could_be_blank)) echo ' required="required"'; ?> name="jobs[<?php
echo $job_id; ?>][date]" size="14" min="<?php
$firstdate = date('Y-m-d', mktime(9, 0, 0, substr(FINALDATE,5,2), substr(FINALDATE,8,2)+1, substr(FINALDATE,0,4)));
echo empty($job['date']) ? $firstdate : min($job['date'], $firstdate);
echo '" max="', idate('Y') + 5, substr(BOOKDATE, 4), '"';
if (isset($job['date'])) echo " value=\"{$job['date']}\"";
if ($read_only) echo ' readonly';
?> onchange="weekday(document.getElementById('wday<?php echo $jsuf; ?>'), this.value)" />
            <span style="font-style: italic;" id="wday<?php echo $jsuf; ?>"><?php
if (isset($job['dateday'])) echo "({$job['dateday']})";
else echo '(e.g. &lsquo;today&rsquo;, &lsquo;', date('j F'), '&rsquo;, &lsquo;31/12/', idate('Y'), '&rsquo;)';
?></span>
          </td>
	</tr>

        <tr>
	  <td colspan="2"><label for="date2_<?php echo $jsuf; ?>">Date paid:</label></td>
          <td>
            <input type="date" id="date2_<?php echo $jsuf; ?>" name="jobs[<?php
echo $job_id; ?>][date_paid]" size="14" min="<?php
$foo = substr(FINALDATE, 0, 4) . '-01-01';
echo empty($job['date_paid']) ? $foo : min($job['date_paid'], $foo);
unset($foo);
echo '" max="', idate('Y') + 5, '-12-31"';
if (isset($job['date_paid'])) echo " value=\"{$job['date_paid']}\"";
if ($read_only) echo ' readonly';
?> onchange="weekday(document.getElementById('pday<?php echo $jsuf; ?>'), this.value)" />
            <span style="font-style: italic;" id="pday<?php echo $jsuf; ?>"><?php
if (isset($job['paidday'])) echo "({$job['paidday']})";
elseif (isset($job['dateday']))
  echo '(e.g. &lsquo;today&rsquo;, &lsquo;', date('j F'), '&rsquo;, &lsquo;31/12/', idate('Y'), '&rsquo;)';
?></span>
          </td>
	</tr>

        <tr>
	  <td><label for="fee<?php echo $jsuf; ?>">Fee:</label></td>
	  <td align="right">&pound;</td>
          <td>
	    <input type="number" class="numeric" id="fee<?php echo $jsuf; ?>" name="jobs[<?php
echo $job_id; ?>][fee]" size="9" step="0.01" placeholder="0.00"<?php
if (isset($job['fee'])) echo ' value="', $job['fee'], '"';
if ($read_only) echo ' readonly';
?> />
	    <input type="submit" class="offscreen" tabindex="-1" />
	  </td>
	</tr>

<?php

if (! defined('PLAIN_FORM')) {

  // get realcontact_id from job
  if (empty($job['realcontact_id'])) $job_contacts = array();
  else $job_contacts = array($job['realcontact_id']);
  $nextclient = $job['client_id'];
  $explanation = FALSE;

  // failing that, get realcontact_ids from client or nearest ancestor
  while (empty($job_contacts) and $nextclient) {
    $result = $db_connection->query("SELECT realcontact_id FROM metacontacts WHERE " .
	"user_id=$user_id AND business_id=$business_id AND client_id=$nextclient");
    if ($result === FALSE) break;
    while (($row = $result->fetch_row()) !== NULL)
      $job_contacts[] = $row[0];
    $result->free();

    $result = $db_connection->query("SELECT client_name,parent_id FROM clients WHERE " .
	"user_id=$user_id AND business_id=$business_id AND client_id=$nextclient");
    if ($result === FALSE) break;
    if (($row = $result->fetch_assoc()) === NULL) break;
    $explanation = htmlify(truncate($row['client_name']));
    $nextclient = $row['parent_id'];
  }

?>

        <tr>
	  <td colspan="2">Contact:</td>
          <td>
<?php
  if (!empty($job_contacts) and $explanation)
    echo "This job has no contact, but the client &lsquo;$explanation&rsquo; has these:\n";
?>
            <div style="display: table; border-spacing: 0;">
              <div style="display: table-row;">
                <div style="display: table-cell; vertical-align: middle;">
<?php

  $notfirst = FALSE;
  foreach ($job_contacts as $job_rc_id) {
    $result = $db_connection->query("SELECT CONCAT_WS(' ',forenames,surname) AS fullname FROM realcontacts " .
	"WHERE user_id=$user_id AND realcontact_id=$job_rc_id");
    if ($result === FALSE) continue;
    if (($row = $result->fetch_row()) === NULL) continue;
    if ($notfirst) echo "<br />\n";
    echo "	          <a href=\"contact.php?user_id=$user_id&amp;realcontact_id=$job_rc_id\">", htmlify(truncate($row[0])), "</a>";
    $notfirst = TRUE;
  }
  if (!empty($job_contacts)) echo "\n";

?>
                </div>

<?php if (empty($job['date']) or strcmp($job['date'], FINALDATE) >= 0) { ?>
                <div style="display: table-cell; vertical-align: middle;<?php if (!empty($job_contacts)) echo ' padding-left: 0.5em;'; ?>">
                  <input type="submit" class="button" name="btn_job_con_chg" value="<?php
  echo empty($job_contacts) ? 'Choose' : ($explanation ? 'Override' : 'Change'); ?>" />
                  <input type="submit" class="button" name="btn_job_con_new" value="New contact" />
                </div>
<?php } ?>
              </div>
            </div>
	  </td>
	</tr>

<?php unset($job_contacts, $nextclient, $explanation, $result, $row, $job_rc_id); ?>

        <tr>
	  <td colspan="2" valign="top"><label for="expenses<?php echo $jsuf; ?>">Related expenses:</label></td>
          <td valign="top">
            <div style="display: table; border-spacing: 0;">
              <div style="display: table-row;">
                <div style="display: table-cell; vertical-align:top;">
<?php

  $je_conditions['user_id'] = $user_id;
  $je_conditions['business_id'] = $business_id;
  $je_conditions['job_id'] = $job_id;
  draw_related('expenses INNER JOIN jobs USING (user_id,business_id,job_id)',
    $je_conditions, 'expense_id', 'expense_title',
    "expenses related to the job &lsquo;{$job['job_title']}&rsquo;",
    9, "expenses$jsuf", "jobs[$job_id][expenses]",
    "job_enbt_exp$jsuf()"
  );
  unset($je_conditions);

?>
                </div>

                <div style="display: table-cell; vertical-align:top; text-align: left; padding: 0 0.5em;" onmouseover="job_enbt_exp<?php echo $jsuf; ?>()">
<?php if (empty($job['date']) or strcmp($job['date'], FINALDATE) >= 0) { ?>
                  <input type="submit" class="button" name="btn_job_exp_new" id="btnjobexpnew<?php echo $jsuf; ?>" value="Create new expense" /><br />
		  <input type="submit" class="button" name="btn_job_exp_chg" id="btnjobexpchg<?php echo $jsuf; ?>" value="Link/sever expenses" /><br />
<?php } ?>
		  <input type="submit" class="button" name="btn_job_exp_gto" id="btnjobexpgto<?php echo $jsuf; ?>" value="View expense" />
                </div>
              </div>
            </div>
          </td>
        </tr>
<?php

}

close_xhtml_tag('table');

?>

      <script type="text/javascript">
      // <!-- [CDATA[
	job_enbt_exp<?php echo $jsuf; ?>();
	job_enbt_cli<?php echo $jsuf; ?>();
      // ]] -->
      </script>

      <table>
        <tr>
	  <td valign="top"><label for="notebox<?php echo $jsuf; ?>">Notes:</label>&nbsp;</td>
          <td><textarea id="notebox<?php echo $jsuf; ?>" name="jobs[<?php
echo $job_id; ?>][notes]" rows="10" cols="60"<?php
if ($read_only) echo ' readonly'; echo '>';
if (isset($job['notes'])) echo $job['notes']; ?></textarea></td>
	</tr>
      </table>

<?php
if (! $new) {
  require_once './php/forms/system_dates.php';
  print_system_dates($job, $jsuf);
}
?>

      <div class="savebutton">
	<script type="text/javascript" src="javascript/<?php echo empty($could_be_blank) ? 'cancel' : 'unjob'; ?>.js"></script>

<?php if (empty($job['date']) or strcmp($job['date'], FINALDATE) >= 0) { ?>
        <input type="submit" name="btn_save" value="<?php echo $new ? 'Create' : 'Save'; ?>" />
<?php } ?>
      </div>
<?php
echo $delete_button;
close_xhtml_tag('fieldset');
