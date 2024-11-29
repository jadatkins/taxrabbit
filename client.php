<?php

require_once './php/essentials.php';
include_once './php/nocache.php';
require_once './php/identify/user.php';
$headings = array($user['user_name']);
$navbox = TRUE;
$urls = array("user.php?user_id=$user_id");
require_once './php/identify/business.php';
array_push($headings, $business['business_name'], 'Clients');
array_push($urls, "business.php$unique_id", "clients.php$unique_id");
$saveall = TRUE;
require './php/identify/client.php';
$headings[] = html_entity_decode($client['client_name'], ENT_COMPAT, 'UTF-8');
require_once './php/header_one.php';
require_once './php/header_two.php';
require_once './php/forms/join.php';

?>
  <h3>Jobs</h3>

  <p><a href="find_in.php<?php echo "$unique_id&amp;clients%5B%5D={$_GET['client_id']}";
            ?>&amp;nodate=on&amp;sort%5B%5D=date+ASC"
      >Search for jobs with this client</a> (does not include sub-clients)</p>

  <form accept-charset="UTF-8" name="frm_main" method="post" action="action.php" onsubmit="disable()">

<?php

$open_xhtml_tags[] = 'form';
insert_hidden_fields(goto_address("clients.php$unique_id", array('contact.php', 'commission.php')));
require './php/forms/client.php';

?>

    <h3>Contacts</h3>

    <p class="button" style="padding-bottom: 1px;">
      <input type="submit" name="btn_client_new" value="Create new contact" />
      <input type="submit" name="btn_client_chg" value="Link contacts to <?php if ($new) echo 'this Client'; else echo $client['client_name']; ?>" />
    </p>

<?php

if (! $new) {
  $badger = $db_connection->query('SELECT realcontact_id FROM metacontacts ' .
    "WHERE user_id={$_GET['user_id']} AND business_id=" . $_GET['business_id']
    . " AND client_id={$_GET['client_id']}");
  if (! $badger) {
    echo "  </form>\n";
    trigger_error("I was just trying to retrieve the contact details" .
      " for " . $client['client_name'] .
      ", and MySQL threw up the following error:<br />\n     "
      . $db_connection->error, E_USER_ERROR);
  }

  while ($row = $badger->fetch_row()) {
    $realcontact_id = $row[0];
    require './php/identify/realcontact.php';
    require './php/forms/contact.php';
  }
  $badger->free();
}

close_xhtml_tag('form');

include './php/footer.php';
