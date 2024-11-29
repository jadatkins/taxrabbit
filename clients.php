<?php

require_once './php/essentials.php';
include_once './php/nocache.php';
require_once './php/identify/user.php';
$headings = array($user['user_name']);
$navbox = TRUE;
$urls = array("user.php?user_id=$user_id");
require_once './php/identify/business.php';
array_push($headings, $business['business_name'], 'Clients');
$urls[] = "business.php$unique_id";

require_once './php/header_one.php';

?>
  <meta name="viewport" content="width=device-width" />
  <style type="text/css">
  /* <![CDATA[ */
    ul {padding-left: 16px; margin-top: 0;}
    li ul {margin-bottom: 0.5em;}
  /* ]]> */
  </style>
<?php

require_once './php/header_two.php';

?>
  <h3>About Clients</h3>

  <p>The client system provides a way to organise your commissions into
     categories, based on who the job is for or any other criteria that may be
     convenient.&nbsp; The &ldquo;clients&rdquo; in <?php echo APPNAME; ?> do
     not actually have to correspond to real people or organisations.&nbsp; For
     example, if you sometimes sell shoes and sometimes sell shirts, you could
     create a &ldquo;client&rdquo; for each.&nbsp; You can also create clients
     that belong to other clients, so, following on from the previous example,
     within shoes, you could create clients representing all the people you sell
     shoes to, and within shirts, you could create clients for all the people
     who buy shirts.</p>

  <h3>Client List</h3>

<?php

function getclients($parent_id = NULL, $htmlindent = 0, $recursion_level = 0) {
  if ($recursion_level > 599) {return;}
  global $db_connection, $user_id, $business_id, $unique_id;
  $any = FALSE;
  if ($parent_id === NULL) $parent_clause = ' IS NULL';
  else $parent_clause = "=$parent_id";
  $result = $db_connection->query("SELECT client_id,client_name FROM clients WHERE"
    . " user_id=$user_id AND business_id=$business_id AND parent_id$parent_clause")
    or trigger_error("I'm sorry, I seem to have encountered a problem while re"
      . "trieving the list of clients.&nbsp; The error message is:<br />\n     "
      . $db_connection->error, E_USER_ERROR);
  if ($row = $result->fetch_assoc()) {
    if ($recursion_level == 0) echo "  <p>Click on a client to view or " .
      "change the client's details or to add or remove contacts.</p>\n\n";
    html_indent($htmlindent);
    echo '<ul class="type', (int) fmod($recursion_level, 3) + 1, "\">\n";
    do {
      html_indent($htmlindent+1);
      echo "<li>\n";
      html_indent($htmlindent+2);
      echo "<a href=\"client.php$unique_id&amp;client_id=", $row['client_id'],
        '">', htmlify($row['client_name']), "</a>\n";
      getclients($row['client_id'], $htmlindent+2, $recursion_level+1);
      html_indent($htmlindent+1); echo "</li>\n";
    } while ($row = $result->fetch_assoc());
    html_indent($htmlindent); echo "</ul>\n";
    $any = TRUE;
  }
  $result->free();
  return $any;
}

if (! getclients(NULL, 1, 0))
  echo "  <p>There are no clients set up for $business_name yet.</p>\n";

?>

  <h3>New Client</h3>

  <p>Click on the following link to set up a new client or category.</p>

  <p><a href="client.php<?php echo $unique_id ?>&amp;client_id=ncli">New Client</a></p>
<?php include './php/footer.php';
