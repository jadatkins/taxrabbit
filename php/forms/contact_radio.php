<?php

/* This file contains the functions to draw a list of contact radio buttons,
 * for specifying which contact is linked to a particular job.
 */

function draw_contact_radio($indent_width = 0) {
  global $db_connection, $user_id, $user_name, $job, $job_id;

  $result = $db_connection->query("SELECT realcontact_id,CONCAT_WS(' ',forenames,surname)"
  . " AS fullname FROM realcontacts WHERE user_id=$user_id ORDER BY surname,forenames");
  if (! $result) {
    echo '    </fieldset>
  </form>

';
    trigger_error("I got stuck while trying to read the address book for " . $user_name
      . ".&nbsp; The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);
  }

  if (! array_key_exists('realcontact_id', $job)) $job['realcontact_id'] = null;
  html_indent($indent_width);
  echo '<input type="radio" name="jobs[', $job_id, '][realcontact_id]" value=""';
  if (empty($job['realcontact_id'])) echo ' checked="checked';
  echo ' id="radionull" /> <label for="radionull">None</label>', "\n";
  html_indent($indent_width); echo "<hr />\n";

  if ($row = $result->fetch_assoc()) {
    html_indent($indent_width); echo "<ul style=\"padding-left: 0;\">\n";
    do {
      html_indent($indent_width+1);
      echo '<li><input type="radio" name="jobs[', $job_id, '][realcontact_id]" value="', $row['realcontact_id'];
      if ($row['realcontact_id'] == $job['realcontact_id']) echo '" checked="checked';
      echo '" id="radio', $row['realcontact_id'], '" />';
      echo '<label for="radio', $row['realcontact_id'], '"> ';
      echo htmlify(truncate($row['fullname'])), "</label></li>\n";
    } while ($row = $result->fetch_assoc());
    html_indent($indent_width); echo "</ul>\n";
  }
  else {
    html_indent($indent_width);
    echo "<p>You have no contacts.</p>";
  }
  $result->free();
}
