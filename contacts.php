<?php

require_once './php/essentials.php';
include_once './php/nocache.php';
require_once './php/identify/user.php';
$headings = array($user['user_name'], 'Address Book');
$navbox = TRUE;
$urls = array("user.php$unique_id");

require_once './php/header_one.php';

?>
  <meta name="viewport" content="width=device-width" />
  <style type="text/css">
  /* <![CDATA[ */
    ul {list-style-type: none; padding-left: 0;}
    h4 {
      position: -webkit-sticky;
      position: -moz-sticky;
      position: -ms-sticky;
      position: -o-sticky;
      position: sticky;
      top: 0;
      z-index: 1;
      margin-bottom: 0;
      background-color: #F9F8EE;
      border-bottom: 1px Black solid;
    }
  /* ]]> */
  </style>
<?php

require_once './php/header_two.php';

?>
  <h3>Contact List</h3>

<?php

do {
  $result = $db_connection->query('SELECT COUNT(*) FROM realcontacts WHERE user_id=' . $user_id);
  if (! $result) break;
  $row = $result->fetch_row();
  if (! $row) break;
  $manycontacts = $row[0] >= 50;
  $result->free();
  unset($row, $result);
} while (false);

$result = $db_connection->query("SELECT realcontact_id, LEFT(surname,1)" .
      " AS letter, CONCAT_WS(' ',forenames,surname) AS fullname FROM" .
      " realcontacts WHERE user_id=$user_id ORDER BY surname,forenames")
  or trigger_error("I seem to have encountered a problem while reading your address " .
  "book.&nbsp; The error message is:<br />\n     " . htmlify($db_connection->error), E_USER_ERROR);
if ($row = $result->fetch_assoc()) {
  $oldletter = mb_strtoupper(null);
  echo "  <p>Click on a contact to view or change the details.</p>\n\n  <div><ul>\n";
  do {
    $newletter = mb_strtoupper($row['letter']);
    if ($manycontacts and $newletter != $oldletter)
      echo "  </ul></div>\n  <div><h4>{$newletter}</h4>\n  <ul>\n";
    echo "    <li><a href=\"contact.php$unique_id&amp;realcontact_id=",
      $row['realcontact_id'], '">', htmlify($row['fullname']), "</a></li>\n";
    $oldletter = $newletter;
  } while ($row = $result->fetch_assoc());
  echo "  </ul></div>\n\n";
} else {
  echo "  <p>Your address book is empty.</p>\n\n";
}
$result->free();

?>
  <h3>New Contact</h3>

  <p>Click on the following link to add a new contact to the address book.</p>

  <p><a href="contact.php<?php echo $unique_id ?>&amp;realcontact_id=ncon">New Contact</a></p>
<?php include './php/footer.php';
