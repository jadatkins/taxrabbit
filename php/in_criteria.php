<?php

foreach($_REQUEST as &$input)
  {if (is_string($input)) treat_input_string($input);}

// user_id, business_id
$where_clause = 'WHERE user_id='.wrap_for_sql($user_id).' AND business_id='.wrap_for_sql($business_id);

// clients
if (!empty($_REQUEST['clients'])) {
  $where_clause .= ' AND (';
  foreach ($_REQUEST['clients'] as $getclientid) {
    if ($getclientid == '')
      $where_clause .= 'client_id IS NULL OR ';
    else
      $where_clause .= 'client_id='.wrap_for_sql($getclientid).' OR ';
  }
  $where_clause = substr($where_clause, 0, -4) . ')';
}

// date
if (!empty($_REQUEST['datetype']) and $_REQUEST['datetype'] == 'paid')
  $datetype = 'date_paid';
else
  $datetype = 'date';
if (!empty($_REQUEST['after']) or !empty($_REQUEST['before'])) {
  require_once './php/intestines/dates.php';
  $where_clause .= ' AND ';
  if (!empty($_REQUEST['nodate']))
    $where_clause .= "( $datetype IS NULL OR ( ";
  if (!empty($_REQUEST['after'])) {
    digest_date($_REQUEST['after'], $user_id, '‘on or after’ date');
    $where_clause .= "$datetype>=CAST(".wrap_for_sql($_REQUEST['after']).' AS DATE)';
    if (!empty($_REQUEST['before'])) $where_clause .= ' AND ';
  }
  if (!empty($_REQUEST['before'])) {
    digest_date($_REQUEST['before'], $user_id, '‘on or before’ date');
    $where_clause .= "$datetype<=CAST(".wrap_for_sql($_REQUEST['before']).' AS DATE)';
  }
  if (!empty($_REQUEST['nodate']))
    $where_clause .= ' ) )';
} else {
  if (empty($_REQUEST['nodate']))
    $where_clause .= " AND $datetype IS NOT NULL";
}

// payment status
if (array_key_exists('paid', $_REQUEST) and count($_REQUEST['paid']) == 1 and
    ($_REQUEST['paid'][0] == 'yes' or $_REQUEST['paid'][0] == 'no')) {
  $where_clause .= ' AND date_paid IS' . ($_REQUEST['paid'][0] == 'no' ? '' : ' NOT') . ' NULL';
}

// fee
if (array_key_exists('least', $_REQUEST) or !empty($_REQUEST['most'])) {
  require_once './php/intestines/decimals.php';
  if (!empty($_REQUEST['least']) or substr($_REQUEST['least'],0,1) === '0') {
    digest_decimal($_REQUEST['least'], 'minimum fee');
    $where_clause .= ' AND fee>='.wrap_for_sql($_REQUEST['least']);
  }
  if (!empty($_REQUEST['most']) or substr($_REQUEST['most'],0,1) === '0') {
    digest_decimal($_REQUEST['most'], 'maximum fee');
    $where_clause .= ' AND fee<='.wrap_for_sql($_REQUEST['most']);
  }
}

// search
if (!empty($_REQUEST['words'])) {
  $where_clause .= " AND MATCH(job_title";
  if (array_key_exists('domain', $_REQUEST) and $_REQUEST['domain'] == 'both')
    $where_clause .= ',notes';
  $where_clause .= ") AGAINST(";
  treat_input_string($_REQUEST['words']);
  $where_clause .= wrap_for_sql($_REQUEST['words']);
  $where_clause .= " IN NATURAL LANGUAGE MODE)";
}

// order by
$order_clause = '';
if (!empty($_REQUEST['sort']) and is_array($_REQUEST['sort'])) {
  ksort($_REQUEST['sort']);
  $orderby_cols = '';
  foreach (array_keys($_REQUEST['sort']) as $i) {
    if (! preg_match('/^[a-z_]+( (A|DE)SC)?$/', $_REQUEST['sort'][$i]))
      unset($_REQUEST['sort'][$i]);
  }
  $orderby_cols = implode(', ', $_REQUEST['sort']);
}
if (!empty($orderby_cols))
  $order_clause = "ORDER BY $orderby_cols";
