<?php

if (! defined('PLAIN_FORM') and ! $saveall)
  require_once BASEURL . 'php/forms/join.php';
$cosuf = alphanum_counter();

?>
    <fieldset class="main">
      <legend><?php echo $realcontact['fullname'] ?></legend>

      <input type="hidden" name="realcontacts[<?php echo $realcontact_id ?>][user_id]" value="<?php echo $user_id; ?>" />
<?php if ($new) { ?>
      <input type="hidden" name="realcontacts[<?php echo $realcontact_id ?>][guid]" value="<?php echo mt_rand(); ?>" />
<?php } ?>

      <table>
        <!-- tr>
          <td colspan="3" style="position: relative; top: -0.25em; font-family: Arial, Helvetica, sans-serif; font-style: italic;">If the personal names come after the family name,
             enter the whole name into the second box.</td>
        </tr -->

        <tr>
          <td valign="bottom"><label for="name3_<?php echo $cosuf; ?>">Name:</label></td>
          <td>
            <div style="display: table; border-spacing: 0;">
              <div style="display: table-row;">
                <div style="display: table-cell;"><label for="name2_<?php echo $cosuf; ?>">Prefix / forenames:</label></div>
                <div style="display: table-cell;"><label for="name3_<?php echo $cosuf; ?>">Name to sort by:</label></div>
              </div>
              <div style="display: table-row;">
                <div style="display: table-cell;"><input type="text" id="name2_<?php echo $cosuf; ?>" name="realcontacts[<?php
echo $realcontact_id ?>][forenames]" size="25" maxlength="85"<?php
if (isset($realcontact['forenames'])) echo ' value="', $realcontact['forenames'], '"';
?> />&nbsp;</div>
                <div style="display: table-cell;"><input type="text" id="name3_<?php echo $cosuf; ?>" name="realcontacts[<?php
echo $realcontact_id ?>][surname]" size="35" maxlength="85"<?php
if (isset($realcontact['surname'])) echo ' value="', $realcontact['surname'], '"';
?> /></div>
              </div>
            </div>
          </td>
        </tr>

        <tr>
          <td><label for="telno<?php echo $cosuf, '">Telephone ', $numero; ?>:</label></td>
          <td><input type="tel" id="telno<?php
echo $cosuf; ?>" name="realcontacts[<?php
echo $realcontact_id ?>][phone]" size="22" maxlength="40"<?php
if (isset($realcontact['phone'])) echo ' value="', $realcontact['phone'], '"';
?> /></td>
        </tr>

        <tr>
          <td><label for="email<?php echo $cosuf; ?>">E-mail address:</label></td>
          <td>
            <input type="email" id="email<?php
echo $cosuf; ?>" name="realcontacts[<?php
echo $realcontact_id ?>][email]" size="40" maxlength="120"<?php
if (isset($realcontact['email'])) echo ' value="', $realcontact['email'], '"';
?> />
            <input type="submit" class="offscreen" />
          </td>
        </tr>

<?php if (! defined('PLAIN_FORM') and ! $saveall) { ?>
        <tr>
          <td valign="top"><label for="metacontacts<?php echo $cosuf; ?>">Clients:</label></td>
          <td valign="top">
            <div style="display: table; border-spacing: 0;">
              <div style="display: table-row;">
                <div style="display: table-cell; vertical-align:top;">
                  <script type="text/javascript">
                  // <!-- [CDATA[
                    function co_enbt_one<?php echo $cosuf; ?>() {
                      if (document.getElementById("busid<?php echo $cosuf; ?>").selectedIndex == 0) {
                        document.getElementById("btnconew<?php echo $cosuf; ?>").disabled=true;
                      } else {
                        document.getElementById("btnconew<?php echo $cosuf; ?>").disabled=false;
                      }
                    }
                    function co_enbt_two<?php echo $cosuf; ?>() {
                      if (document.getElementById("metacontacts<?php echo $cosuf; ?>").selectedIndex == -1) {
                        document.getElementById("btncogto<?php echo $cosuf; ?>").disabled=true;
                      } else {
                        document.getElementById("btncogto<?php echo $cosuf; ?>").disabled=false;
                      }
                    }
                  // ]] -->
                  </script>

<?php

  $contacts_conditions['user_id'] = $user_id;
  $contacts_conditions['realcontact_id'] = $realcontact_id;
  draw_related('metacontacts NATURAL JOIN clients', $contacts_conditions,
    "CONCAT_WS(' ',user_id,business_id,client_id)", 'client_name',
    "clients that have &lsquo;{$realcontact['fullname']}&rsquo; as a contact",
    9, "metacontacts$cosuf", "realcontacts[$realcontact_id][metacontacts]",
    "co_enbt_two$cosuf()"
  );
  unset($contacts_conditions);

?>
                </div>

                <div style="display: table-cell; vertical-align:top; padding: 0 0.5em;" onmouseover="co_enbt_one<?php echo $cosuf; ?>(); co_enbt_two<?php echo $cosuf; ?>()">
                  <input type="submit" class="button" name="btn_contact_chg[<?php 
    echo $realcontact_id ?>]" id="btncochg<?php echo $cosuf; ?>" value="Link/sever clients" /><br />
                  <input type="submit" class="button" name="btn_contact_new[<?php 
    echo $realcontact_id ?>]" id="btnconew<?php echo $cosuf; ?>" value="Create client" />
                  <span style="position: relative; top: 1px;">in</span>
                  <select id="busid<?php echo $cosuf; ?>" name="realcontacts[<?php
    echo $realcontact_id ?>][business_id]" onchange="co_enbt_one<?php echo $cosuf; ?>()">
                    <option value="" selected="selected"></option>
<?php

  $result = $db_connection->query("SELECT business_id,business_name FROM businesses WHERE user_id=$user_id");
  if (! $result) {
    echo '                  </select>
                </div>
              </div>
            </div>
          </td>
        </tr>
      </table>
    </fieldset>
  </form>

';
    trigger_error("I seem to have encountered a problem while retrieving "
      . "the list of businesses.&nbsp; The error message is:<br />\n     "
      . $db_connection->error, E_USER_ERROR);
  }
  if ($row = $result->fetch_assoc()) {
    do {
      echo '                    <option value="', $row['business_id'];
      echo '">', htmlify(truncate($row['business_name'])), "</option>\n";
    } while ($row = $result->fetch_assoc());
    $any = TRUE;
  }
  $result->free();

?>
                  </select><br />
                  <input type="submit" class="button" name="btn_contact_gto[<?php 
    echo $realcontact_id ?>]" id="btncogto<?php echo $cosuf; ?>" value="View selected client" />
                </div>
              </div>
            </div>
          </td>
        </tr>

<?php } ?>
        <tr>
          <td valign="top"><label for="notebox<?php echo $cosuf; ?>">Notes:</label></td>
          <td><textarea id="notebox<?php 
echo $cosuf; ?>" name="realcontacts[<?php
echo $realcontact_id ?>][notes]" rows="<?php if ($saveall or $new) echo '4'; else echo '10'; ?>" cols="60"><?php
if (isset($realcontact['notes'])) echo $realcontact['notes'];
?></textarea></td>
        </tr>
      </table>

      <script type="text/javascript">
      // <!-- [CDATA[
        co_enbt_one<?php echo $cosuf; ?>();
        co_enbt_two<?php echo $cosuf; ?>();
      // ]] -->
      </script>

      <div class="savebutton">
        <script type="text/javascript" src="javascript/cancel.js"></script>

        <input type="submit" name="btn_save" value="<?php echo ($new ? 'Create' : 'Save' . ($saveall ? ' All' : '')); ?>" />
      </div>
    </fieldset>
