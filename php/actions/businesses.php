<?php

foreach ($_POST['businesses'] as $business_id => &$business) {
  action_clean_input(
    $business,
    array('user_id' => 'users'),
    array('birth', 'death', 'business_name', 'descr', 'address', 'postcode')
  );

  action_isnew(
    $business, $business_id, 'business_id', 'business', 'businesses',
    array('user_id' => $business['user_id'])
  );

  $business_name = '';
  $business_name_ybox = '';
  action_name_or_title(
    $business, $business_id, $business_name, $business_name_ybox,
    array('user_id' => $business['user_id'], 'business_id' => $business_id),
    'business', 'businesses', 'business_name', 'name'
  );

  // validation for all businesses, new and old
  action_check_id($business, $business_name, 'user', 'users', TRUE);
  if (! preg_match('/^[ -~]*$/', $business['postcode']))
    friendly_error("&lsquo;{$business['postcode']}&rsquo; isn't a valid " .
                   'postcode.&nbsp; Please go back and change it.', TRUE);
  action_fix_date($business, 'birth', 'start date');
  action_fix_date($business, 'death', 'end date');

  foreach (array('bookday', 'book_day', 'book_month') as $foo)
  if (preg_match('/^\d+$/', $business[$foo]))
    settype($business[$foo], 'integer');
  else
    unset($business[$foo]);
  if (array_key_exists('book_day', $business) and array_key_exists('book_month', $business)) {
    $business['bookdate'] = "2000-{$business['book_month']}-{$business['book_day']}";
  }

  action_do_sql(
    $business, $business_id, 'business_id', 'businesses',
    array('user_id', 'business_id'),
    array('birth', 'death', 'business_name', 'descr', 'address', 'postcode', 'bookday', 'bookdate'),
    $business_name, $business_name_ybox
  );
}
