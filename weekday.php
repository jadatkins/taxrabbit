<?php
//error_reporting(0);
date_default_timezone_set('Europe/London');
require './php/intestines/dates.php';
$result = digest_date($_REQUEST['text'], 0, 'date', TRUE, 'l');
if ($result) echo "($result)";
