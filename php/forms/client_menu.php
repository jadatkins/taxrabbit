<?php

/* This file contains the functions to draw a drop-down list of clients, for
 * the clients form and the jobs form, to specify what client the client or job
 * belongs to.
 */

function draw_client_dropdown($htmlid, $htmlname, $selected, $exclude_tree,
    $indent_width = 0, $client_id = NULL, $onchange = '', $read_only = FALSE) {
  html_indent($indent_width);   echo "<select id=\"$htmlid\" name=\"$htmlname\"$onchange>\n";
  $open_xhtml_tags[] = 'select';
  html_indent($indent_width+1); echo '<option value=""';
  if ($selected === NULL) echo ' selected="selected"';
  elseif ($read_only) echo ' disabled="disabled"';
  echo '></option>', "\n";
  if (is_string($client_id) and substr($client_id, 0, 4) == 'ncli')
    {$guid_phrase = ((int) substr($client_id, 4)) + 1;}
  else {$guid_phrase = '';}
  if (! $read_only) {
    html_indent($indent_width+1); echo '<option value="ncli', $guid_phrase;
    if ($exclude_tree == 'new') echo '" disabled="disabled';
    echo "\">New client</option>\n";
  }
  client_menu_draw_options(NULL, $selected, $read_only ? 'all' : $exclude_tree, $indent_width+1, 0, ! $read_only);
  html_indent($indent_width);   close_xhtml_tag('select');
}

function client_menu_draw_options(
  $parent_id, $selected, $exclude_root, $htmlindent = 0, $recursion_level = 0, $include_divider = TRUE
) {
  if ($recursion_level > 599) {return;}
  global $db_connection, $user_id, $business_id;
  $any = FALSE;
  if ($parent_id === NULL) $parent_clause = ' IS NULL';
  else $parent_clause = "=$parent_id";
  $result = $db_connection->query("SELECT client_id,client_name FROM clients WHERE"
    . " user_id=$user_id AND business_id=$business_id AND parent_id$parent_clause");
  if (! $result) {
    trigger_error("I have encountered a problem while retrieving "
      . "the list of clients.&nbsp; The error message is:<br />\n     "
      . $db_connection->error, E_USER_ERROR);
  }
  if ($row = $result->fetch_assoc()) {
    if ($include_divider and $parent_id === NULL) {
      // Insert a divider between 'New client' and the list of clients, if there are any:
      html_indent($htmlindent);
      echo '<option value="" disabled="disabled">', str_repeat('&mdash;', 5), "</option>\n";
    }
    do {
      if ($exclude_root == $row['client_id']) $exclude_these = 'all';
      elseif (!empty($exclude_root) and $recursion_level > 498) $exclude_these = 'all';
      else $exclude_these = $exclude_root;

      html_indent($htmlindent);
      echo '<option value="';
      if ($row['client_id'] == $selected) echo $row['client_id'], '" selected="selected';
      elseif ($exclude_these == 'all') echo '" disabled="disabled';
      else echo $row['client_id'];
      echo '">', str_repeat('&nbsp;', $recursion_level * 2),
        htmlify(truncate($row['client_name'])), "</option>\n";

      client_menu_draw_options(
        $row['client_id'], $selected, $exclude_these, $htmlindent, $recursion_level+1
      );
    } while ($row = $result->fetch_assoc());
    $any = TRUE;
  }
  $result->free();
  return $any;
}
