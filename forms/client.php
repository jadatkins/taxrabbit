<?php

define('BASEURL', '../');
require_once BASEURL . 'php/essentials.php';
include_once BASEURL . 'php/nocache.php';
require_once BASEURL . 'php/identify/user.php';
require_once BASEURL . 'php/identify/business.php';

define('PLAIN_FORM', TRUE);
$new = TRUE;
$client_id = uniqid('cli', TRUE);
$client['client_name'] = 'New Client';
$client['parent_id'] = NULL;
$saveall = TRUE;

require '../php/forms/client.php';
