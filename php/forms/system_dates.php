<?php

function print_system_dates($record, $id_suffix) {
  $utc = new DateTimeZone('UTC');
  $dawn = new DateTime('2021-09-14T00:00:00+00:00');
  $created = new DateTime($record['created'], $utc);
  $updated = new DateTime($record['updated'], $utc);

  if ($created > $dawn) { ?>
        Created: <span id="created<?php echo $id_suffix; ?>"></span>
        <script type="text/javascript">
          document.getElementById('created<?php echo $id_suffix; ?>').innerText = new Date('<?php echo $created->format(DATE_RFC3339) ?>').toLocaleString();
        </script>
  <?php }

  if ($created > $dawn && $updated > $dawn) { ?>
        &#x2000;&bullet;&#x2000;
  <?php }

  if ($updated > $dawn) { ?>
        Updated: <span id="updated<?php echo $id_suffix; ?>"></span>
        <script type="text/javascript">
          document.getElementById('updated<?php echo $id_suffix; ?>').innerText = new Date('<?php echo $updated->format(DATE_RFC3339) ?>').toLocaleString();
        </script>
  <?php }
}
