<?php

define('BASEURL', '../');
require_once BASEURL . 'php/essentials.php';
$user_id = $_REQUEST['user_id'];
$business_id = $_REQUEST['business_id'];

define('PLAIN_FORM', TRUE);
$new = TRUE;
$expense_id = uniqid('exp', TRUE);
$expense['expense_title'] = 'New Expense';
$expense['job_id'] = NULL;
$saveall = TRUE;

require '../php/forms/expense.php';
