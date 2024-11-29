<?php

foreach ($_POST['expenses'] as $expense_id => &$expense) {
  if (! empty($expense['readonly']))
    continue;

  action_clean_input(
    $expense,
    array('user_id' => 'users', 'business_id' => 'businesses', 'job_id' => 'jobs'),
    array('expense_title', 'date', 'cost', 'mileage', 'notes')
  );

  action_isnew(
    $expense, $expense_id, 'expense_id', 'expense', 'expenses',
    array('user_id' => $expense['user_id'], 'business_id' => $expense['business_id'])
  );

  if (is_completely_blank($expense, array('expense_title', 'cat_code', 'date', 'cost', 'mileage', 'notes')))
    continue;

  $expense_name = '';
  $expense_name_ybox = '';
  action_name_or_title(
    $expense, $expense_id, $expense_name, $expense_name_ybox,
    array('user_id' => $expense['user_id'], 'business_id' => $expense['business_id'], 'expense_id' => $expense_id),
    'expense', 'expenses', 'expense_title', 'title'
  );

  // validation for all expenses, new and old
  action_check_id($expense, $expense_name, 'user', 'users', TRUE);
  action_check_id($expense, $expense_name, 'business', 'businesses', TRUE);
  // TODO: $job_doesnt_exist = ! action_check_id($expense, $expense_name, 'job', 'jobs', FALSE);

  action_fix_date($expense, 'date', 'date', TRUE);
  action_fix_decimal($expense, 'cost', 'cost');
  if (! empty($expense['date'])) {
    $_REQUEST['business_id'] = $expense['business_id'];
    require_once './php/identify/business.php';
    if (strcmp($expense['date'], FINALDATE) <= 0)
      trigger_error("You can't create expenses with dates before " .
          date('j F Y', mktime(9, 0, 0, substr(FINALDATE,5,2), substr(FINALDATE,8,2)+1, substr(FINALDATE,0,4)))
          . '.', E_USER_ERROR);
    if (strcmp($expense['date'], (idate('Y')+5).substr(BOOKDATE, 4)) > 0)
      trigger_error("You can't create expenses with dates after " .
          date('j F Y', mktime(9, 0, 0, substr(BOOKDATE,5,2), substr(BOOKDATE,8,2), idate('Y')+5))
          . '.', E_USER_ERROR);
  }

  if (array_key_exists('mileage', $expense)) {
    if ($expense['mileage'] == '') $expense['mileage'] = NULL;
    elseif (is_numeric($expense['mileage']) and $expense['mileage'] >= 0) {
      if ($expense['category'] != 3)
        friendly_error('You can\'t have a mileage unless the category is ' .
            '&lsquo;Car, van and travel expenses&rsquo;.&nbsp; Enter any ' .
            'other information into the &lsquo;Notes:&rsquo; field.', TRUE);
      if (!is_null($expense['cost']))
        friendly_error('Please fill in <em>either</em> the cost, <em>or</em> the mileage.' .
          '&nbsp; You can put any other information in the &lsquo;Notes:&rsquo; field.', TRUE);
      settype($expense['mileage'], 'float');
    } else friendly_error('The mileage (entered as &lsquo;'
      . $expense['mileage'] . '&rsquo;) is not in an acceptable form.&nbsp; An
     acceptable form would be something like &lsquo;156.238&rsquo; or &lsquo;1.56238e+2&rsquo;.', TRUE);
  }

  if (array_key_exists('category', $expense) and $expense['category'] === '') {
    $expense['cat_code'] = NULL;
    unset($expense['category']);
  }
  if (array_key_exists('category', $expense)) {
    if (! array_key_exists('disallowable', $expense)) $expense['disallowable'] = FALSE;
    settype($expense['category'], 'integer');
    $result = $db_connection->query('SELECT cat_code FROM cats WHERE descr_id=' . $expense['category']
      . ' AND allowable=' . ($expense['disallowable'] ? 'FALSE' : 'TRUE'));
    if (! $result or !($row = $result->fetch_row()) )
      trigger_error("I'm having trouble translating the category information into the
     right format, as I can't pull some information from the database that I
     need.&nbsp; The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);
    $expense['cat_code'] = $row[0];
    $result->free();
    unset($result, $row);
    if ($expense['cat_code'] == 28) {
      $expense['cat_code'] = 43;
      $db_connection->query('INSERT INTO messages (user_id,tie,msg) VALUES(' .
          $expense['user_id'] . ',' . yellowboxtie($expense['user_id']) . ",'Depreciation and loss/profit on sale of assets " .
          "are not allowable expenses, so I\'m putting this down as disallowable.')");
    }
  }
  // end of validation

  action_do_sql(
    $expense, $expense_id, 'expense_id', 'expenses',
    array('user_id', 'business_id', 'expense_id'),
    array('cat_code', 'expense_title', 'date', 'cost', 'mileage', 'job_id', 'notes'),
    $expense_name, $expense_name_ybox
  );
}
