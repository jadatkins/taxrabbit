<?php

$navbox = FALSE;
require_once './php/essentials.php';
include_once './php/nocache.php';
require_once './php/identify/user.php';
require_once './php/forms/join.php';
$headings = array($user['user_name'], 'Address Book');
$navbox = TRUE;
$urls = array("user.php$unique_id", "contacts.php$unique_id");
require './php/identify/realcontact.php';
$headings[] = html_entity_decode($realcontact['fullname'], ENT_COMPAT, 'UTF-8');
require_once './php/header_one.php';
require_once './php/header_two.php';

?>
  <form accept-charset="UTF-8" name="frm_main" method="post" action="action.php" onsubmit="disable()">

<?php

insert_hidden_fields(goto_address("contacts.php$unique_id", array('commission.php', 'find_in.php')));
require './php/forms/contact.php';

?>
  </form>
<?php include './php/footer.php';
