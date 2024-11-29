<?php

function unlikely_month($given_year, $given_month) {
  $given = (12 * $given_year) + $given_month;
  $today = (12 * idate('Y')) + idate('m');
  return (($given < $today - 4) or ($given > $today + 1));
}

function digest_date(&$input, $user_id, $date_name = 'date', $ajax = FALSE, $format = '') {
  // I expect something like 'date of commencement' for $date_name
  // global $db_connection;

  if ($input == '')
    {if ($ajax) return FALSE; else return ($input = NULL);}

  // Before we do anything else, check whether the date is already in SQL format:
  if (preg_match('/^(\d{4})-(\d\d)-(\d\d)$/', $input, $sqldate)) {
    $year = intval($sqldate[1]); $month = intval($sqldate[2]); $day = intval($sqldate[3]);

    // Check to see if the user has entered 31st February or something like that:
    if (! checkdate($month, $day, $year)) {
      if ($ajax) return FALSE;
      friendly_error("There is no such date as $input in the Gregorian calendar.&nbsp; Please
     go back and enter a date that makes sense" . ($date_name == 'date' ? '' : " for the $date_name") . '.', TRUE);
    }

    if ($ajax)
      return date($format, mktime(12, 0, 0, $month, $day, $year));
    else
      return $input;
  }

  // Remove 'st', 'nd', 'th', etc:
  $text = preg_replace('/(?<=\d)(st|nd|rd|th)/', '', $input);

  // Ok, now see if I can understand the date:
  if (
    preg_match('/^\D*(0*\d{1,2})\D(0*\d{1,2})(\D(0*\d{2,4}))?\D*$/', $text, $date_array)
    // $date_array now looks like array('21/4/86', '21', '4', '/86', '86')
  and
    $date_array[1] <= 31
  and
    $date_array[2] <= 12
  ) {
    // Yes, I can.  I will assume it is in the British format.
    $day = intval($date_array[1]); $month = intval($date_array[2]);
    
    if (array_key_exists(4, $date_array)) {
      $year = $date_array[4];
    } else {
      // If no year was entered, assume it is at least four months ago and no
      // more than one month in the future, or complain about ambiguity.
      if     ($month <= 4 and idate('m') >= 9) $year = idate('Y') + 1;
      elseif ($month >= 9 and idate('m') <= 4) $year = idate('Y') - 1;
      else $year = idate('Y');

      if (unlikely_month($year, $month)) {
	if ($ajax) return FALSE;
	friendly_error("I'm not sure which year you mean, when you say &lsquo;$input&rsquo;
     " . ($date_name == 'date' ? '' : " for the $date_name") . '.&nbsp; Please give a year.', TRUE);
      }
    }

    if ($year <  70) $year += 2000;  // If a two-digit year was entered, take it as being
    if ($year < 100) $year += 1900;  // between 1970 and 2069, for consistency with strtotime

    // Check to see if the user has entered 31st February or something like that:
    if (! checkdate($month, $day, $year)) {
      if ($ajax) return FALSE;
      friendly_error("There is no such date as $input in the Gregorian calendar.&nbsp; Please
     go back and enter a date that makes sense" . ($date_name == 'date' ? '' : " for the $date_name") . '.', TRUE);
    }

    if ($ajax)
      return date($format, mktime(12, 0, 0, $month, $day, $year));

    // Now put the date into SQL format.
    $day   = str_pad($day  , 2, '0', STR_PAD_LEFT);  // Pad the date elements
    $month = str_pad($month, 2, '0', STR_PAD_LEFT);  // with zeroes as necessary.
    $year  = str_pad($year , 4, '0', STR_PAD_LEFT);
    $input = $year . '-' . $month . '-' . $day;
    return $input;
  }

  // I couldn't understand the date.  Let's see if PHP's strtotime() can:
  elseif (($timestamp = strtotime($text . ' noon')) === FALSE) {
    // Oh no, it couldn't.  Clearly the user is being unhelpful.
    if ($ajax) return FALSE;
    friendly_error("I don't understand the date &lsquo;$input&rsquo;.&nbsp; Please go
     back and enter the $date_name in a form that I can understand.", TRUE);
  } else {
    // Ok, I couldn't understand the date, but strtotime() could.

    // But maybe it's the wrong year? strtotime() assumes the current
    // year (if not specified) instead of the closest date.
    
    // Is the output of strtotime() a different year than we would guess?
    if (unlikely_month(idate('Y', $timestamp), idate('m', $timestamp))) {

      // Check whether the user mentioned the year that strtotime() found
      $mentions = substr_count($input, date('Y', $timestamp));
      if ($mentions < 1) $mentions = substr_count($input, 'month');
      if ($mentions < 1) $mentions = substr_count($input, 'year');
      if ($mentions < 1) $mentions = substr_count($input, 'week');
      if ($mentions < 1) $mentions = substr_count($input, 'day');
      if ($mentions < 1) {
	$test_string = str_ireplace(date('M', $timestamp), date('m', $timestamp), $input);
	$mentions = substr_count($test_string, date('y', $timestamp));
	if (idate('m', $timestamp) == idate('y', $timestamp)) $mentions --;
	if (idate('d', $timestamp) == idate('y', $timestamp)) $mentions --;
      }

      if ($mentions < 1) {
	// The year wasn't mentioned, so strtotime() must have guessed it
	$now = time(); $ts = $timestamp;
	if     ($timestamp - $now > 15552000)
	  $timestamp = mktime(idate('H',$ts),idate('i',$ts),0,idate('m',$ts),idate('d',$ts),idate('Y',$ts)-1);
	elseif ($now - $timestamp > 15552000)
	  $timestamp = mktime(idate('H',$ts),idate('i',$ts),0,idate('m',$ts),idate('d',$ts),idate('Y',$ts)+1);
	unset($now, $ts);
	  if ($ajax) return FALSE;
	  friendly_error("I'm not sure which year you mean, when you say &lsquo;$input&rsquo;
	 " . ($date_name == 'date' ? '' : " for the $date_name") . '.&nbsp; Please give a year.', TRUE);
      }

    }

    if ($ajax)
      return date('l jS F Y', $timestamp);

    /*/ write yellow-box message
    $db_connection->query("INSERT INTO messages (user_id,tie,msg) VALUES($user_id,"
	. yellowboxtie($user_id) . ",'I\'m taking ‘"
	. $db_connection->real_escape_string(truncate($input))
	. "’ to mean ". date('l jS F Y', $timestamp)
	. ($date_name == 'date' ? '' : ", for the $date_name") . ".')"); */
    $input = date('Y-m-d', $timestamp);
    return $input;
  }
}
