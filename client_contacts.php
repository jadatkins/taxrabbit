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
require_once './php/forms/join.php';
$saveall = TRUE;
require './php/identify/client.php';
$headings[] = html_entity_decode($client['client_name'], ENT_COMPAT, 'UTF-8');
$urls[] = "client.php$unique_id&amp;client_id=$client_id";
$headings[] = "Contacts";

require_once './php/header_one.php';
?>
  <style type="text/css">
  /* <![CDATA[ */
    ul { list-style-type: none; }
  /* ]]> */
  </style>
<?php
require_once './php/header_two.php';

require_once './php/forms/contact_ticks.php';

?>
  <p>Ticked contacts will be linked with the client &lsquo;<?php echo $client['client_name']; ?>&rsquo;:</p>

  <form accept-charset="UTF-8" name="frm_main" method="post" action="link_contacts.php" onsubmit="disable()">

<?php insert_hidden_fields("client.php$unique_id&amp;client_id=$client_id"); ?>
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
    <input type="hidden" name="business_id" value="<?php echo $business_id; ?>" />
    <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
    <input type="hidden" name="client_name" value="<?php echo $client['client_name']; ?>" />

    <fieldset class="main">
<?php draw_contact_ticks(3); ?>

      <div class="savebutton">
    	<script type="text/javascript">
        // <!-- [CDATA[
          insertcancel();
        // ]] -->
        </script>

        <input type="submit" name="btn_save" value="Save" />
      </div>
    </fieldset>
  </form>
<?php include './php/footer.php';
