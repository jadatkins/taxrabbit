<?php

if (isset($db_connection) and $db_connection !== FALSE)
  $db_connection->close();

if (defined('PANIC_MSG') and PANIC_MSG)
  include './include/panic.html';
elseif (defined('HELPCONTACTINFO'))
  include './include/helpcontact.html';
if (defined('INCL_CREDITS'))
  include './include/credits.php';

close_xhtml_tag('body', CLOSE_FIRST_XHTML_TAG, 1);

if (count($headings) >= 2 and $headings[0] == $headings[1]) $samename = 2;
else $samename = 0;

if ($navbox and $curr_heading = reset($headings)) {
  echo '
<div class="nav">
  <a href="./">', APPNAME, '</a>';
  //if ($samename) echo "<br />\n    {$headings[0]}";
  $curr_url = reset($urls);
  do {
    echo "<br />\n  ";
    if ($curr_url) {echo "<a href=\"$curr_url\">";}
    switch ($samename) {
      case 2: $samename --; echo 'User: ', htmlify(truncate($curr_heading, 34)); break;
      case 1: $samename --; echo 'Business: ', htmlify(truncate($curr_heading, 30)); break;
      default: echo htmlify(truncate($curr_heading)); break;
    }
    if ($curr_url) {echo '</a>';}
    $curr_url = next($urls);
  } while ($curr_heading = next($headings));
  echo "\n</div>\n";
}

// close_xhtml_tag(2, CLOSE_FIRST_XHTML_TAG);  // Close all children of <body>

close_xhtml_tag(0);  // Close all XHTML tags
