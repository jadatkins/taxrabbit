<?php

$navbox = FALSE;
require_once './php/essentials.php';
include_once './php/nocache.php';
require_once './php/identify/user.php';
$headings = array($user['user_name'], 'Address Book');
$navbox = TRUE;
$urls = array("user.php$unique_id", "contacts.php$unique_id");
require './php/identify/realcontact.php';
$headings[] = html_entity_decode($realcontact['fullname'], ENT_COMPAT, 'UTF-8');
$urls[] = "contact.php$unique_id&amp;realcontact_id=$realcontact_id";
$headings[] = "Clients";

require_once './php/header_one.php';
?>
  <style type="text/css">
  /* <![CDATA[ */
    ul { list-style-type: none; }
  /* ]]> */
  </style>
<?php
require_once './php/header_two.php';

require_once './php/forms/client_ticks.php';

?>
  <p>The contact &lsquo;<?php echo $realcontact['fullname']; ?>&rsquo; will be linked to clients ticked below:</p>

  <form accept-charset="UTF-8" name="frm_main" method="post" action="link_clients.php" onsubmit="disable()">

<?php insert_hidden_fields("contact.php?user_id=$user_id&amp;realcontact_id=$realcontact_id"); ?>
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
    <input type="hidden" name="realcontact_id" value="<?php echo $realcontact_id; ?>" />
    <input type="hidden" name="contact_name" value="<?php echo $realcontact['fullname']; ?>" />

<?php

$busres = $db_connection->query("SELECT business_id,business_name " .
                          "FROM businesses WHERE user_id=$user_id");
if (! $busres)
  trigger_error("I'm having trouble going through your businesses.&nbsp; " .
    "The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);

while ($business = $busres->fetch_assoc()) {
  $business_id = intval($business['business_id']);
  $business_name = htmlify(truncate($business['business_name']));
?>

    <fieldset class="main">
      <legend><?php echo $business_name ?></legend>

<?php draw_client_ticks(TRUE, 3); ?>

      <div class="savebutton">
    	<script type="text/javascript">
        // <!-- [CDATA[
          insertcancel();
        // ]] -->
        </script>

        <input type="submit" name="btn_save" value="Save" />
      </div>
    </fieldset>
<?php
}

$busres->free();
unset($busres);

?>
  </form>
<?php include './php/footer.php';
