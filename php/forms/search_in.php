<?php require_once './php/forms/client_ticks.php'; ?>
    <fieldset class="main">
      <h3 style="margin-top: 0;">Filter by word</h3>

      <p>Enter specific words to search for.&nbsp; Leave blank to list all jobs.</p>

      <p>
	<input type="search" name="words" size="65" />
	<input type="submit" name="btnsearch" value="Search" /><br />
	<input type="checkbox" id="chkdomain" name="domain" value="both" checked="checked"
	  /><label for="chkdomain"> Search in notes as well as job title</label>
      </p>

      <h3>Display order</h3>

      <p>
	Sort by
	<select name="sort[]">
	  <option></option>
	  <option value="date ASC" selected="selected">Date (oldest first)</option>
	  <option value="date DESC">Date (latest first)</option>
	  <option value="client_id">Client</option>
	  <option value="fee ASC">Fee (ascending)</option>
	  <option value="fee DESC">Fee (descending)</option>
	</select>
	then
	<select name="sort[]">
	  <option selected="selected"></option>
	  <option value="date ASC">Date (oldest first)</option>
	  <option value="date DESC">Date (latest first)</option>
	  <option value="client_id">Client</option>
	  <option value="fee ASC">Fee (ascending)</option>
	  <option value="fee DESC">Fee (descending)</option>
	</select>
	then
	<select name="sort[]">
	  <option selected="selected"></option>
	  <option value="date ASC">Date (oldest first)</option>
	  <option value="date DESC">Date (latest first)</option>
	  <option value="client_id">Client</option>
	  <option value="fee ASC">Fee (ascending)</option>
	  <option value="fee DESC">Fee (descending)</option>
	</select>
      </p>

      <h3>Date range</h3>

      <div class="table">
	<div class="tablerow">
	  <div class="tablecell" style="min-width: 16em;">
	    <table>
	    <tbody>
	      <tr>
		<td><label for="after">On or after:</label></td>
		<td><input type="date" id="after" name="after" size="14" min="2011-01-01" max="<?php
echo idate('Y') + 1; ?>-04-05" /></td>
	      </tr>

	      <tr>
		<td><label for="before">On or before:</label></td>
		<td><input type="date" id="before" name="before" size="14" min="2011-01-01" max="<?php
echo idate('Y') + 1; ?>-04-05" /></td>
	      </tr>
	    </tbody>
	    </table>
	  </div>

	  <div class="tablecell">
	    <input type="radio" id="ddone" name="datetype" value="done" checked="checked" /><label for="ddone"> Date work done</label><br />
	    <input type="radio" id="dpaid" name="datetype" value="paid" /><label for="dpaid"> Date paid</label>
	  </div>
	</div>
      </div>

      <p>
	<input type="checkbox" id="dnull" name="nodate" checked="checked" /><label for="dnull"> Include jobs with no date</label>
      </p>

      <h3>Fee</h3>

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

      <h3>Payment status</h3>

      <div class="table">
	<div class="tablerow">
	  <div class="tablecell">Include:&nbsp;</div>
	  <div class="tablecell">
	    <input type="checkbox" id="pyes" name="paid[]" value="yes" checked="checked" /><label for="pyes"> Work that has been paid for</label><br />
	    <input type="checkbox" id="pno" name="paid[]" value="no" checked="checked" /><label for="pno"> Work that has not been paid for</label>
	  </div>
	</div>
      </div>

      <h3>Clients</h3>

      <p>Search specific clients by ticking them below.&nbsp;
	 (If you tick none, all clients will be included.)</p>

      <input type="checkbox" name="clients[]" value="" id="chknull" /> <label for="chknull">Jobs with no client</label>
      <hr style="text-align: left; width: 62%; margin-left: 0; margin-right: auto;" />

<?php draw_client_ticks(FALSE, 3); ?>

      <div class="savebutton">
	<input type="submit" name="btnsearch" value="Search" />
      </div>
    </fieldset>
