<?php

require_once './php/essentials.php';
include_once './php/nocache.php';
require_once './php/identify/user.php';
$headings = array($user['user_name']);
$navbox = TRUE;
$urls = array("user.php?user_id=$user_id");
require_once './php/identify/business.php';
array_push($headings, $business['business_name'], 'Income');
array_push($urls, "business.php$unique_id", "search_in.php$unique_id");
require_once './php/forms/join.php';
$saveall = TRUE;
require './php/identify/job.php';
$headings[] = html_entity_decode($job['job_title'], ENT_COMPAT, 'UTF-8');
$urls[] = "commission.php$unique_id&amp;job_id=$job_id";
$headings[] = "Contact";

require_once './php/header_one.php';
?>
  <style type="text/css">
  /* <![CDATA[ */
    ul { list-style-type: none; }
  /* ]]> */
  </style>
<?php
require_once './php/header_two.php';

require_once './php/forms/contact_radio.php';

?>
  <p>Who should be the main contact for the job &lsquo;<?php echo $job['job_title']; ?>&rsquo;?</p>

  <form accept-charset="UTF-8" name="frm_main" method="post" action="action.php" onsubmit="disable()">

<?php insert_hidden_fields("commission.php$unique_id&amp;job_id=$job_id"); ?>
    <input type="hidden" name="jobs[<?php echo $job_id; ?>][user_id]" value="<?php echo $user_id; ?>" />
    <input type="hidden" name="jobs[<?php echo $job_id; ?>][business_id]" value="<?php echo $business_id; ?>" />

    <fieldset class="main">
<?php draw_contact_radio(3); ?>

      <div class="savebutton">
    	<script type="text/javascript">
        // <!-- [CDATA[
          insertcancel();
        // ]] -->
        </script>

        <input type="submit" name="btn_save" value="Save" />
      </div>
    </fieldset>
  </form>
<?php include './php/footer.php';
