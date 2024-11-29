<?php

if (isset($_REQUEST['user_id']) and $_REQUEST['user_id'] == 1)
  error_reporting(E_ALL);
else
  error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR);

// initialise constants and variables
define('APPNAME', 'TaxRabbit');
if (! defined('BASEURL')) define('BASEURL', './');
define('NUMERO', 'N<span class="osuper">o</span>');
define('REALM', 'Your username for TaxRabbit is probably your email address.');
if (!defined('E_USER_DEPRECATED')) define('E_USER_DEPRECATED', 16384);

$numero = NUMERO;
mb_internal_encoding('UTF-8');
date_default_timezone_set('Europe/London');

if ($_SERVER['REQUEST_METHOD'] == 'BREW') {
  header("HTTP/1.1 418 I'm a teapot");
  include BASEURL . 'errordoc/418.shtml';
  die;
}

if (! isset($open_xhtml_tags) || ! is_array($open_xhtml_tags))
  $open_xhtml_tags = array();

if (! isset($headings) || ! is_array($headings)) $headings = array();
if (! isset( $navbox )                         )   $navbox = FALSE;
if ($navbox and ! isset($urls) || ! is_array($urls)) $urls = array();

// Set $saveall = TRUE if you are about to display forms for multiple (related)
// records on the same page.
if (! isset( $saveall ) || ! is_bool( $saveall )) $saveall = FALSE;


// Function definitions follow.

function alphanum_counter() {
  static $a = 0;
  return base_convert($a++, 10, 36);
}

function yellowboxtie($uid) {
  global $db_connection;
  static $tie = null;
  if (is_int($tie)) {
    $tie ++;
    return $tie;
  }
  $tie = 0;
  /* do {
    if (empty($uid)) break;
    $result = $db_connection->query("SELECT MAX(tie) AS tie FROM messages "
          . "WHERE user_id={$record['user_id']} AND ts=CURRENT_TIMESTAMP");
    if (! $result) break;
    if (! $row = $result->fetch_row()) break;
    $tie = intval($row[0]) + 1;
    unset($result, $row);
  } while (false); */
  return $tie;
}

// treat_input_string is the first of two functions for treating text input
// from the browser.  The other function that you need to use is wrap_for_sql.
function treat_input_string(&$text) {
  if (get_magic_quotes_gpc()) $text = stripslashes($text);
  $text = trim($text);
  return NULL;
}

// start of wrap_for_sql definition
define('WFS_DEFAULT', "WFS_\4\5\6\1\25\14\24");

function wrap_for_sql($item) {
  global $db_connection;
  if (is_int($item) or is_float($item))
    return (string) $item;
  if (is_bool($item))
    return $item ? 'TRUE' : 'FALSE';
  if (is_null($item) or $item === '')
    return 'NULL';
  if ($item === WFS_DEFAULT)
    return 'DEFAULT';
  return "'" . $db_connection->real_escape_string($item) . "'";
}
// end of wrap_for_sql definition

// truncate and htmlify are two functions for preparing text for output.  Text strings
// should be passed through truncate before being passed through htmlify.  This allows
// you to truncate parts of an output message before htmlifying the whole.
function truncate($text, $max_length = 40) {
  if (mb_strlen($text, 'UTF-8') > $max_length)
    {$text = mb_substr($text, 0, $max_length - 1, 'UTF-8') . '…';}
  return $text;
}

function htmlify($text) {
  $text = htmlentities($text, ENT_COMPAT, 'UTF-8');
  $text = nl2br($text);
  $text = preg_replace('/\h(?=\h)/', '&nbsp;', $text);
  return $text;
}

/* function realm($user_name) {
  // trim() should have already been done, but to ensure consistency:
  $sanitised = trim($user_name);
  // replace a few unwanted characters with similar-looking safe characters
  $sanitised = str_replace(array('\\', '"', ':'), array('/', "''", '.'), $sanitised);
  // escape all punctuation
  $sanitised = preg_replace(array('/\s+/', '#([!-/:-@[-`{-~])#'), array(' ', "\\\\$1"), $sanitised);
  // convert fancy characters to their ascii counterparts
  $sanitised = iconv('UTF-8', 'ASCII//TRANSLIT', $sanitised);
  // remove punctuation added by iconv()
  $sanitised = preg_replace('#(?<!\\\\)[!-/:-@[-`{-~]#', '', $sanitised);
  // un-escape previously existing punctuation
  $sanitised = preg_replace('#\\\\([!-/:-@[-`{-~])#', "$1", $sanitised);
  // final checks that nothing horrible is in there (next two instructions)
  $sanitised = str_replace(array('"', ':', '\\'), '', $sanitised);
  $sanitised = preg_replace("/[^ -~]/", '', $sanitised);
  // it's safe now
  return APPNAME . ' - ' . $sanitised;
} */

function jada_handle_error($errno, $errstr, $errfile, $errline /*, $errcontext*/ ) {
  if (error_reporting() & $errno) {
    if ($errno < E_USER_ERROR)
      $errstr .= " in <b>$errfile</b> on line <b>$errline</b>";
    return friendly_error($errstr, FALSE, $errno, TRUE, " in $errfile on line $errline");
  } elseif (! defined('NO_ERROR_LOG')) {
    error_log("REQUEST_URI: " . $_SERVER['REQUEST_URI'], 0);
    error_log("HTTP_REFERER: " . $_SERVER['HTTP_REFERER'], 0);
    error_log("$errstr in $errfile on line $errline", 0);
  }
  return FALSE;
}

function dummy_handler($errno, $errstr) {
  return FALSE;
}

// start of friendly_error definition
define('FRIENDLY_ERROR_GO_BACK', TRUE);

// $msg should be htmlified before calling friendly_error.
function friendly_error($msg, $goback = FALSE, $errno = E_USER_ERROR, $logging = TRUE, $context = '') {
  global $db_connection, $open_xhtml_tags, $headings, $navbox, $urls, $user, $user_id;

  if (! defined('PANIC_MSG')) define('PANIC_MSG', TRUE);

  if (!defined('RESPONSE_CODE')) define('RESPONSE_CODE', 200);
  switch (RESPONSE_CODE) {
    default:  $title = 'Error'; break;
    case 400: $title = 'Bad Request'; break;
    case 401: $title = 'Authorisation Required'; break;
    case 403: $title = 'Forbidden'; break;
    case 405: $title = 'Method Not Allowed'; break;
    case 500: $title = 'Internal Server Error'; break;
    case 'LOGOUT': $title = 'Log Out';
  }

  if (! defined('NO_ERROR_LOG') and $logging and
      ($errno & (E_USER_ERROR | E_USER_NOTICE | E_USER_WARNING | E_USER_DEPRECATED))
    ) {
    error_log("REQUEST_URI: " . $_SERVER['REQUEST_URI'], 0);
    if (! empty($_SERVER['HTTP_REFERER']))
      error_log("HTTP_REFERER: " . $_SERVER['HTTP_REFERER'], 0);
    error_log($msg . $context, 0);
  }

  if (defined('PLAIN_FORM')) {
    close_xhtml_tag(0);
  } else {
    if (!count($headings) or $headings[count($headings)-1] != $title) $headings[] = $title;
    if (!defined('AUTHUID') and !defined('SUPPRESS_YBOX')) define('SUPPRESS_YBOX', TRUE);
    include_once BASEURL . 'php/header_one.php';
    if (! defined('HEAD_INCLUDED'))
      echo '  <meta name="viewport" content="width=device-width" />
';
    include_once BASEURL . 'php/header_two.php';
    close_xhtml_tag('div', FALSE, 1)
      or close_xhtml_tag('body', FALSE, 1);
  }

  if (!($errno & (E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE | E_USER_DEPRECATED)))
    return FALSE;

  echo "  <p>";
  if (RESPONSE_CODE == 200) switch (mt_rand(0,27)) {
    case 0: echo 'Whoops: '; break;
    case 1: echo 'Oops: '; break;
    case 2: echo 'Whoopsie: '; break;
    case 3: echo 'Oopsy-daisy: '; break;
    case 4: echo 'Oh, dear.&nbsp; '; break;
    case 5: echo 'I say!&nbsp; '; break;
    case 6: echo "Something's gone wrong: "; break;
    case 7: echo 'Excuse me.&nbsp; '; break;
    case 8: echo 'So sorry:  '; break;
    case 9: echo 'Bad news: '; break;
    case 10: echo "Well, here's a how-de-do.&nbsp; "; break;
    case 11: echo 'Ahem.&nbsp; '; break;
    case 12: echo 'Aaargh: '; break;
    case 13: echo "I'm sorry to say this, but &mdash; "; break;
    case 14: echo '<em>(Coughs politely.)</em>&nbsp; '; break;
    case 15: echo 'Oh, crumbs.&nbsp; '; break;
    case 16: echo 'Tsk!&nbsp; '; break;
    case 17: echo 'Tut, tut.&nbsp; '; break;
    case 18: echo "That just won't do.&nbsp; "; break;
    case 19: case 20: echo "I'm sorry: "; break;
    default: break;
  }
  echo "$msg</p>\n";
  $localtime = localtime(time(), TRUE);
  $drink = FALSE;
  if ($localtime['tm_hour'] == 18) {
    if ($localtime['tm_isdst']) $drink = 1; else $drink = 2;
  }
  unset($localtime);
  if (RESPONSE_CODE == 200) switch (mt_rand(0,100)) {
    case 0: echo "<p>Why don't you take a break?</p>\n"; break;
    case 1: echo "<p>Let's stop for " . ($drink ? ($drink==1 ? 'gin and tonics' : 'whisky' ) : 'tea') . ".&nbsp; Oh no, wait: I don't drink.</p>\n"; break;
    case 2: echo "<p>Oh, is that the time? <em>(Hint: " . ($drink ? ($drink==1 ? 'G &amp; T' : 'whisky' ) : 'tea') . ")</em></p>\n"; break;
    case 3: echo "<p>Isn't it time for " . ($drink ? ($drink==1 ? 'gin and tonics' : 'whisky' ) : 'tea') . ", anyway?</p>\n"; break;
    case 4: echo "<p>You know what?&nbsp; I think it's time for " . ($drink ? ($drink==1 ? 'gin &amp; tonics' : 'whisky' ) : 'tea') . ".</p>\n"; break;
    case 5: echo "<p>Why don't you have a " . ($drink ? ($drink==1 ? 'gin and tonic' : 'glass of whisky' ) : 'cup of tea') . ", and come back to this later?</p>\n"; break;
    default: break;
  }

  if ($goback) { ?>

  <script type="text/javascript">
  // <!-- [CDATA[
    document.write('<p><a href="javascript:history.back()">Go back</a></p>');
  // ]] -->
  </script>
<?php }

  if (! defined('NO_ERROR_LOG') and $logging and ($errno & (E_USER_ERROR | E_USER_NOTICE | E_USER_WARNING | E_USER_DEPRECATED))) {
    if (! (isset($user) and is_array($user))) {
      if (empty($_REQUEST['user_id']) and defined('AUTHUID')) $_REQUEST['user_id'] = AUTHUID;
      if (! empty($_REQUEST['user_id'])) include_once BASEURL . 'php/identify/user.php';
    }
    mail('alexander@taxrabbit.co.uk', 'error' . (RESPONSE_CODE == 200 ? '' : ' ' . RESPONSE_CODE),
         "user = {$user['user_name']}, email = {$user['email']}\n" .
         "REQUEST_URI = {$_SERVER['REQUEST_URI']}\n" .
         "HTTP_REFERER = {$_SERVER['HTTP_REFERER']}\n" .
         html_entity_decode($msg, ENT_COMPAT, 'UTF-8') . "\n" .
         ($_SERVER['REQUEST_METHOD'] == 'POST' ? "\n" . var_export($_POST, TRUE) : ''),
         'From: webserver@taxrabbit.co.uk' . "\r\n"
       . 'Reply-To: alexander@taxrabbit.co.uk' . "\r\n"
       . 'Content-Type: text/plain; charset="UTF-8"');
  } elseif (defined('NO_ERROR_LOG') and $user_id = 1 and $_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<pre>\n\$_POST = ";
    var_dump($_POST);
    echo "</pre>\n";
  }

  if ($errno & (E_USER_NOTICE | E_USER_DEPRECATED)) return TRUE;
  include BASEURL . 'php/footer.php';
  die();
}
// end of friendly_error definition

// start of close_xhtml_tag definition
define('CLOSE_LAST_XHTML_TAG', TRUE);
define('CLOSE_FIRST_XHTML_TAG', FALSE);

// $tag can be an integer, a string, or an array of strings.
// If it is an integer, then <html> has an index of 0, <body>
// has an index of 1 and so on. Specify CLOSE_LAST_XHTML_TAG
// to close only the last occurence of any of the tag(s)
// specified. Pass 1 for offset to close all children of the
// tag specified, 2 to close grandchildren, -1 to close the
// parent, and so on. Returns TRUE if the specified tag was
// found, and FALSE if it wasn't.
function close_xhtml_tag($tag, $last = CLOSE_FIRST_XHTML_TAG, $offset = 0) {
  global $open_xhtml_tags;

  // echo "<!--\n\$open_xhtml_tags = "; print_r($open_xhtml_tags);
  // echo "\$tag = $tag\n\$last = $last\n\$offset = $offset\n-->\n";

  if (count($open_xhtml_tags) == 0) return FALSE;

  if (is_int($tag)) {
    $index = $tag;
  } elseif (is_array($tag)) {
    $to_close = array_intersect($open_xhtml_tags, $tag);
    // $to_close is the intersection of the two arrays, with keys from $open_xhtml_tags
    if (count($to_close) == 0) return FALSE;
    if ($last) end($to_close); // sets the internal array pointer to the last element
    $index = key($to_close);   // returns the key of the current element
  } else {
    if ($last) {
      $index = array_search($tag, array_reverse($open_xhtml_tags));
      if ($index === FALSE) return FALSE;
      $index = count($open_xhtml_tags) - 1 - $index;
    } else {
      $index = array_search($tag, $open_xhtml_tags);
      if ($index === FALSE) return FALSE;
    }
  }
  $index += $offset;
  if ($index >= count($open_xhtml_tags)) return TRUE;
  if ($index < 0) {$index = 0;}

  // Don't indent for <html> or <body>, but do for <head>:
  $indent = count($open_xhtml_tags) - 2;
  if (count($open_xhtml_tags) >= 2 and $open_xhtml_tags[1] == 'body') {$indent --;}
  // $indent is allowed to be negative, in which case it counts as 0.

  $number = count($open_xhtml_tags) - $index;
  for ($i = 0; $i < $number; $i ++) {
    $current = array_pop($open_xhtml_tags);
    if ($current == 'body') echo "\n";
    if ($indent - $i > 0) echo str_repeat('  ', $indent - $i);
    echo "</$current>\n";
  }

  return TRUE;
}
// end of close_xhtml_tag definition

function html_indent($number) {
  echo str_repeat('  ', $number);
}

// $allowed is an array of acceptable pages (without query
// parameters) to redirect to if the referer starts with
// one of these. $default is the full address (with query
// parameters) to redirect to if it doesn't. Returns either
// $default or basename($_SERVER['HTTP_REFERER']).
function goto_address($default, $allowed) {
  $allow = FALSE;
  if (!empty($_SERVER['HTTP_REFERER'])) {
    $basename = basename($_SERVER['HTTP_REFERER']);
    foreach($allowed as $try) {
      if (strlen($basename) < strlen($try)) continue;
      if (substr_compare($basename, $try, 0, strlen($try)) === 0)
        {$allow = TRUE; break;}
    }
  }
  if ($allow) return htmlspecialchars(str_replace('&return=1', '', basename($_SERVER['HTTP_REFERER'])));
  else return $default;
}

function insert_hidden_fields($goto_address) {
  global $headings, $navbox, $urls, $hiddenfields;
  foreach ($headings as $k => $v) {
    echo '    <input type="hidden" name="headings[', $k, ']" value="', htmlspecialchars($v, ENT_QUOTES, 'UTF-8'), "\" />\n";
  }
  echo '    <input type="hidden" name="navbox" value="', $navbox, "\" />\n";
  foreach ($urls as $k => $v) {
    echo '    <input type="hidden" name="urls[', $k, ']" value="', $v, "\" />\n";
  }
  if (! empty($hiddenfields)) echo "\n", $hiddenfields, "\n";
  echo '    <input type="hidden" name="goto" value="', $goto_address, "\" />\n\n";
}

function redirect($target) {
  if ($_SERVER['REQUEST_METHOD'] == 'POST' and $_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1')
    header("HTTP/1.1 303 See Other");
  // dirname is inconsistent and only sometimes leaves a slash at the end
  // hence rtrim(dir, '/') . '/'
  header('Location: https://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']),'/').'/'.$target);
  exit;
}

// end of function definitions


// register error handling function
set_error_handler('jada_handle_error');

if (!defined('NO_DATABASE')) {
  // Connect to database
  $db_connection = @new mysqli('127.0.0.1', 'taxrabbit', 'JPLdnuDJmWXX', 'taxrabbit');
  if (mysqli_connect_errno()) {
    unset($db_connection);
    define('RESPONSE_CODE', 500);
    header('HTTP/1.1 500 Internal Server Error');
    trigger_error("I'm having trouble connecting to the database.&nbsp; "
    . "Perhaps the MySQL server\n     is not set up correctly?&nbsp; The error "
    . "message is:<br />\n     " . htmlify(mysqli_connect_error()), E_USER_ERROR);
  }
  $db_connection->set_charset("utf8");
}
