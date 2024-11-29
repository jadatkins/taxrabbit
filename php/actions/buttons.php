<?php

if (array_key_exists('btn_contact_chg', $_POST) or
    array_key_exists('btn_contact_new', $_POST) or
    array_key_exists('btn_contact_del', $_POST) or
    array_key_exists('btn_contact_gto', $_POST))
  require './php/actions/buttons/contact.php';

if (array_key_exists('btn_client_chg', $_POST) or
    array_key_exists('btn_client_new', $_POST) or
    array_key_exists('btn_client_del', $_POST))
  require './php/actions/buttons/client.php';

if (array_key_exists('btn_job_cli_gto', $_POST) or
    array_key_exists('btn_job_con_chg', $_POST) or
    array_key_exists('btn_job_con_new', $_POST) or
    array_key_exists('btn_job_con_gto', $_POST) or
    array_key_exists('btn_job_exp_chg', $_POST) or
    array_key_exists('btn_job_exp_new', $_POST) or
    array_key_exists('btn_job_exp_gto', $_POST))
  require './php/actions/buttons/job.php';

if (array_key_exists('btn_expense_chg', $_POST) or
    array_key_exists('btn_expense_new', $_POST))
  require './php/actions/buttons/expense.php';
