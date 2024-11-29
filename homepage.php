<?php

define('SUPPRESS_YBOX', TRUE);
define('HELPCONTACTINFO', TRUE);
define('INCL_CREDITS', TRUE);
define('NO_DATABASE', TRUE);
$headings = array();
$navbox = FALSE;
//include_once './php/nocache.php';
require_once './php/essentials.php';
require_once './php/header_one.php';
?>
  <meta name="viewport" content="width=device-width" />
  <meta name="google-site-verification" content="By5sDO3oOqVcoHLGSetkwPtz9gCLMdGigqBfYTSRk3Y" />
  <meta name="p:domain_verify" content="fddd3fb4af0c88271e322fdb12478ab2" />
  <meta name="msvalidate.01" content="3B7358202ABF620CDDB9B0FC41868686" />
<?php
require_once './php/header_two.php';

?>
<h3>About <?php echo APPNAME; ?></h3>

<p><?php echo APPNAME; ?> is a very simple bookkeeping system for sole traders
   (self-employed people) in the United Kingdom.&nbsp; It is intended mainly to
   take the pain out of self-assessment tax returns, with the additional aim of
   (optionally) helping you run your business by keeping track of which jobs you
   have or haven't been paid for (to help you chase up your debtors).</p>

<h3>Existing Users</h3>

<p>If you already have an account, click below to log in.</p>

<p><a href="user.php">Log in to <?php echo APPNAME; ?></a></p>

<p>Can't log in?&nbsp; Email
  <script type="text/javascript" language="javascript">
    <!--
    insert_detail(true);
    // -->
  </script>
  <noscript>
    <img src="images/contact.png" alt="me" style="position: relative; top: 1px;" />
  </noscript>
   or call <a href="tel:+441179112858">0117 911 2858</a>.</p>

<h3>New Users</h3>

<p>So, you think you'd like to try out <?php echo APPNAME; ?> for your own
   personal use?&nbsp; Great!&nbsp; Drop me a line (contact details below) and
   I'll get you set up.</p>

<h3>Links</h3>

<p>
  <a href="http://www.facebook.com/TaxRabbit"><?php echo APPNAME; ?> on Facebook</a>
</p>

<?php

include './php/footer.php';
