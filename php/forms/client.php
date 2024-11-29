<?php

require_once BASEURL . 'php/forms/client_menu.php';
$clsuf = alphanum_counter();

?>
    <fieldset class="main" style="margin-top: 1em; padding-bottom: 5px;">
      <legend><?php echo $client['client_name']; ?></legend>

      <h3 style="margin-top: 0;">Client</h3>

      <input type="hidden" name="clients[<?php echo $client_id; ?>][user_id]" value="<?php echo $user_id; ?>" />
      <input type="hidden" name="clients[<?php echo $client_id; ?>][business_id]" value="<?php echo $business_id; ?>" />
<?php if ($new) { ?>
      <input type="hidden" name="clients[<?php echo $client_id; ?>][guid]" value="<?php echo mt_rand(); ?>" />
<?php } ?>

      <script type="text/javascript">
      // <!-- [CDATA[
        function copyabbrev() {
          if (document.getElementById("abbr<?php echo $clsuf; ?>").value == "" &&
	      document.getElementById("name<?php echo $clsuf; ?>").value.length <= 6) {
            document.getElementById("abbr<?php echo $clsuf; ?>").value
	      = document.getElementById("name<?php echo $clsuf; ?>").value;
          }
        }
      // ]] -->
      </script>

      <script type="text/javascript">
      // <!-- [CDATA[
        function copyabbrev() {
          if (document.getElementById("abbr<?php echo $clsuf; ?>").value == "" &&
	      document.getElementById("name<?php echo $clsuf; ?>").value.length <= 6) {
            document.getElementById("abbr<?php echo $clsuf; ?>").value
	      = document.getElementById("name<?php echo $clsuf; ?>").value;
          }
        }
      // ]] -->
      </script>

      <table style="width: 100%; padding-right: 0;">
        <tr>
    	  <td><label for="name<?php echo $clsuf; ?>">Name:</label></td>
    	  <td colspan="2"><input type="text" id="name<?php echo $clsuf;
?>" name="clients[<?php echo $client_id; ?>][client_name]" size="40" required="required" maxlength="85"<?php
if (! $new) echo ' value="', $client['client_name'], '"'; ?> onchange="copyabbrev()" /></td>
        </tr>

        <tr>
    	  <td><label for="abbr<?php echo $clsuf; ?>">Abbreviation:</label></td>
    	  <td colspan="2"><input type="text" id="abbr<?php echo $clsuf;
?>" name="clients[<?php echo $client_id; ?>][abbrev]" size="5" required="required" maxlength="6"<?php
if (! $new) echo ' value="', $client['abbrev'], '"'; ?> /> (required)</td>
    	</tr>

        <tr>
     	  <td><label for="parent_id<?php echo $clsuf; ?>">File under:</label></td>
          <td colspan="2">
<?php

array_push($open_xhtml_tags, 'fieldset', 'table', 'tr', 'td');

// see the end of /php/actions/clients.php
if (isset($new_client_exclude) and array_key_exists($client_id, $new_client_exclude))
  $exclude = $new_client_exclude[$client_id];
else
  $exclude = 'bedrock';
draw_client_dropdown("parent_id$clsuf", "clients[$client_id][parent_id]", $client['parent_id'], $new ? $exclude : $client_id, 6, $client_id);

if ($new) echo "            (leave blank for none)\n";

close_xhtml_tag('tr');

?>

        <tr>
    	  <td valign="top"><label for="notebox<?php echo $clsuf; ?>">Notes:</label></td>
          <td><textarea id="notebox<?php echo $clsuf;
?>" name="clients[<?php echo $client_id; ?>][notes]" rows="8" cols="60"><?php
if (isset($client['notes'])) echo $client['notes']; ?></textarea></td>
    	  <td valign="bottom" align="right">
	    <script type="text/javascript" src="javascript/cancel.js"></script>
    
	    <input type="submit" name="btn_save" value="<?php echo $new ? 'Create' : 'Save All'; ?>" />
	  </td>
    	</tr>
<?php close_xhtml_tag('table');
