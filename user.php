<?php

define('HELPCONTACTINFO', TRUE);
$navbox = FALSE;
require_once './php/essentials.php';
//include_once './php/nocache.php';
require_once './php/identify/user.php';
$headings = array($user['user_name']);
$navbox = TRUE;
$urls = array();
require_once './php/header_one.php';
echo '  <meta name="viewport" content="width=device-width" />
';
require_once './php/header_two.php';

$result = $db_connection->query("SELECT * FROM businesses WHERE user_id=$user_id")
  or trigger_error("I couldn't retrieve the list of businesses.&nbsp; The error"
  . " message is:<br />\n     " . htmlify($db_connection->error), E_USER_ERROR);
if ($row = $result->fetch_assoc()) $has_business = TRUE; else $has_business = FALSE;

?>
  <h3>User Details</h3>

  <p>Change your name, email address, password or UTR.</p>

  <p><a href="profile.php<?php echo $unique_id; ?>">User details</a></p>

<?php
if ($has_business) { ?>
  <h3>Businesses</h3>

  <p>Select one of your businesses below to go to the accounts for that business.</p>

  <dl>
<?php
  do {
    echo '    <dt><a href="business.php?user_id=', $user_id, '&amp;business_id=',
      $row['business_id'], '">', htmlify(truncate($row['business_name'])), "</a></dt>\n";
    echo '    <dd class="desc">', htmlify(truncate($row['descr'], 85)), "</dd>\n";
  } while ($row = $result->fetch_assoc());
  echo "  </dl>\n\n";
}

$result->free();
unset($row, $result);

if (! $has_business) {
  ?>
  <h3>What to do Next</h3>

  <p>It looks like you're new to <?php echo APPNAME; ?>.&nbsp; Welcome!&nbsp; The
     next thing to do is to create a new business.&nbsp; Just click on &lsquo;New
     Business&rsquo; below.&nbsp; And remember to drop me a line if you have any
     problems or feature requests (contact details at the bottom of this page).</p>
<?php
}

?>
  <h3>New Business</h3>

  <p>Click on the following link to create a new set of accounts for a business.</p>

  <p><a href="business_details.php<?php echo $unique_id; ?>&amp;business_id=newb">New Business</a></p>

  <h3>Address Book</h3>

  <p>Here you can manage your contacts for all your businesses and clients.</p>

  <p><a href="contacts.php<?php echo $unique_id; ?>">Address Book</a></p>

  <h3>Log Out</h3>

  <p>To attempt to log out, click the link below and then click &lsquo;Cancel&rsquo;
     when asked for a username and password.&nbsp; It may or may not work.&nbsp;
     To log out properly, quit your web browser (close all internet windows and
     tabs).</p>

  <p><a href="logout.php">Log out</a></p>
<?php include './php/footer.php';
