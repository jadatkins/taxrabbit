<?php

set_error_handler('dummy_handler');
close_xhtml_tag('head');

$exists_msg = FALSE;
if (! defined('SUPPRESS_YBOX') and isset($db_connection) and $result
    = $db_connection->query('SELECT COUNT(*) FROM messages WHERE user_id='.AUTHUID)) {
  $row = $result->fetch_row();
  if ($row and ! empty($row[0]))
    $exists_msg = $row[0];
  unset($row);
  $result->free();
}
unset($result);

echo "\n<body", ($exists_msg ? ' onload="setTimeout(hide_messages, '
                  . (40 + 20 * $exists_msg) . '00)"' : ''), ">\n\n";
$open_xhtml_tags[] = 'body';


if (! defined('WIDE_PAGE')) echo "<div class=\"title\">\n  ";
echo '<h1>TaxRabbit<sup>&reg;</sup> <img src="images/beta.png" alt="beta"
     style="margin: -8px 0; width: 36px; height: 36px;" /></h1>';
if (! defined('WIDE_PAGE')) echo "\n</div>";
echo "\n\n";

if ($exists_msg and $result
    = $db_connection->query('SELECT msg FROM messages WHERE user_id='.AUTHUID)) {
  if ($row = $result->fetch_row()) {
    echo '<div id="yellow_msg_box"><div id="yelmsg_inner">
  ';
    do {
      $msgs[] = htmlify($row[0]);
    } while ($row = $result->fetch_row());
    echo implode("<br />\n  ", $msgs);
    ?>

</div></div>

<script type="text/javascript">
// <!-- [CDATA[
  document.getElementById('yellow_msg_box').style.visibility='visible';
// ]] -->
</script>

<?php
    unset($msgs);
  }
  unset($row);
  $result->free();
}
unset($result);

if ($exists_msg and
    $result = $db_connection->query('SELECT EXISTS(SELECT * FROM '
                        . 'messages WHERE user_id != '.AUTHUID.')')
    and $row = $result->fetch_row() and ! $row[0]) {
  $db_connection->query('TRUNCATE TABLE messages');
} else {
  if (! defined('SUPPRESS_YBOX') and isset($db_connection))
    $db_connection->query('DELETE FROM messages WHERE user_id='.AUTHUID);
}

if (! defined('WIDE_PAGE')) {
  echo "<div class=\"content\">\n";
  $open_xhtml_tags[] = 'div';
}

if (count($headings))
  echo '<h2>', htmlify(end($headings)), "</h2>\n\n";

unset($row, $result, $muid, $exists_msg);
define('HEAD_INCLUDED', true);
restore_error_handler();
