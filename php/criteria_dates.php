<?php

if (! empty($_REQUEST['after']) or ! empty($_REQUEST['before'])) {
  $date_criterion = array(null, null);
  $year = array(TRUE, FALSE);
  $h3_date = array(FALSE, FALSE);
  $date_criterion[0] = $_REQUEST['before'];
  $date_criterion[1] = $_REQUEST['after'];
  for ($j=0; $j<2; $j++) {
    if (preg_match('/^(\d{4})-(\d\d)-(\d\d)$/', $date_criterion[$j], $sqldate)) {
      $year[$j] = intval($sqldate[1]); $month = intval($sqldate[2]); $day = intval($sqldate[3]);
      $h3_date[$j] = date('j F' . ($year[0] === $year[1] ? '' : ' Y'), mktime(6, 0, 0, $month, $day, $year[$j]));
    }
    unset($sqldate, $month, $day);
  }
  unset($date_criterion, $j, $year);
  
  echo '  <h3 style="margin: 0 0 0.5em 0;">';
  if ($h3_date[1] and $h3_date[0]) echo "{$h3_date[1]} ~ {$h3_date[0]}";
  elseif ($h3_date[1]) echo "From {$h3_date[1]}";
  elseif ($h3_date[0]) echo "Up to {$h3_date[0]}";
  echo "</h3>\n";
}
