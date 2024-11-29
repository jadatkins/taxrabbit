<?php

/* This file contains the functions to draw a list of contact tick-boxes,
 * for specifying which contacts a client is associated with.
 */

function draw_contact_ticks($indent_width = 0) {
  global $db_connection, $user_id, $business_id, $client, $client_id;

  $result = $db_connection->query("SELECT realcontact_id FROM metacontacts WHERE"
    . " user_id=$user_id AND business_id=$business_id AND client_id=$client_id");
  if (! $result) {
    echo '    </fieldset>
  </form>

';
    trigger_error("I have encountered a problem while looking up the contacts associated"
      . " with {$client['client_name']}.&nbsp; The error message is:<br />\n     "
      . $db_connection->error, E_USER_ERROR);
  }
  $metacontacts = array();
  while ($row = $result->fetch_row()) {
    $metacontacts[] = $row[0];
  }
  $result->free();
  unset($result);

  $result = $db_connection->query("SELECT realcontact_id,CONCAT_WS(' ',forenames,surname)"
  . " AS fullname FROM realcontacts WHERE user_id=$user_id ORDER BY surname,forenames");
  if (! $result) {
    echo '    </fieldset>
  </form>

';
    trigger_error("I have encountered a problem while retrieving "
      . "the list of contacts.&nbsp; The error message is:<br />\n     "
      . $db_connection->error, E_USER_ERROR);
  }

  if ($row = $result->fetch_assoc()) {
    html_indent($indent_width); echo "<ul style=\"margin-top: 0; padding-left: 0;\">\n";
    do {
      html_indent($indent_width+1);
      echo '<li><input type="checkbox" name="realcontacts[]" value="', $row['realcontact_id'];
      if (in_array($row['realcontact_id'], $metacontacts)) echo '" checked="checked';
      echo '" id="chk', $row['realcontact_id'], '" />';
      echo '<label for="chk', $row['realcontact_id'], '"> ';
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
