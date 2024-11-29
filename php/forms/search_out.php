    <fieldset class="main">
      <h3 style="margin-top: 0;">Filter by word</h3>

      <p>Enter specific words to search for.&nbsp; Leave blank to list all expenses.</p>

      <p>
        <input type="search" name="words" size="65" />
        <input type="submit" name="btnsearch" value="Search" /><br />
        <input type="checkbox" id="chkdomain" name="domain" value="both" checked="checked"
          /><label for="chkdomain"> Search in notes as well as expense title</label>
      </p>

      <h3>Display order</h3>

      <p>
        Sort by
        <select name="sort[]">
          <option></option>
          <option value="date ASC" selected="selected">Date (oldest first)</option>
          <option value="date DESC">Date (latest first)</option>
          <option value="cat_code">Category</option>
          <option value="cost ASC, mileage ASC">Cost (ascending)</option>
          <option value="cost DESC, mileage DESC">Cost (descending)</option>
          <option value="job_id">Associated job</option>
        </select>
        then
        <select name="sort[]">
          <option selected="selected"></option>
          <option value="date ASC">Date (oldest first)</option>
          <option value="date DESC">Date (latest first)</option>
          <option value="cat_code">Category</option>
          <option value="cost ASC, mileage ASC">Cost (ascending)</option>
          <option value="cost DESC, mileage DESC">Cost (descending)</option>
          <option value="job_id">Associated job</option>
        </select>
        then
        <select name="sort[]">
          <option selected="selected"></option>
          <option value="date ASC">Date (oldest first)</option>
          <option value="date DESC">Date (latest first)</option>
          <option value="cat_code">Category</option>
          <option value="cost ASC, mileage ASC">Cost (ascending)</option>
          <option value="cost DESC, mileage DESC">Cost (descending)</option>
          <option value="job_id">Associated job</option>
        </select>
      </p>

      <h3>Date range</h3>

      <div class="table">
	<div class="tablerow">
	  <div class="tablecell" style="min-width: 15em;">
            <label for="after">On or after:</label>
            <input type="date" id="after" name="after" size="14" min="2011-01-01" max="<?php
echo idate('Y') + 1; ?>-04-05" />
	  </div>

	  <div class="tablecell">
            <label for="before">On or before:</label>
            <input type="date" id="before" name="before" size="14" min="2011-01-01" max="<?php
echo idate('Y') + 1; ?>-04-05" />
	  </div>
	</div>
      </div>

      <p>
	<input type="checkbox" id="dnull" name="nodate" checked="checked" /><label for="dnull"> Include expenses with no date</label>
      </p>

      <h3>Cost</h3>

      <div class="table">
	<div class="tablerow">
	  <div class="tablecell" style="min-width: 15em;">
            <label for="least">At least:</label>
            &pound; <input type="number" id="least" class="numeric" name="least" size="9" step="0.01" placeholder="0.00" />
          </div>

          <div class="tablecell">
            <label for="most">At most:</label>
            &pound; <input type="number" id="most" class="numeric" name="most" size="9" step="0.01" placeholder="0.00" />
          </div>
	</div>
      </div>

      <h3>Categories</h3>

      <p>Search specific categories by selecting them below.&nbsp;
         (If you select none, all categories will be included.)</p>

      <div class="column" style="margin-right: 1em; margin-bottom: 0.5em;">
<?php

$open_xhtml_tags[] = 'fieldset';

$result = $db_connection->query('SELECT * FROM cat_descriptions');
if (! $result)
  trigger_error("I couldn't retrieve the list of category descriptions from the " .
    "database.&nbsp; The error message is:<br />\n     " . $db_connection->error, E_USER_ERROR);
echo '        <select id="categories" name="categories[]" size="', $result->num_rows + 4, '" multiple="multiple">
          <option value="">Expenses with no category</option>
          <option disabled="disabled" value=""></option>
          <optgroup label="Business expenses">', "\n";
while ($cat_descr = $result->fetch_assoc()) {
  if ($cat_descr['descr_id'] == 14) {
    echo '          </optgroup>', "\n";
    echo '          <optgroup label="Capital allowances">', "\n";
  }
  echo '            <option value="', $cat_descr['descr_id'], '">', $cat_descr['descr_text'], "</option>\n";
}
$result->free();
unset($result, $cat_descr);

?>
          </optgroup>
        </select>
      </div>

      <p>
        Include:<br />
        <input type="radio" id="allexp" name="catdomain" value="catallow" /><label for="allexp"> Allowable expenses</label><br />
        <input type="radio" id="disexp" name="catdomain" value="catdis" /><label for="disexp"> Disallowable (personal) expenses</label><br />
        <input type="radio" id="botexp" name="catdomain" value="catboth" checked="checked" /><label for="botexp"> Both</label>
      </p>

      <p><strong>Tip:</strong> Use your <?php echo (is_int(strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh')) ? 'Command (&#8984;)' : 'Ctrl' );
         ?> key to select multiple categories.</p>

      <script type="text/javascript">
      // <!-- [CDATA[
        function selectall() {
          var i, lines = document.getElementById('categories').options;
          for (i in lines) {lines[i].selected = true;}
          lines[1].selected = false;
          document.getElementById('categories').focus();
        }
        if (navigator.userAgent.indexOf('Safari') == -1 && navigator.userAgent.indexOf('MSIE') == -1 && navigator.userAgent.indexOf('Opera') == -1) {
          document.write('<input type="button" class="button" value="Select All" onclick="selectall()" />');
        }
      // ]] -->
      </script>

      <div class="savebutton">
        <input type="submit" name="btnsearch" value="Search" />
      </div>
<?php close_xhtml_tag('fieldset');
