<?php

define('BASEURL', '../');
require_once BASEURL . 'php/essentials.php';
include_once BASEURL . 'php/nocache.php';
require_once BASEURL . 'php/identify/user.php';
require_once BASEURL . 'php/identify/business.php';

define('PLAIN_FORM', TRUE);
$new = TRUE;
$job_id = uniqid('job', TRUE);
$job['job_title'] = 'New Job';
$job['client_id'] = NULL;
$saveall = TRUE;

require '../php/forms/job.php';
