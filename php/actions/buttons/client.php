<?php

function btn_cli_go_nowhere() {
  global $user_id, $business_id, $client_id;
  if (array_key_exists('HTTP_REFERER', $_SERVER) and
      !empty($_SERVER['HTTP_REFERER'])
      and substr($_SERVER['HTTP_REFERER'], -10) != 'action.php') {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
  } else {
    $_POST['goto'] = "client.php?user_id=$user_id&business_id=$business_id&client_id=$client_id";
  }
}

$temp = array_keys($_POST['clients']);
$client_id = $temp[0];
$client = $_POST['clients'][$client_id];
unset($temp);

if (array_key_exists('user_id', $client)) $user_id = $client['user_id'];
if (array_key_exists('business_id', $client)) $business_id = $client['business_id'];

// replace $client_id (if alias) with the real ID number from auto_increment
if (array_key_exists('client_id', $client))
  $client_id = $client['client_id'];
// see action_clean_input() in /php/actions/common.php


if (array_key_exists('btn_client_chg', $_POST)) {
  $_POST['goto'] = "client_contacts.php?user_id=$user_id&business_id=$business_id&client_id=$client_id";
}

if (array_key_exists('btn_client_new', $_POST)) {
  $need_more_information = TRUE;
  if (empty($moreinfo_sentence))
    $moreinfo_sentence = 'Please';
  else
    $moreinfo_sentence .= "</p>\n<p>Also";
  $moreinfo_sentence .= " enter the details for the new contact that you wish to associate with $client_name.";
  $realcontact_id = 'nrco' . alphanum_counter();
  $metaco_id = 'meta' . alphanum_counter();
  $more_realcontacts[] = array('user_id' => $client['user_id'],
                        'realcontact_id' => $realcontact_id);
  $hiddenfields .= "
    <input type=\"hidden\" name=\"metacontacts[$metaco_id][user_id]\" value=\"{$client['user_id']}\" />
    <input type=\"hidden\" name=\"metacontacts[$metaco_id][business_id]\" value=\"{$client['business_id']}\" />
    <input type=\"hidden\" name=\"metacontacts[$metaco_id][client_id]\" value=\"$client_id\" />
    <input type=\"hidden\" name=\"metacontacts[$metaco_id][realcontact_id]\" value=\"$realcontact_id\" />\n";

  // see /php/forms/join.php
  $existing_related = array('key' => "$user_id $business_id $client_id",
                            'name' => $client['client_name']);

  if (substr($_POST['goto'], 0, 14) != 'commission.php')
    $_POST['goto'] = "client.php?user_id=$user_id&business_id=$business_id&client_id=$client_id";
}
