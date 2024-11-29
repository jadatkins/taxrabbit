<?php

require_once './php/essentials.php';
$user_id = NULL;
require_once './php/identify/authenticate.php';
redirect('user.php?user_id=' . AUTHUID);
