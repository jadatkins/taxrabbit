<?php

define('NONCE_EXPIRY', 180);  // maximum nonce age in seconds

// function to parse the http auth header
function http_digest_parse($txt) {
  // protect against missing data
  $needed_parts = array('username'=>1, 'nonce'=>1, 'uri'=>1, 'qop'=>1, 'nc'=>1, 'cnonce'=>1, 'response'=>1);
  $data = array();
  $keys = implode('|', array_keys($needed_parts));

  preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

  foreach ($matches as $m) {
    $data[$m[1]] = $m[3] ? $m[3] : $m[4];
    unset($needed_parts[$m[1]]);
  }

  return $needed_parts ? false : $data;
}


function issue_nonce($realm, $stale = FALSE) {
  global $db_connection;
  if ($result = $db_connection->query('SELECT EXISTS(SELECT * FROM '
        . 'nonces WHERE TIMESTAMPDIFF(SECOND,ts,NOW()) < '.NONCE_EXPIRY.')')
      and $row = $result->fetch_row() and ! $row[0]) {
    $db_connection->query('TRUNCATE TABLE nonces');
  } else {
    if (isset($db_connection))
      $db_connection->query('DELETE FROM nonces WHERE TIMESTAMPDIFF(SECOND,ts,NOW()) > ' . NONCE_EXPIRY);
  }
  $nonce = sha1(uniqid() . $_SERVER['REQUEST_URI'] .
           ($_SERVER['REQUEST_METHOD'] == 'POST' ? serialize($_POST) : ''));
  if (! $db_connection->query("INSERT INTO nonces (nonce) VALUES('$nonce')"))
    return 500;
  header('HTTP/1.1 401 Unauthorized');
  header('WWW-Authenticate: Digest realm="'.$realm.'", qop="auth", nonce="'.$nonce.($stale?'", stale=true':'"'));
  if ($stale) die();
  define('RESPONSE_CODE', 401);
  friendly_error('It looks like you\'ve refused to enter a password.', FALSE, E_USER_ERROR, FALSE);
  die();
}


function authenticate_user($user_id) {
  global $db_connection;

  // fix for suPHP, from http://devmd.com/r/digest-access-authentication and
  // www.sslcatacombnetworking.com/articles/http-authentication-php-cgi.html
  if (substr($_SERVER['PHP_AUTH_DIGEST'], 0, 7) == 'Digest ')
    $_SERVER['PHP_AUTH_DIGEST'] = substr($_SERVER['PHP_AUTH_DIGEST'], 7);

  // has the browser attempted to authenticate?
  if (empty($_SERVER['PHP_AUTH_DIGEST']))
    return issue_nonce(REALM);

  // analyze the PHP_AUTH_DIGEST variable
  $digest = http_digest_parse($_SERVER['PHP_AUTH_DIGEST']);
  if (! $digest) return 400;  // some parts are missing
  if (strcasecmp($digest['qop'], 'auth')) return 400;

  // get user details
  if (empty($user_id) or !is_numeric($user_id)) {
    $user_phrase = '';
  } else {
    $user_id = intval($user_id);
    $user_phrase = "(user_id=$user_id OR user_id=1) AND ";
  }
  $email = $digest['username'];
  treat_input_string($email);
  $email = wrap_for_sql($email);
  $result = $db_connection->query("SELECT * FROM users WHERE {$user_phrase}email=$email");
  if (! $result) return 500;
  $user = $result->fetch_assoc();
  if (! $user) {
    if ($user_phrase != '') {
      $result = $db_connection->query("SELECT user_id FROM users WHERE email=$email");
      $user = $result->fetch_assoc();
    }
    if (! $user) {
      define('RESPONSE_CODE', 401);
      header('HTTP/1.1 401 Unauthorized');
      header('WWW-Authenticate: Digest realm="'.REALM.'", qop="auth", nonce="'.$digest['nonce'].'"');
      trigger_error("There's no account with the username / email address " .
            "&lsquo;{$digest['username']}&rsquo;.", E_USER_ERROR);
      die();
    } else {
      $wrong = $user_id;
      $right = intval($user['user_id']);
      $count = 0;
      $location = str_replace("user_id=$wrong", "user_id=$right", $_SERVER['REQUEST_URI'], $count);
      if ($count == 0) {
        if (strpos($location, '?') === FALSE) $location .= "?user_id=$right";
        else $location = str_replace('?', "user_id=$right&", $location);
      }
      redirect(ltrim($location, '/'));
      die();
    }
  }
  $result->free();
  unset($result);
  if ($user_phrase == '') $user_id = intval($user['user_id']);

  $HA1 = $user['password'];
  // check whether user's password (in the database) is stored as a hash
  if (strlen($HA1) == 32) {
    $temp_password = FALSE;
  } else {
    // if no password and logging on from localhost, authenticate automatically
    if ($HA1 == '' and $_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR']) {
      define('AUTHUID', $user['user_id']);
      unset($user['password']);
      return 200;
    } else {
      // otherwise, assume it is a temporary password
      $temp_password = TRUE;
      $HA1 = md5($user['email'] . ':' . REALM . ':' . $HA1);
    }
  }

  // generate the valid response
  $HA2 = md5($_SERVER['REQUEST_METHOD'].':'.$digest['uri']);
  $valid_response = md5($HA1.':'.$digest['nonce'].':'.$digest['nc'].':'.$digest['cnonce'].':'.$digest['qop'].':'.$HA2);

  if ($digest['response'] != $valid_response) {
    define('RESPONSE_CODE', 401);
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Digest realm="'.REALM.'", qop="auth", nonce="'.$digest['nonce'].'"');
    error_log("wrong password: username={$digest['username']}", 0);
    mail('alexander@taxrabbit.co.uk', 'failed login attempt',
         "wrong password:\n" .
         "REQUEST_URI = {$_SERVER['REQUEST_URI']}\n" .
         "HTTP_REFERER = {$_SERVER['HTTP_REFERER']}\n" .
         "username = {$digest['username']}",
         'From: webserver@taxrabbit.co.uk' . "\r\n"
       . 'Reply-To: alexander@taxrabbit.co.uk' . "\r\n"
       . 'Content-Type: text/plain; charset="UTF-8"');
    friendly_error('Your password doesn\'t match up.', FALSE, E_USER_ERROR, FALSE);
    die();
  }

  // check whether nonce exists
  $result = $db_connection->query("SELECT nonce,nc,TIMESTAMPDIFF(SECOND,ts,NOW()) " .
                          "AS age FROM nonces WHERE nonce='{$digest['nonce']}'");
  if (! $result) return 500;
  $nonce = $result->fetch_assoc();
  if (! $nonce) return issue_nonce(REALM, TRUE);  // nonce doesn't exist

  // check whether nonce has expired
  if ($digest['nc']  <= $nonce['nc']) return issue_nonce(REALM, TRUE);
  if ($nonce['age'] > NONCE_EXPIRY) return issue_nonce(REALM, TRUE);

  // increase nonce-count
  $db_connection->query("UPDATE nonces SET nc={$digest['nc']} WHERE nonce='{$nonce['nonce']}'");

  $db_connection->query('UPDATE users SET lastseen=NOW() WHERE user_id=' . $user['user_id']);

  // if temporary password, require the user to change it
  if ($temp_password and basename($_SERVER['PHP_SELF']) != 'profile.php'
                     and basename($_SERVER['PHP_SELF']) != 'action.php') {
    redirect("profile.php?user_id=$user_id&changepassword=1");
    die();
  }

  if ($user['user_id'] == 1) {
    define('AUTHUID', $user_id);
    define('NO_ERROR_LOG', TRUE);
  }
  else
    define('AUTHUID', $user['user_id']);
  unset($user['password']);
  return 200;
}


$response_code = authenticate_user($user_id);

if ($response_code != 200) define('RESPONSE_CODE', $response_code);
switch ($response_code) {
  case 200:
    unset($response_code);
    break;
  case 400:
    header('HTTP/1.1 400 Bad Request');
    trigger_error("Your browser hasn't correctly proved that you've " .
        "entered the right password.&nbsp; Try using a different browser.", E_USER_ERROR);
    die();
  default:
    header('HTTP/1.1 500 Internal Server Error');
    trigger_error("There's a problem with the password checking mechanism.&nbsp; " .
      "Unfortunately, there's nothing you can do about it: the fault is mine.", E_USER_ERROR);
    die();
}
