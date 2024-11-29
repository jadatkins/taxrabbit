<?php

foreach($_REQUEST as &$input)
  {if (is_string($input)) treat_input_string($input);}

// user_id, business_id
$where_clause = 'WHERE user_id='.wrap_for_sql($user_id).' AND business_id='.wrap_for_sql($business_id);

// categories
if (empty($_REQUEST['categories'])) {
  if (array_key_exists('catdomain', $_REQUEST)) {
    switch ($_REQUEST['catdomain']) {
      case 'catallow': $where_clause .= ' AND (cat_code IS NULL OR cat_code<31 OR cat_code>47)' ; break;
      case 'catdis':   $where_clause .= ' AND (cat_code IS NULL OR (cat_code>30 AND cat_code<48))'; break;
      default:         ;
    }
  }
} else {
  // read cats table
  if (! array_key_exists('catdomain', $_REQUEST)) $_REQUEST['catdomain'] = 'catboth';
  switch ($_REQUEST['catdomain']) {
    case 'catallow': $allow_phrase = ' WHERE allowable=TRUE' ; break;
    case 'catdis':   $allow_phrase = ' WHERE allowable=FALSE'; break;
    default:         $allow_phrase = '';
  }
  $cats = array();
  $result = $db_connection->query('SELECT * FROM cats' . $allow_phrase);
  if (! $result or !($row = $result->fetch_assoc()) )
    trigger_error("I'm having trouble translating the category information into the
   right format, as I can't pull some information from the database that I
   need.&nbsp; The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);
  do $cats[] = $row; while ($row = $result->fetch_assoc());
  $result->free();
  unset($result, $row);

  $where_clause .= ' AND (';
  foreach ($_REQUEST['categories'] as $descr_id) {
    if ($descr_id == '') $where_clause .= 'cat_code IS NULL OR ';
    else foreach ($cats as $cat) {
      if ($cat['descr_id'] == $descr_id)
        $where_clause .= 'cat_code=' . (int) $cat['cat_code'] . ' OR ';
    }
  }
  if (substr($where_clause, -4) == ' OR ')
    $where_clause = substr($where_clause, 0, -4) . ')';
  else
    $where_clause .= 'FALSE)';
}

// date
if (!empty($_REQUEST['after']) or !empty($_REQUEST['before'])) {
  require_once './php/intestines/dates.php';
  $where_clause .= ' AND ';
  if (!empty($_REQUEST['nodate']))
    $where_clause .= '( date IS NULL OR ( ';
  if (!empty($_REQUEST['after'])) {
    digest_date($_REQUEST['after'], $user_id, '‘on or after’ date');
    $where_clause .= "date>=CAST(".wrap_for_sql($_REQUEST['after']).' AS DATE)';
    if (!empty($_REQUEST['before'])) $where_clause .= ' AND ';
  }
  if (!empty($_REQUEST['before'])) {
    digest_date($_REQUEST['before'], $user_id, '‘on or before’ date');
    $where_clause .= "date<=CAST(".wrap_for_sql($_REQUEST['before']).' AS DATE)';
  }
  if (!empty($_REQUEST['nodate']))
    $where_clause .= " ) )";
} else {
  if (empty($_REQUEST['nodate']))
    $where_clause .= ' AND date IS NOT NULL';
}

// cost
if (array_key_exists('least', $_REQUEST) or !empty($_REQUEST['most'])) {
  require_once './php/intestines/decimals.php';
  if (!empty($_REQUEST['least']) or substr($_REQUEST['least'],0,1) === '0') {
    digest_decimal($_REQUEST['least'], 'minimum cost');
    $where_clause .= ' AND cost>='.wrap_for_sql($_REQUEST['least']);
  }
  if (!empty($_REQUEST['most']) or substr($_REQUEST['most'],0,1) === '0') {
    digest_decimal($_REQUEST['most'], 'maximum cost');
    $where_clause .= ' AND cost<='.wrap_for_sql($_REQUEST['most']);
  }
}

// search
if (!empty($_REQUEST['words'])) {
  $where_clause .= " AND MATCH(expense_title";
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
    if (! preg_match('/^[a-z_]+( (A|DE)SC)?(, [a-z_]+( (A|DE)SC)?)?$/', $_REQUEST['sort'][$i]))
      unset($_REQUEST['sort'][$i]);
  }
  $orderby_cols = implode(', ', $_REQUEST['sort']);
}
if (!empty($orderby_cols))
  $order_clause = "ORDER BY $orderby_cols";