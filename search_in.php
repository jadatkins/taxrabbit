<?php

require_once './php/essentials.php';
require_once './php/identify/user.php';
$headings = array($user['user_name']);
$navbox = TRUE;
$urls = array("user.php?user_id=$user_id");
require_once './php/identify/business.php';
array_push($headings, $business['business_name'], 'Search Income');
$urls[] = "business.php$unique_id";

require_once './php/header_one.php';

?>
  <style type="text/css">
  /* <![CDATA[ */
    p {margin-bottom: 0.75em;}
    ul {list-style-type: none; margin-top: 0;}
    li ul {margin-bottom: 0.5em;}
  /* ]]> */
  </style>
<?php

require_once './php/header_two.php';

?>
  <form accept-charset="UTF-8" name="formname" method="get" action="find_in.php" onsubmit="disable('document.formname.search')">

    <input type="hidden" name="user_id" value="<?php echo $_GET['user_id']; ?>" />
    <input type="hidden" name="business_id" value="<?php echo $_GET['business_id']; ?>" />

<?php require './php/forms/search_in.php'; ?>
  </form>
<?php include './php/footer.php';
