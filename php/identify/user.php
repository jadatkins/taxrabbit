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

if (array_key_exists('user_id', $_REQUEST)) {
  $user_id = (int) $_REQUEST['user_id'];
  if ($user_id <= 0)
    trigger_error($_REQUEST['user_id'] . ' is not a valid user ID.', E_USER_ERROR);
}
else $user_id = null;

require_once BASEURL . 'php/identify/authenticate.php';
if (! isset($user_id)) $user_id = AUTHUID;
elseif (AUTHUID != $user_id) die();

$result = $db_connection->query("SELECT user_name,email FROM users WHERE user_id=$user_id")
  or trigger_error("I couldn't retrieve the details of user $numero $user_id" .
          ".&nbsp; The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);
$user = $result->fetch_assoc()
  or trigger_error("It looks like user $numero $user_id doesn't exist.", E_USER_ERROR);
$result->free();

$user_name = htmlify(truncate($user['user_name']));
unset($result);

$unique_id = "?user_id=$user_id";
