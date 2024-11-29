<?php

function draw_related(
  $db_table, $conditions, $identifier, $name, $err_msg, $indent = 0, $html_id, $html_name, $onchange
) {
  global $db_connection, $existing_related;

  foreach ($conditions as $k => $v) {$sql_conditions[] = "$k='$v'";}
  $result = $db_connection->query("SELECT $identifier,$name FROM $db_table WHERE "
                                  . implode(' AND ', $sql_conditions));

  if (! $result) {
    trigger_error("I seem to have encountered a problem while looking up the list of " .
        $err_msg . ".&nbsp; The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);
  }

  html_indent($indent);
  echo "<select id=\"$html_id\" name=\"$html_name", '[]" size="', max(2, $result->num_rows), "\" onchange=\"$onchange\">\n";

  if (!empty($existing_related)) {
    // see btn_client_new in /php/actions/buttons/client.php
    // and btn_job_con_new in /php/actions/buttons/job.php
    html_indent($indent+1);
    echo '<option value="', $existing_related['key'], '">',
      htmlify($existing_related['name']), "</option>\n";
  }

  while ($row = $result->fetch_assoc()) {
    html_indent($indent+1);
    echo '<option value="', $row[$identifier], '">',
      htmlify($row[$name]), "</option>\n";
  }

  $result->free();

  html_indent($indent);
  echo "</select>\n";
}
