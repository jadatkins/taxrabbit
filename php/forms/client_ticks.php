<?php

/* This file contains the functions to draw a list of client tick-boxes,
 * for specifying which clients a realcontact is associated with.
 */

function draw_client_ticks($context, $indent_width = 0) {
  global $db_connection, $user_id, $business_id, $realcontact, $realcontact_id, $metacontacts;

  if ($context) {
    $result = $db_connection->query("SELECT client_id FROM metacontacts WHERE"
      . " user_id=$user_id AND business_id=$business_id AND realcontact_id=$realcontact_id");
    if (! $result) {
      echo '    </fieldset>
  </form>

';
      trigger_error("I have encountered a problem while looking up the clients associated"
        . " with {$realcontact['fullname']}.&nbsp; The error message is:<br />\n     "
        . $db_connection->error, E_USER_ERROR);
    }
    $metacontacts = array();
    while ($row = $result->fetch_row()) {
      $metacontacts[] = $row[0];
    }
    $result->free();
  }

  client_ticks_draw_boxes(NULL, $context, $indent_width, 0);
}

function client_ticks_draw_boxes(
  $parent_id, $context, $indent_width = 0, $recursion_level = 0
) {
  global $db_connection, $user_id, $business_id, $business_name, $metacontacts;
  if ($recursion_level > 249) {return;}

  if ($parent_id === NULL) $parent_clause = ' IS NULL';
  else $parent_clause = "=$parent_id";
  $result = $db_connection->query("SELECT client_id,client_name FROM clients WHERE"
    . " user_id=$user_id AND business_id=$business_id AND parent_id$parent_clause");
  if (! $result) {
    echo str_repeat('</ul>', $recursion_level), '
    </fieldset>
  </form>

';
    trigger_error("I have encountered a problem while retrieving "
      . "the list of clients.&nbsp; The error message is:<br />\n     "
      . $db_connection->error, E_USER_ERROR);
  }

  if ($row = $result->fetch_assoc()) {
    html_indent($indent_width); echo '<ul';
    if (is_null($parent_id)) echo ' style="margin-top: 0; padding-left: 0;"';
    echo ">\n";
    do {
      html_indent($indent_width+1); echo "<li>\n";
      html_indent($indent_width+2);
      echo '<input type="checkbox" name="clients', ($context ? "[$business_id]" : ''), '[]" value="', $row['client_id'];
      if ($context) {
        if (in_array($row['client_id'], $metacontacts)) echo '" checked="checked';
      }
      //else echo '" checked="checked';
      echo '" id="chk', $business_id, '-', $row['client_id'], '" />';
      echo '<label for="chk', $business_id, '-', $row['client_id'], '"> ';
      echo htmlify(truncate($row['client_name'])), "</label>\n";
      client_ticks_draw_boxes(
        $row['client_id'], $context, $indent_width+2, $recursion_level+1
      );
      html_indent($indent_width+1); echo "</li>\n";
    } while ($row = $result->fetch_assoc());
    html_indent($indent_width); echo "</ul>\n";
  }
  elseif (is_null($parent_id)) {
    html_indent($indent_width);
    echo "<p>The business &lsquo;$business_name&rsquo; has no clients.</p>";
  }
  $result->free();
}
