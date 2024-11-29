<?php

require_once './php/essentials.php';
//include_once './php/nocache.php';
require_once './php/identify/user.php';
$headings = array($user['user_name']);
$navbox = TRUE;
$urls = array("user.php?user_id=$user_id");
require_once './php/identify/business.php';
$headings[] = $business['business_name'];

require_once './php/header_one.php';

?>
  <meta name="viewport" content="width=device-width" />
  <style type="text/css">
  /* <![CDATA[ */
    ul {list-style-type: none;}
    li {margin-bottom: 0.4em;}
  /* ]]> */
  </style>
<?php

require_once './php/header_two.php';

?>
  <h3>Business Details</h3>

  <p>Click on the following link to view or change basic details such as the
     business address.</p>

  <ul>
    <li><a href="business_details.php<?php echo $unique_id; ?>">Business details</a></li>
  </ul>

  <h3>Clients (Income Groups) &amp; Address Book</h3>

  <p>Click the first to organise clients and recurring jobs into categories and
     sub-categories.&nbsp; The second will take you to the address book for the
     user <?php echo $user_name; ?>, which means that you will have to leave the
     business pages for the business <?php echo $business_name; ?>.</p>

  <ul>
    <li><a href="clients.php<?php echo $unique_id; ?>">Clients (groups of jobs)</a></li>

    <li><a href="contacts.php?user_id=<?php echo $user_id; ?>">Address Book</a></li>
  </ul>

  <h3>Accounts</h3>

  <p>Click on any of the links below to perform the associated task.&nbsp; Use
     the search options to find a single entry (for example, to update it) or
     to produce a summarised list of entries (for example, to produce a list of
     jobs for which you have not yet been paid).</p>

  <ul>
    <li><a href="commission.php<?php echo $unique_id; ?>&amp;job_id=newj">Create a new job</a></li>

    <li><a href="search_in.php<?php echo $unique_id; ?>">Search income</a></li>

    <li><a href="expense.php<?php echo $unique_id; ?>&amp;expense_id=newe">Create a new expense</a></li>

    <li><a href="search_out.php<?php echo $unique_id; ?>">Search expenses</a></li>
  </ul>

  <h3>One Place to do Everything</h3>

  <p>View all accounts:</p>

  <ul>
    <li><a href="diary.php<?php echo $unique_id; ?>">by month</a> or</li>

    <li><a href="diary.php<?php echo $unique_id, '&amp;year=',
        ( date('md') > '0405' ? idate('Y') + 1 : date('Y') ); ?>">by year</a></li>
  </ul>

  <p>At the bottom of the page, you will find links to create new jobs and expenses.</p>
<?php include './php/footer.php';
