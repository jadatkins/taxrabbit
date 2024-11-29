<?php

$esuf = alphanum_counter();

if (array_key_exists('cat_code', $expense)) {
  $result = $db_connection->query('SELECT * FROM cats WHERE cat_code=' . $expense['cat_code']);
  if (! $result or !($row = $result->fetch_assoc()) )
    trigger_error("I'm having trouble getting the category details from the " .
      "database.&nbsp; The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);
  $descr_id = $row['descr_id'];
  $disallow = ! $row['allowable'];
  $result->free();
  unset($result, $row);
}
else {$descr_id = NULL; $disallow = FALSE;}

?>
    <script type="text/javascript">
    // <!-- [CDATA[
      function exp_enab_tick<?php echo $esuf; ?>() {
	if (document.getElementById("caty<?php echo $esuf; ?>").selectedIndex == 0 ||
	    document.getElementById("caty<?php echo $esuf; ?>").selectedIndex == 15) {
	  if (savedallow<?php echo $esuf; ?> == null)
	    {savedallow<?php echo $esuf; ?> = document.getElementById("tick<?php echo $esuf; ?>").checked;}
	  document.getElementById("tick<?php echo $esuf; ?>").checked=false;
	  document.getElementById("tick<?php echo $esuf; ?>").disabled=true;
	} else {
	  document.getElementById("tick<?php echo $esuf; ?>").disabled=false;
	  if (savedallow<?php echo $esuf; ?> != null)
	    {document.getElementById("tick<?php echo $esuf; ?>").checked=savedallow<?php echo $esuf; ?>;}
	  savedallow<?php echo $esuf; ?> = null;
	}
      }
      function exp_enab_mile<?php echo $esuf; ?>() {
	exp_enab_tick<?php echo $esuf; ?>();
	if (document.getElementById("caty<?php echo $esuf; ?>").selectedIndex == 4) {
	  document.getElementById("mile<?php echo $esuf; ?>").disabled=false;
	  if (savedmileage<?php echo $esuf; ?>)
	    {document.getElementById("mile<?php echo $esuf; ?>").value = savedmileage<?php echo $esuf; ?>;}
	  savedmileage<?php echo $esuf; ?> = null;
	} else {
	  if (savedmileage<?php echo $esuf; ?> == null)
	    {savedmileage<?php echo $esuf; ?> = document.getElementById("mile<?php echo $esuf; ?>").value;}
	  document.getElementById("mile<?php echo $esuf; ?>").value='';
	  document.getElementById("mile<?php echo $esuf; ?>").disabled=true;
	}
      }
    // ]] -->
    </script>

    <fieldset class="main">
      <legend><?php echo $expense['expense_title']; ?></legend>

      <input type="hidden" name="expenses[<?php echo $expense_id; ?>][user_id]" value="<?php echo $user_id; ?>" />
      <input type="hidden" name="expenses[<?php echo $expense_id; ?>][business_id]" value="<?php echo $business_id; ?>" />
<?php if ($new) { ?>
      <input type="hidden" name="expenses[<?php echo $expense_id; ?>][guid]" value="<?php echo mt_rand(); ?>" />
<?php } elseif (empty($expense['date']) or strcmp($expense['date'], FINALDATE) >= 0) { ?>

      <div class="deletebutton">
        <input type="submit" name="btn_delete[expense<?php
echo $user_id, '-', $business_id, '-', $expense_id; ?>]" value="Delete" />
      </div>
<?php } else { ?>
      <input type="hidden" name="expenses[<?php echo $expense_id; ?>][readonly]" value="1" />

      <p>You cannot modify this expense because it is too far in the past.&nbsp;
         Any changes you make below will not be saved.</p>
<?php } ?>

      <table>
        <tr>
	  <td><label for="title<?php echo $esuf; ?>">Expense title / reference:</label></td>
          <td><input type="text" id="title<?php echo $esuf;
?>" name="expenses[<?php echo $expense_id; ?>][expense_title]" size="40"<?php
if (empty($could_be_blank)) echo ' required="required"'; ?> maxlength="85"<?php
if (! $new) echo ' value="', $expense['expense_title'], '"'; ?> /></td>
	</tr>

        <tr>
	  <td valign="top"><label for="caty<?php echo $esuf; ?>">Category:</label></td>
          <td>
	    <select id="caty<?php echo $esuf; ?>" name="expenses[<?php
echo $expense_id; ?>][category]" onchange="exp_enab_mile<?php echo $esuf; ?>()">
            <option<?php if ($descr_id === NULL) echo ' selected="selected"'; ?> value=""></option>
<?php

$result = $db_connection->query('SELECT * FROM cat_descriptions');
if (! $result) {
  ?>
            </select>
	  </td>
	</tr>
      </table>
    </fieldset>
<?php
  trigger_error("I couldn't retrieve the list of category descriptions from the " .
    "database.&nbsp; The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);
}
echo '            <optgroup label="Business expenses">', "\n";
while ($cat_descr = $result->fetch_assoc()) {
  if ($cat_descr['descr_id'] == 14) {
    echo '            </optgroup>', "\n";
    echo '            <optgroup label="Capital allowances">', "\n";
  }
  echo '              <option';
  if ($descr_id == $cat_descr['descr_id']) echo ' selected="selected"';
  echo ' value="', $cat_descr['descr_id'], '">', $cat_descr['descr_text'], "</option>\n";
}
echo '            </optgroup>', "\n";
$result->free();
unset($result, $cat_descr, $descr_id);

?>
            </select>
            <a href="key_to_expenses.php" target="_blank">What do these mean?</a><br />
	    <input type="checkbox" id="tick<?php echo $esuf; ?>" name="expenses[<?php
echo $expense_id; ?>][disallowable]"<?php if ($disallow) echo ' checked="checked"';
unset($disallow); ?> /><label for="tick<?php echo $esuf; ?>"> Disallowable (Personal)</label>
	  </td>
	</tr>

        <tr>
          <td><label for="date<?php echo $esuf; ?>">Date:</label></td>
          <td>
            <input type="date" id="date<?php echo $esuf; ?>" name="expenses[<?php
echo $expense_id; ?>][date]" size="14" min="<?php
$firstdate = date('Y-m-d', mktime(9, 0, 0, substr(FINALDATE,5,2), substr(FINALDATE,8,2)+1, substr(FINALDATE,0,4)));
echo empty($expense['date']) ? $firstdate : min($expense['date'], $firstdate);
echo '" max="', idate('Y') + 5, substr(BOOKDATE, 4), '"';
if (! empty($expense['date'])) echo " value=\"{$expense['date']}\"";
elseif (isset($related_expense_default_date)) echo  " value=\"$related_expense_default_date\"";
?> onchange="weekday(document.getElementById('wday<?php echo $esuf; ?>'), this.value)" />
            <span style="font-style: italic;" id="wday<?php echo $esuf; ?>"><?php
if (isset($expense['weekday'])) echo "({$expense['weekday']})";
elseif (isset($related_expense_default_date)) echo '(copied over from the job)';
else echo '(e.g. &lsquo;today&rsquo;, &lsquo;', date('j F'), '&rsquo;, &lsquo;31/12/', idate('Y'), '&rsquo;)';
?></span>
          </td>
        </tr>

        <tr>
          <td colspan="2" style="padding: 0; text-align: center;">
            <div style="display: inline-block; text-align: left;">
              <fieldset style="margin: 0.25em 0;"><table>
                <tr>
                  <td><label for="cost<?php echo $esuf; ?>">Cost:</label></td>
                  <td align="right">&pound;</td>
                  <td><input type="number" class="numeric" id="cost<?php
echo $esuf; ?>" name="expenses[<?php echo $expense_id; ?>][cost]" size="9" step="0.01" placeholder="0.00"<?php
if (isset($expense['cost'])) echo ' value="', $expense['cost'], '"';
?> /></td>
                </tr>

                <tr>
                  <td colspan="3" align="center"><span
                      style="font-weight: bold;">&mdash;&mdash; OR &mdash;&mdash;</span></td>
                </tr>

                <tr>
                  <td colspan="2"><label for="mile<?php echo $esuf; ?>">Distance driven:</label></td>
                  <td><input type="number" class="numeric" id="mile<?php
echo $esuf; ?>" name="expenses[<?php echo $expense_id; ?>][mileage]" size="9" placeholder="0"<?php
if (isset($expense['mileage'])) echo ' value="', $expense['mileage'], '"';
?> /> miles</td>
                </tr>
              </table></fieldset>
              <input type="submit" class="offscreen" />
            </div>
          </td>
	</tr>

<?php if (!defined('PLAIN_FORM')) { ?>
        <tr>
	  <td>Related income:</td>
          <td>
<?php
  if (empty($expense['job_id']) or ! $new) $flexijob = TRUE; else $flexijob = FALSE;

  do {
    if (empty($expense['job_id']))
      {echo "            <em>(none)</em>\n"; break;}
    if (!defined('AUTHUID')) break;
  
    $result = $db_connection->query("SELECT job_title FROM jobs WHERE " .
	"user_id=$user_id AND business_id=$business_id AND job_id={$expense['job_id']}");
    if ($result === FALSE) break;
    if (($row = $result->fetch_row()) === NULL) break;
    echo '            ';
    if ($flexijob) echo "<a href=\"commission.php?user_id={$expense['user_id']}&amp;business_id={$expense['business_id']}&amp;job_id={$expense['job_id']}&amp;return=1\">";
    echo htmlify(truncate($row[0]));
    if ($flexijob) echo '</a>';
    echo "\n";
  }
  while (FALSE);

  if ($flexijob and (empty($expense['date']) or strcmp($expense['date'], FINALDATE) >= 0)) {
?>
            <input type="submit" class="button" name="btn_expense_chg" value="<?php
echo empty($expense['job_id']) ? 'Find job' : 'Change'; ?>" />
            <input type="submit" class="button" name="btn_expense_new" value="New job" />
	  </td>
	</tr>
<?php
  }
  unset($flexijob);
}
?>
      </table>

      <script type="text/javascript">
      // <!-- [CDATA[
        savedallow<?php echo $esuf; ?> = null;
        savedmileage<?php echo $esuf; ?> = null;
        if (document.getElementById("caty<?php echo $esuf; ?>").selectedIndex == 0 ||
            document.getElementById("caty<?php echo $esuf; ?>").selectedIndex == 15)
          {document.getElementById("tick<?php echo $esuf; ?>").disabled=true;}
        if (document.getElementById("caty<?php echo $esuf; ?>").selectedIndex != 4)
          {document.getElementById("mile<?php echo $esuf; ?>").disabled=true;}
      // ]] -->
      </script>

      <table>
        <tr>
	  <td valign="top"><label for="notebox<?php echo $esuf; ?>">Notes:</label>&nbsp;</td>
          <td><textarea id="notebox<?php echo $esuf; ?>" name="expenses[<?php
echo $expense_id; ?>][notes]" rows="10" cols="60"><?php
if (isset($expense['notes'])) echo $expense['notes']; ?></textarea></td>
	</tr>
      </table>

      <div class="savebutton">
	<script type="text/javascript" src="javascript/<?php echo empty($could_be_blank) ? 'cancel' : 'unexpense'; ?>.js"></script>

<?php if (empty($expense['date']) or strcmp($expense['date'], FINALDATE) >= 0) { ?>
        <input type="submit" name="btn_save" value="<?php echo $new ? 'Create' : 'Save'; ?>" />
<?php } ?>
      </div>
    </fieldset>
