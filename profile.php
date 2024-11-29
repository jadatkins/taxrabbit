<?php

if ($_SERVER['HTTP_USER_AGENT'] == 'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)'
    or substr($_SERVER['REMOTE_ADDR'], 0, 8) == '65.55.21'
    or (substr($_SERVER['REMOTE_ADDR'], 0, 8) == '65.55.20' and
        intval(substr($_SERVER['REMOTE_ADDR'], 8, 1)) >= '7')
    ) {
  header("HTTP/1.0 404 Not Found");
  include BASEURL . 'errordoc/404.shtml';
  die;
}

define('HELPCONTACTINFO', TRUE);
$navbox = FALSE;
require_once './php/essentials.php';
include_once './php/nocache.php';

if (empty($_GET['user_id'])) {
  if ($user_id == '0' or $_GET['user_id'] == '0')
    trigger_error('0 is not a valid user ID.', E_USER_ERROR);
  else redirect();  // Bing is so broken. Screw you, Microsoft
       //trigger_error('No user ID was specified.', E_USER_ERROR);
}
$user_id = $_GET['user_id'];

if (($intid = (int) $user_id) <= 0) $new = TRUE;
else {
  $user_id = $intid;

  $result = $db_connection->query("SELECT EXISTS(SELECT * FROM users WHERE user_id=$intid)");
  if ($result) {
    $answer = $result->fetch_row();
    $result->free();
    $new = ! $answer[0];
    unset($answer);
  }
  unset($result);
}
unset($intid);

$unique_id = "?user_id=$user_id";

if ($new) {
  define('SUPPRESS_YBOX', TRUE);
  $headings = array('New User');
  $navbox = TRUE;
  $urls = array();
} else {
  require_once './php/identify/authenticate.php';
  if (AUTHUID != $user_id) die();

  $result = $db_connection->query("SELECT * FROM users WHERE user_id=$user_id")
    or trigger_error("I couldn't retrieve the details of user $numero $user_id" .
            ".&nbsp; The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);
  $user = $result->fetch_assoc()
    or trigger_error("It looks like user $numero $user_id doesn't exist.", E_USER_ERROR);
  $result->free();

  $headings = array($user['user_name'], 'User Details');
  $navbox = TRUE;
  $urls = array("user.php$unique_id");

  $user_name = htmlify(truncate($user['user_name']));
  foreach ($user as $k => &$v) {
    if ($v === NULL) unset($user[$k]);
    else $v = htmlify($v);
  }
  unset($result, $v);    // It is necessary to unset $v.
}

require_once './php/header_one.php';
require_once './php/header_two.php';

if ($new) {
?>
<h3>Warning</h3>

<p>You shouldn't be creating an account here unless you've already contacted me
   using the contact details below. The reason is that there are several
   testing accounts on <?php echo APPNAME; ?> at the moment, and unless I know
   that your account is a real account, I might delete it without warning.</p>

<p>Furthermore, I should have explained this to you already over email, but just
   in case: <?php echo APPNAME; ?> is still in development and currently there is
   a small possibility of your account data being destroyed by accident.&nbsp;
   It should not be necessary to keep a separate copy of all data you enter into
   <?php echo APPNAME; ?>, since data loss is very unlikely, but I advise you to
   keep enough records (outside of <?php echo APPNAME; ?>) to enable you to
   reconstruct your accounts in case of catastrophe.</p>

<h3>Your Details</h3>

<?php
}

?>
  <form accept-charset="UTF-8" name="frm_main" method="post" action="action.php" onsubmit="disable()">
<?php insert_hidden_fields('user.php' . $unique_id); ?>
    <input type="hidden" name="user[user_id]" value="<?php echo $user_id ?>" />
<?php if ($new) { ?>
    <input type="hidden" name="user[guid]" value="<?php echo mt_rand(); ?>" />
<?php } ?>

    <fieldset class="main">
<?php

if (!empty($_REQUEST['changepassword'])) {
  ?>
      <p><strong>You have a temporary password.&nbsp; You must create a new one
      before you may do anything else.</strong></p>
<?php
} elseif ($new) {
  if ($_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR']) {
  ?>
      <p>You may create a user name and password, to protect your account and to
         allow you to log in from other computers, however <strong>everything is
	 optional except your full name</strong>.</p>
<?php
  }
} else {
  ?>
      <p>Don't enter a password unless you're changing something in this first
         section (name, email address or password).&nbsp; Leave the password box
	 empty if you're only updating your postcode, UTR or NI number.
<?php
  if ($_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR'] and isset($user['password'])) {
  ?>&nbsp;
         If you remove your password, you'll only have access to your accounts
	 from this computer.<?php
  }
  echo "</p>\n";
}

?>

      <table>
        <tr>
    	  <td><label for="namebox">Full name</label></td>
    	  <td><input type="text" id="namebox" name="user[user_name]" size="36" required="required" maxlength="85"<?php
if (! $new) echo ' value="', $user['user_name'], '"';
else echo ' placeholder="Joe Bloggs"'; ?> /></td>
        </tr>

        <tr>
    	  <td><label for="emailbox">User name / Email address</label></td>
    	  <td><input type="email" id="emailbox" name="user[email]" size="36" required="required" maxlength="120"<?php
if (isset($user['email'])) echo ' value="', $user['email'], '"';
else echo ' placeholder="joe.bloggs@example.com"';
?> /> <?php if ($new or empty($user['email'])) echo '&larr; Use this as your &lsquo;user name&rsquo; when logging in.'; ?></td>
        </tr>
<?php
if ($_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR'] and !empty($user['password'])) {
?>
        <tr>
    	  <td colspan="2" align="center">
	    <input type="checkbox" id="delpassw" name="user[delpasswd]" />
	    <label for="delpassw">Remove password</label></td>
	  </td>
        </tr>
<?php
}
?>
        <tr>
    	  <td><label for="passwbox"><?php echo ($new ? 'P' : 'New p'); ?>assword</label></td>
    	  <td><input type="password" id="passwbox" name="user[password]" size="36"<?php
if ($new and $_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR']) echo ' required="required"';
?> maxlength="120" autocomplete="off" /></td>
        </tr>

        <tr>
    	  <td><label for="confpbox">Confirm password</label></td>
    	  <td><input type="password" id="confpbox" name="user[pwconfirm]" size="36"<?php
if ($new and $_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR']) echo ' required="required"';
?> maxlength="120" autocomplete="off" /></td>
        </tr>
      </table>

      <hr />
<?php if ($new) { ?>

      <p>You can leave these blank for now if you don't have the details to hand:</p>
<?php } ?>

      <table>
        <tr>
    	  <td><label for="pcbox">Postcode</label></td>
    	  <td><input type="text" id="pcbox" name="user[postcode]" size="9" maxlength="8"<?php
if (isset($user['postcode'])) echo ' value="', $user['postcode'], '"';
else echo ' placeholder="SW12 3XX"';
?> /> (where HMRC writes to you)</td>
    	</tr>

        <tr>
    	  <td><label for="utrbox">Unique Taxpayer Reference (UTR)</label></td>
    	  <td><input type="text" id="utrbox" name="user[utr]" size="13" maxlength="12"<?php
if (isset($user['utr']))
  echo ' value="', substr($user['utr'],0,5), ' ', substr($user['utr'],5), '"';
else echo ' placeholder="12345 67890"';
?> /></td>
    	</tr>

        <tr>
    	  <td><label for="ninbox">National Insurance number</label></td>
    	  <td><input type="text" id="ninbox" name="user[nin]" size="13" maxlength="13"<?php
if (isset($user['nin'])) echo ' value="', substr($user['nin'],0,2), ' ',
	   substr($user['nin'],2,2), ' ', substr($user['nin'],4,2), ' ',
	   substr($user['nin'],6,2), ' ', substr($user['nin'],8  ), '"';
else echo ' placeholder="AB 12 34 56 C"';
?> /></td>
    	</tr>
      </table>

      <div class="savebutton">
	<script type="text/javascript">
        // <!-- [CDATA[
          insertcancel();
        // ]] -->
        </script>

        <input type="submit" name="btn_save" value="<?php echo $new ? 'Create' : 'Save'; ?>" />
      </div>
    </fieldset>
  </form>

<?php include './php/footer.php';
