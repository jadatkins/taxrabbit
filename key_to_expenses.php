<?php

define('SUPPRESS_YBOX', TRUE);
define('WIDE_PAGE', TRUE);
define('NO_DATABASE', TRUE);
$headings = array('Help', 'Help with Expenses');
$navbox = FALSE;
$urls = array('key_to_expenses.php');
require_once './php/essentials.php';
require_once './php/header_one.php';
echo '  <link rel="stylesheet" type="text/css" href="stylesheets/expenses.css" />', "\n";
require_once './php/header_two.php';

?>
<p>The content of this page is adapted from pages 7-9 of the <a
href="http://search2.hmrc.gov.uk/kb5/hmrc/forms/view.page?record=gcfEDZSp5dY&amp;formId=3043">Self-employment
(Full) Notes</a>, published by <a href="http://www.hmrc.gov.uk/">HMRC</a>.</p>

<h3>Table of Expenses</h3>

<table id="tblexpenses" style="position: relative;">
  <thead>
    <tr>
      <th scope="col" style="width: 19%;">Category</th>
      <th scope="col" style="width: 44%;">Allowable expenses</th>
      <th colspan="2" scope="col" style="width: 37%;">Disallowable expenses</th>
    </tr>
  </thead>

  <tbody>
    <tr class="odd">
      <td>Cost of goods bought for re-sale or goods used</td>
      <td>Cost of goods bought for resale, cost of raw materials used; direct
      costs of producing goods sold; adjustments for opening and closing stock
      and work in progress; commissions payable; discounts given. Taxi and
      minicab drivers and those in the road haulage industry should include fuel
      costs here, rather than in &lsquo;Car, van and travel expenses&rsquo;.</td>
      <td colspan="2">Cost of goods or materials bought for private use; depreciation of equipment.</td>
    </tr>

    <tr class="even">
      <td>Construction industry &ndash; payments to subcontractors</td>
      <td>Total payments made to subcontractors in the construction industry
      (before taking off any deductions). If you take on subcontractors in the
      construction industry (including work in a domestic environment, such as
      painting and decorating), then you probably need to register as a
      contractor in the Construction Industry Scheme (CIS).*</td>
      <td colspan="2">Payments made for non-business work.</td>
    </tr>

    <tr class="odd">
      <td>Wages, salaries and other staff costs</td>
      <td>Salaries, wages, bonuses, pensions, benefits for staff or employees;
      agency fees, subcontract labour costs; employer's NICs etc.</td>
      <td colspan="2">Own wages and drawings, pension payments or NICs; payments made for
      non-business work.</td>
    </tr>

    <tr class="even">
      <td>Car, van and travel expenses</td>
      <td>Car and van insurance, repairs, servicing, fuel, parking, hire
      charges, vehicle licence fees, motoring organisation membership; train,
      bus, air and taxi fares; hotel room costs and meals on overnight business
      trips.</td>
      <td colspan="2">Non-business motoring costs (private use proportions); fines; costs of
      buying vehicles; lease rental expenses for cars with CO<span
      style="font-size: x-small; position: relative; top: 0.3em;">2</span>
      emissions over 160g/km (15% of the amount paid); travel costs between home
      and business; other meals.</td>
    </tr>

    <tr class="odd">
      <td>Rent, rates, power and insurance costs</td>
      <td>Rent for business premises, business and water rates, light, heat,
      power, property insurance, security; use of home as office (business
      proportion only).</td>
      <td colspan="2">Costs of any non-business part of premises; costs of buying business premises.</td>
    </tr>

    <tr class="even">
      <td>Repairs and renewals of property and equipment</td>
      <td>Repairs and maintenance of business premises and equipment; renewals
      of small tools and items of equipment.</td>
      <td colspan="2">Repairs of non-business parts of premises or equipment; costs of
      improving or altering premises and equipment.</td>
    </tr>

    <tr class="odd">
      <td>Telephone, fax, stationery and other office costs</td>
      <td>Phone and fax running costs; postage, stationery, printing and small
      office equipment costs; computer software.</td>
      <td colspan="2">Non-business or private use proportion of expenses; new phone, fax,
      computer hardware or other equipment costs.</td>
    </tr>

    <tr class="even">
      <td>Advertising and business entertainment costs</td>
      <td>Advertising in newspapers, directories etc. mailshots, free samples,
      website costs.</td>
      <td colspan="2">Entertaining clients, suppliers and customers; hospitality at events.</td>
    </tr>

    <tr class="odd">
      <td>Interest on bank and other loans</td>
      <td>Interest on bank and other business loans; alternative finance payments.</td>
      <td rowspan="2" style="background-color: #F0F2F8; vertical-align: middle;
       line-height: 1; padding: 0 0 0 2px;">⎫<br />⎬<br />⎭</td>
      <td rowspan="2" style="background-color: #F0F2F8;">Repayment of the loans
      or overdrafts, or finance arrangements; a proportion of interest and other
      charges where borrowing not used solely for the business.</td>
    </tr>

    <tr class="even">
      <td>Bank, credit card and other financial charges</td>
      <td>Bank, overdraft and credit card charges; hire purchase interest and
      leasing payments; alternative finance payments.</td>
    </tr>

    <tr class="odd">
      <td>Irrecoverable debts written off</td>
      <td>Amounts included in turnover but unpaid and written off because they
      will not be recovered.</td>
      <td colspan="2">Debts not included in turnover; debts relating to fixed assets; general bad debts.</td>
    </tr>

    <tr class="even">
      <td>Accountancy, legal and other professional fees</td>
      <td>Accountant's, solicitor's, surveyor's, architect's and other
      professional fees; professional indemnity insurance premiums.</td>
      <td colspan="2">Legal costs of buying property and large items of equipment; costs of
      settling tax disputes and fines for breaking the law.</td>
    </tr>

    <tr class="odd">
      <td>Depreciation and loss/profit on sale of assets</td>
      <td>These expenses are not allowable, because you are expected to claim the
      full cost of the item as a capital allowance in the year that you buy it.</td>
      <td colspan="2">Depreciation of equipment, cars etc. losses on sales of assets (minus
      any profits on sales).</td>
    </tr>

    <tr class="even">
      <td>Other business expenses</td>
      <td>Trade or professional journals and subscriptions; other sundry
      business running expenses not included elsewhere; net VAT payments.</td>
      <td colspan="2">Payments to clubs, charities, political parties etc. non-business part
      of any expenses; cost of ordinary clothing.</td>
    </tr>

    <tr class="odd">
      <td>Vehicles and equipment</td>
      <td>Vans, tools, computers, business furniture, cars (even if the
      items were purchased under hire purchase) and certain industrial
      and agricultural buildings that you use in your business.</td>
      <td colspan="2" style="font-style: italic;">(n/a)</td>
    </tr>
  </tbody>
</table>

<div class="content">
<?php $open_xhtml_tags[] = 'div'; ?>
<h3>General Notes</h3>

<p>Some expenses are not allowable for tax purposes, for example, entertaining
clients, even if such entertainment directly led to new business. Some expenses
are only partly allowable. For example, you may use a car for both business and
personal (private) motoring; only the business costs are allowable. If you work
from home or use a room in your home as an office you can only charge the
business percentage of the costs of running your home (heat and light etc.)
against tax.</p>

<p>If you have a cost where a proportion of it is allowable and a proportion is
disallowable, in the current version of <?php echo APPNAME; ?> you must enter
this as two separate expenses &ndash; one for the allowable proportion and one
for the disallowable proportion. Alternatively you may omit the disallowable
component and (optionally) make a note of the total amount in the
&lsquo;Notes&rsquo; box. In a future version of <?php echo APPNAME; ?> it may be
possible to enter an expense that is partly allowable as a single record.</p>

<p>If you lease or hire a car you may not be allowed to claim all of the hire
charges/rental payments. For leases which commenced before 6 April 2009, if the
car cost under &pound;12,000 you can claim all of the rental payments. But if
the car cost more than &pound;12,000 you have to disallow a proportion of the
rentals. More details about how to calculate this restriction can be found in
<a href="http://www.hmrc.gov.uk/manuals/bimmanual/BIM47717.htm">section
BIM47717</a> of the Business Income Manual. Where the car lease commenced on or
after 6 April 2009 and the car's CO<span style="font-size: x-small;
position: relative; top: 0.3em;">2</span> emissions are not more than 160g/km
there is no restriction. If the CO<span style="font-size: x-small;
position: relative; top: 0.3em;">2</span> emissions are more than 160g/km, you
can only claim 85% of the rental payments.</p>

<p>In some circumstances you may need to enter a &lsquo;negative&rsquo; expense,
such as a profit on the sale of an asset. You can do this by putting a minus
sign next to the amount.</p>

<p>Do not include the cost of any equipment or machinery you use in the
business. Instead, claim tax allowances (capital allowances) on these items; see
below. But do include their running costs here. If you record the depreciation
or loss in value of any equipment or machinery, you must mark it as
disallowable.</p>

<h3>Vehicles and Equipment (Capital Allowances)</h3>

<p>You can claim tax allowances, called capital allowances, for the costs of
purchasing, and improvements to, vehicles and equipment – such as vans,
tools, computers, business furniture, cars (even if the items were purchased
under hire purchase) and certain industrial and agricultural buildings that
you use in your business. The costs of such items are not allowable as an
expense in working out your taxable profits.</p>
<p>You must not claim capital allowances for:</p>
<ul>
  <li>the costs of things that it is your trade to buy and sell because these
  can be claimed as business expenses</li>
  <li>the interest and other fees that you may be charged for purchasing items
  under hire purchase. These charges should be separated out from the cost of
  the item and filed under &lsquo;Interest on bank and other loans&rsquo;.</li>
</ul>

<h3>Contacts</h3>

<p>For more advice:</p>

<ul>
  <li>Email <?php echo APPNAME; ?> technical support at <a
  href="mailto:alexander@taxrabbit.co.uk">alexander@taxrabbit.co.uk</a></li>
  <li>Phone HMRC's Self-Assessment Helpline on 0845 9000 444</li>
  <li><a href="http://www.hmrc.gov.uk/selfemployed/fagsa103.shtml">Download helpsheets</a> from www.hmrc.gov.uk</li>
</ul>

<p>* For more information about the Construction Industry Scheme, please phone HMRC's New
   Employer Helpline on 0845 60 70 143 or phone the CIS Helpline on 0845 366 7899.</p>
<?php include './php/footer.php';
