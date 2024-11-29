<?php
define('NO_DATABASE', TRUE);
define('HELPCONTACTINFO', TRUE);
define('INCL_CREDITS', TRUE);
define('PANIC_MSG', FALSE);
$navbox = TRUE;
require_once './php/essentials.php';
$nonce = sha1(uniqid() . $_SERVER['REQUEST_URI'] .
         ($_SERVER['REQUEST_METHOD'] == 'POST' ? serialize($_POST) : ''));
header('HTTP/1.1 401 Unauthorized');
header('WWW-Authenticate: Digest realm="'.REALM.'", qop="auth", nonce="'.$nonce);
define('RESPONSE_CODE', 'LOGOUT');
friendly_error('I have attempted to log you out.&nbsp; It may or may not have' .
  ' worked.&nbsp; To check, try to <a href="user.php">log in</a> again (and ' .
  'click &lsquo;Cancel&rsquo;).&nbsp; But to make absolutely sure, you should' .
  ' quit your browser (close all windows and tabs).', FALSE, E_USER_ERROR, FALSE);
