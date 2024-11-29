<?php

$bsuf = alphanum_counter();

$default_bname = '';
if ($new) do {
  $result = $db_connection->query("SELECT COUNT(*) FROM businesses WHERE user_id=$user_id");
  if (! $result) break;
  if (! $row = $result->fetch_row()) break;
  if ($row[0] == '0') $default_bname = $user_name;
  $result->free();
  unset($row, $result);
} while (false);

$open_xhtml_tags[] = 'fieldset';

?>
    <fieldset class="main">

      <input type="hidden" name="businesses[<?php echo $business_id; ?>][user_id]" value="<?php echo $user_id; ?>" />
<?php if ($new) { ?>
      <input type="hidden" name="businesses[<?php echo $business_id; ?>][guid]" value="<?php echo mt_rand(); ?>" />
<?php } ?>

      <table>
        <tr>
	  <td><label for="box1_<?php echo $bsuf; ?>">Business name</label></td>
	  <td><input type="text" id="box1_<?php echo $bsuf; ?>" name="businesses[<?php
echo $business_id; ?>][business_name]" size="40" required="required" maxlength="85"<?php
echo $new ? " value=\"$default_bname\"" : " value=\"{$business['business_name']}\""; ?> /></td>
	</tr>

        <tr>
	  <td valign="top"><label for="box2_<?php echo $bsuf; ?>">Description of business</label></td>
	  <td><textarea id="box2_<?php echo $bsuf; ?>" name="businesses[<?php
echo $business_id; ?>][descr]" rows="3" cols="40"><?php
if (isset($business['descr'])) echo $business['descr']; ?></textarea></td>
	</tr>

        <tr>
	  <td valign="top"><label for="box3_<?php echo $bsuf; ?>">Business address</label></td>
          <td><textarea id="box3_<?php echo $bsuf; ?>" name="businesses[<?php
echo $business_id; ?>][address]" rows="2" cols="40"><?php
if (isset($business['address'])) echo $business['address']; ?></textarea></td>
	</tr>

        <tr>
	  <td><label for="box4_<?php echo $bsuf; ?>">Postcode</label></td>
	  <td><input type="text" id="box4_<?php echo $bsuf; ?>" name="businesses[<?php
echo $business_id; ?>][postcode]" size="8" maxlength="8"<?php
if (isset($business['postcode'])) echo ' value="', $business['postcode'], '"'; ?> /></td>
	</tr>
      </table>

      <table>
        <tr>
	  <td>
	    <label for="box5_<?php echo $bsuf; ?>">Accounts start on the</label>
	    <select id="box5_<?php echo $bsuf; ?>" name="businesses[<?php echo $business_id; ?>][bookday]">
<?php
array_push($open_xhtml_tags, 'table', 'tr', 'td', 'select');
if (empty($business['bookday'])) $business['bookday'] = 6;
for ($j=1; $j<29; $j++) {
  echo '              <option';
  if ($business['bookday'] == $j) echo ' selected="selected"';
  echo ' value="'. $j, '" label="'. $j;
  $suffix = 'th';
  if ($j != 11 and $j % 10 == 1) $suffix = 'st';
  if ($j != 12 and $j % 10 == 2) $suffix = 'nd';
  if ($j != 13 and $j % 10 == 3) $suffix = 'rd';
  echo "$suffix\" />\n";
}
unset($j, $suffix);
close_xhtml_tag('select');
echo "	    day of the month (1st or 6th recommended)\n";
close_xhtml_tag('table');
?>

      <table>
        <tr>
          <td>
            <label for="box6_<?php echo $bsuf; ?>">Accounts run to the </label>
            <select id="box6_<?php echo $bsuf; ?>" name="businesses[<?php echo $business_id; ?>][book_day]">
<?php
array_push($open_xhtml_tags, 'table', 'tr', 'td', 'select');
if (empty($business['book_day'])) $business['book_day'] = 5;
for ($j=1; $j<32; $j++) {
  echo '              <option';
  if ($business['book_day'] == $j) echo ' selected="selected"';
  echo ' value="'. $j, '" label="'. $j;
  $suffix = 'th';
  if ($j != 11 and $j % 10 == 1) $suffix = 'st';
  if ($j != 12 and $j % 10 == 2) $suffix = 'nd';
  if ($j != 13 and $j % 10 == 3) $suffix = 'rd';
  echo "$suffix\" />\n";
}
unset($j, $suffix);
?>
            </select>
	    of
            <select name="businesses[<?php echo $business_id; ?>][book_month]">
<?php
if (empty($business['book_month'])) $business['book_month'] = 4;
for ($j=1; $j<13; $j++) {
  echo '              <option';
  if ($business['book_month'] == $j) echo ' selected="selected"';
  echo ' value="'. $j, '" label="'. date('F', 945369900 + $j * 2629800);
  echo "\" />\n";
}
unset($j);
close_xhtml_tag('select');
?>
            each year (common values are 5 April, 31 March and 31 December)
<?php
close_xhtml_tag('table');
?>

      <table>
        <tr>
	  <td><label for="box7_<?php echo $bsuf; ?>">Business start date (if after 5 April <?php
echo date('Y', time()-34214400); ?>)</label></td>
          <td>
            <input type="date" id="box7_<?php echo $bsuf; ?>" name="businesses[<?php
echo $business_id; ?>][birth]" size="14" max="<?php
echo idate('Y') + 1, '-04-05"';
if (isset($business['birth'])) echo " value=\"{$business['birth']}\"";
?> onchange="weekday(document.getElementById('bday<?php echo $bsuf; ?>'), this.value)" />
            <span style="font-style: italic;" id="bday<?php echo $bsuf; ?>"><?php
if (isset($business['birth_weekday'])) echo "({$business['birth_weekday']})";
else echo '(e.g. &lsquo;today&rsquo;, &lsquo;', date('j F'), '&rsquo;, &lsquo;31/12/', idate('Y'), '&rsquo;)';
?></span>
	  </td>
	</tr>

        <tr>
	  <td><label for="box8_<?php echo $bsuf; ?>">Final date of trading (if ceased)</label></td>
          <td>
            <input type="date" id="box8_<?php echo $bsuf; ?>" name="businesses[<?php
echo $business_id; ?>][death]" size="14" max="<?php
echo idate('Y') + 1, '-04-05"';
if (isset($business['death'])) echo " value=\"{$business['death']}\"";
?> onchange="weekday(document.getElementById('dday<?php echo $bsuf; ?>'), this.value)" />
            <span style="font-style: italic;" id="dday<?php echo $bsuf; ?>"><?php
if (isset($business['death_weekday'])) echo "({$business['death_weekday']})";
elseif (isset($business['birth_weekday']))
  echo '(e.g. &lsquo;today&rsquo;, &lsquo;', date('j F'), '&rsquo;, &lsquo;31/12/', idate('Y'), '&rsquo;)';
?></span>
	  </td>
	</tr>
      </table>

      <div class="savebutton">
	<script type="text/javascript" src="javascript/cancel.js"></script>

        <input type="submit" name="btn_save" value="<?php echo $new ? 'Create' : 'Save'; ?>" />
      </div>
<?php
close_xhtml_tag('fieldset');