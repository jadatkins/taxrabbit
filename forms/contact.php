<?php

define('BASEURL', '../');
define('NO_DATABASE', TRUE);
require_once BASEURL . 'php/essentials.php';
$user_id = $_REQUEST['user_id'];

define('PLAIN_FORM', TRUE);
$new = TRUE;
$realcontact_id = uniqid('con', TRUE);
$realcontact['fullname'] = 'New Contact';
$saveall = TRUE;

require '../php/forms/contact.php';
