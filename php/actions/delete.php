<?php

foreach ($_POST['btn_delete'] as $information => $ignore) {
  if (! preg_match('/^([a-z]+)(\d+)-(\d+)-(\d+)$/', $information, $delprikey))
    break;

  $del_table = $delprikey[1];
  $del_usrid = intval($delprikey[2]);
  $del_busid = intval($delprikey[3]);
  $del_recid = intval($delprikey[4]);

  if (! defined('AUTHUID') and ($user_id = $del_usrid) > 0)
    require_once './php/identify/authenticate.php';
  if (AUTHUID != $del_usrid) die;

  switch ($del_table) {
    case 'job':
    case 'expense':
      if (get_magic_quotes_gpc()) $headings = array_map('stripslashes', $headings);
      $urls = array_map('htmlspecialchars', $urls);
      $friendlyname = end($headings);
      $headings[] = 'Delete';

      require_once './php/header_one.php';
      echo '  <meta name="viewport" content="width=device-width" />
';
      require_once './php/header_two.php';

      ?>
  <form accept-charset="UTF-8" name="frm_main" method="post" action="delete.php" onsubmit="disable(['document.frm_main.btn_delete'])">

<?php

      $hiddenfields .= "
          <input type=\"hidden\" name=\"del_table\" value=\"$del_table\" />
          <input type=\"hidden\" name=\"del_usrid\" value=\"$del_usrid\" />
          <input type=\"hidden\" name=\"del_busid\" value=\"$del_busid\" />
          <input type=\"hidden\" name=\"del_recid\" value=\"$del_recid\" />
          <input type=\"hidden\" name=\"friendlyname\" value=\"" . htmlify($friendlyname) . "\" />\n";
      insert_hidden_fields($_POST['goto']);

      ?>

  <div style="text-align: center;">
    <p>Are you sure you want to delete the <?php echo $del_table; ?> &lsquo;<?php
      echo htmlify($friendlyname); ?>&rsquo;?&nbsp; After
       this, there is no going back.</p>

    <p>
      <script type="text/javascript">
      // <!-- [CDATA[
        insertcancel();
      // ]] -->
      </script>
      &emsp;
      <input type="submit" name="btn_delete" value="Delete" />
    </p>
  </div>
  </form>
<?php include './php/footer.php';

      exit;
  }
}

trigger_error("I don't understand what you're trying to do.&nbsp; It sounds" .
    " like you want to delete something, but I couldn't make sense of what " .
    "you're asking.&nbsp; There must be something wrong with the page you've".
    " just come from.&nbsp; Try something else.", E_USER_ERROR);
