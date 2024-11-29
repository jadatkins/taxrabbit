<?php

set_error_handler('dummy_handler');
header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html>
<head>
  <title><?php
if (defined('RESPONSE_CODE') and RESPONSE_CODE != 200 and RESPONSE_CODE != 'LOGOUT')
  echo RESPONSE_CODE, ' ', htmlspecialchars($headings[count($headings) - 1], ENT_COMPAT, 'UTF-8');
else {
  echo APPNAME;
  if (count($headings) >= 2) echo ' - ', htmlspecialchars($headings[count($headings) - 2], ENT_COMPAT, 'UTF-8');
  if (count($headings) >= 1) echo ' - ', htmlspecialchars($headings[count($headings) - 1], ENT_COMPAT, 'UTF-8');
}
?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="Content-Script-Type" content="text/javascript" />
  <link rel="author" href="https://profiles.google.com/103262464836488665431" />
  <link rel="stylesheet" type="text/css" href="stylesheets/main.css" />
  <link rel="stylesheet" media="screen" type="text/css" href="stylesheets/screen.css" />
  <link rel="stylesheet" media="print" type="text/css" href="stylesheets/print.css" />
  <script type="text/javascript" src="javascript/javascript.js"></script>
<?php

$open_xhtml_tags = array('html', 'head');

if (is_int(strpos($_SERVER['HTTP_USER_AGENT'], 'Windows')))
  echo '  <link rel="stylesheet" type="text/css" href="stylesheets/windows.css" />
';
if (! is_int(strpos($_SERVER['HTTP_USER_AGENT'], 'KHTML')) and is_int(strpos($_SERVER['HTTP_USER_AGENT'], 'Gecko')))
  echo '  <link rel="stylesheet" type="text/css" href="stylesheets/gecko.css" />
';
else
  echo '  <link rel="stylesheet" type="text/css" href="stylesheets/not_gecko.css" />
';

restore_error_handler();
