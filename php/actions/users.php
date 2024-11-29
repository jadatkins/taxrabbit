<?php

$user = $_POST['user'];

if (! array_key_exists('user_id', $user))
  trigger_error("No user_id was given.&nbsp; There must be something wrong " .
            "with the user details page you've just come from.", E_USER_ERROR);
$user_id = $user['user_id'];

action_clean_input($user, array(), array('user_name', 'postcode', 'utr', 'nin'));

$new = action_isnew($user, $user_id, 'user_id', 'user', 'users', array());

if (is_numeric($user_id) and $user_id >= 0) {
  require_once './php/identify/authenticate.php';
  if (AUTHUID != $user_id) {
    define('RESPONSE_CODE', 500);
    header('HTTP/1.1 500 Internal Server Error');
    trigger_error("It looks like you're trying to update records for several users at once.", E_USER_ERROR);
    die;
  }
}

$user_name = '';
$user_name_ybox = '';
action_name_or_title(
  $user, $user_id, $user_name, $user_name_ybox,
  array('user_id' => $user_id),
  'user', 'users', 'user_name', 'name'
);

if (empty($user['email']) and $_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR']) {
  if ($new)
    friendly_error('You must provide a user name / email address and password.', TRUE);
  else
    friendly_error("You can't remove your user name / email address, because "
                   . "then you wouldn't be able to log in.", TRUE);
}
if (empty($user['email']) and !empty($user['password']))
  friendly_error("You can't have a password without a user name / email address.", TRUE);

action_fix_email($user, 'email');
$matches = array();
foreach (array('email' => 'user name / email address', 'password' => 'password') as $k => $v) {
  if (empty($user[$k])) continue;
  if (preg_match('/^\s.+\s$/', $user[$k]))
    friendly_error("Your $v cannot begin or end with a space.", TRUE);
  if (preg_match_all('/[^ !#-&(-9;-_a-~]+/', $user[$k], $matches, PREG_PATTERN_ORDER))
    friendly_error("Your $v contains these forbidden characters: " .
        implode($matches[0]) . "<br />\n     Please go back and try again.", TRUE);
}
unset($k, $v, $matches);

$nopassword = FALSE;
if (!empty($user['delpasswd'])) {
  if ($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR'])
    friendly_error("You can't remove your password because then you wouldn't be able to log in.", TRUE);
  $user['password'] = NULL;
  $nopassword = TRUE;
} else {
  $newcredentials = ($new and $_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR']);
  if (! $new) {
    $uresult = $db_connection->query("SELECT * FROM users WHERE user_id=$user_id")
      or trigger_error("I can't check which details of user $numero $user_id are different from " .
          "the stored details.&nbsp; The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);
    $olduser = $uresult->fetch_assoc();
    if (! $olduser) {
      define('RESPONSE_CODE', 500);
      header('HTTP/1.1 500 Internal Server Error');
      trigger_error("First I thought you were updating the details for the existing user " .
        "$numero $user_id, now I think user $numero $user_id doesn't exist.&nbsp; I'm confused.", E_USER_ERROR);
    }
    $uresult->free();

    if ($user['user_name'] != $olduser['user_name'] or $user['email'] != $olduser['email'])
      $newcredentials = TRUE;
    if (empty($olduser['password']) and empty($user['password']))
      {$newcredentials = FALSE; $nopassword = TRUE;}
    if (empty($user['email']) and empty($user['delpasswd']) and !empty($olduser['password']))
      friendly_error("You can't remove your user name / email address unless you also remove your password.", TRUE);

    // protect user name and password of 'Alexander Atkins' testing account
    //if (($newcredentials or !empty($user['password'])) and $user_id == 1 and
    //    $olduser['user_name'] == 'Alexander Atkins' and
    //    $olduser['email'] == 'alexander@taxrabbit.co.uk' and
    //    strlen($olduser['password']) == 32) {
    //  define('RESPONSE_CODE', 403);
    //  header('HTTP/1.1 403 Forbidden');
    //  trigger_error("You can't change the details of the user &lsquo;Alexander Atkins&rsquo;.", E_USER_ERROR);
    //  die;
    //}

    unset($uresult);
  }

  if ($newcredentials or !empty($user['password'])) {
    if (empty($user['password']))
      friendly_error('You must provide a password' . ($new ? '' :
          ' when you change your name or user name / email address') . '.', TRUE);
    if (array_key_exists('pwconfirm', $user) and $user['password'] != $user['pwconfirm'])
      friendly_error("The two passwords entered don't match.", TRUE);
    if (! $new and strlen($olduser['password']) != 32 and $user['password'] == $olduser['password']) {
      friendly_error("You can't set your new password to be the same as your " .
                     "temporary password.&nbsp; Choose something else.", TRUE);
    if (strlen($user['password']) < 6 or stripos("{$user['user_name']}\t{$user['email']}", $user['password']) !== FALSE)
      friendly_error("Your password is too easy to guess.&nbsp; Use a better password.", TRUE);
    }
    $user['password'] = md5($user['email'] . ':' . REALM . ':' . $user['password']);
  } elseif (! $nopassword and ! $new) {
    unset($user['user_name'], $user['email'], $user['password']);
  }
}
unset($nopassword);

if (array_key_exists('postcode', $user) and $user['postcode'] != '') {
  $matches = array();
  if (preg_match('/^([A-Za-z]{1,2}\d(\d|[A-Za-z])?) ?(\d[A-Za-z]{2})$/', $user['postcode'], $matches))
    $user['postcode'] = strtoupper($matches[1] . ' ' . $matches[3]);
  else
    friendly_error("&lsquo;{$user['postcode']}&rsquo; isn't a valid UK postcode.&nbsp;" .
        ' If you live outside the UK, please leave the postcode box blank.', TRUE);
  unset($matches);
}
else $user['postcode'] = NULL;

if (array_key_exists('utr', $user) and $user['utr'] != '') {
  $matches = array();
  if (! preg_match('/^(\d{5})\W?(\d{5})K?$/', $user['utr'], $matches))
    friendly_error("&lsquo;{$user['utr']}&rsquo; isn't a Unique Taxpayer Reference.&nbsp;" .
        ' A UTR should be a ten-digit number, possibly followed by the letter K.', TRUE);
  else $user['utr'] = $matches[1] . $matches[2];
  unset($matches);
}
else $user['utr'] = NULL;

if (array_key_exists('nin', $user) and $user['nin'] != '') {
  $matches = array();
  if (! preg_match('/^([A-Za-z]{2})\s?(\d{2})\s?(\d{2})\s?(\d{2})\s?([A-Za-z])$/', $user['nin'], $matches))
    friendly_error("&lsquo;{$user['nin']}&rsquo; isn't a National Insurance number.&nbsp;" .
        ' An NI number should be of the form: AB 12 34 56 C', TRUE);
  else $user['nin'] = strtoupper($matches[1] . $matches[2] . $matches[3] . $matches[4] . $matches[5]);
  unset($matches);
}
else $user['nin'] = NULL;

if (! $new) {
  $mailuser = $user;
  $pwchange = (empty($user['password']) ? 'left blank' :
                ($user['password'] == $olduser['password'] ? 'kept the same' : 'changed')
              );
  unset($olduser['password'], $mailuser['password'], $mailuser['pwconfirm']);
  mail('alexander@taxrabbit.co.uk', 'user details changed',
      "old details:\n" . var_export($olduser, TRUE)
        . "\n\nnew details:\n" . var_export($mailuser, TRUE)
        . "\n\npassword: $pwchange",
      'From: webserver@taxrabbit.co.uk' . "\r\n"
    . 'Reply-To: alexander@taxrabbit.co.uk' . "\r\n"
    . 'Content-Type: text/plain; charset="UTF-8"');
  unset($olduser, $mailuser);
}

action_do_sql(
  $user, $user_id, 'user_id', 'users',
  array('user_id'),
  array('user_name', 'email', 'password', 'postcode', 'utr', 'nin'),
  $user_name, $user_name_ybox
);

if ($new) mail('alexander@taxrabbit.co.uk', 'new user',
    "I have created $user_name_ybox with email address ‘{$user['email']}’.",
    'From: webserver@taxrabbit.co.uk' . "\r\n"
  . 'Reply-To: alexander@taxrabbit.co.uk' . "\r\n"
  . 'Content-Type: text/plain; charset="UTF-8"');

if ($new and ($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR'] or !empty($user['password']))) {
  $headings = array('Hey, Listen Up!');
  $navbox = FALSE;
  define('SUPPRESS_YBOX', TRUE);
  require_once './php/header_one.php';
  require_once './php/header_two.php';
  ?>
<p>On the next screen, you will be asked for a &lsquo;user name&rsquo; and
   password.&nbsp; You must enter your email address (<?php echo $user['email']; ?>)
   as your user name.&nbsp; Your password is the same one you typed in a few
   seconds ago.</p>

<p><a href="<?php echo $_POST['goto']; ?>">Okay, take me to the log in screen</a></p>
<?php
  exit;
}