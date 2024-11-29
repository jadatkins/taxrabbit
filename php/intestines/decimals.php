<?php

function digest_decimal(&$input, $name, $ante = 18, $post = 2) {
  // $ante and $post are the number of digits before and after the decimal point.
  // $name is the user-friendly name of the input field, to be used in error messages.

  if ($input == '') return ($input = NULL);
  settype($ante, 'integer');
  settype($post, 'integer');

  // Construct the third regular expression, which depends on $ante.
  $middle = '';
  $extra = '';
  if ($ante > 3) {
    $m = (int) ($ante / 3);
    if ($ante >= 6)
      $middle = '|\d{1,3}([, ]\d{3}){1,' . ($m-1) . '}';
    if (($r = fmod($ante, 3)) != 0)
      $extra = '|\d{1,' . $r . '}([, ]\d{3}){' . $m . '}';
  }

  if (preg_match('/^\D*(0)0*\.?0*\D*$/', $input, $matches)							// match 0
      or $post && preg_match('/^\D*(?<!\.)0*(\.\d{1,'.$post.'})0*\D*$/', $input, $matches)			// match fractions
      or $ante && preg_match('/^\D*(?<!\.)0*((?<!\.)\d{1,'.$ante.'}'.$middle.$extra.')\D*$/', $input, $matches)	// match integers
      or $ante && $post && preg_match('/^\D*(?<!\.)0*((?<!\.)(\d{1,'.$ante.'}'.$middle.$extra.')\.\d{1,'.$post.'})0*\D*$/', $input, $matches)  // match anything else
    /* Here's that last regular expression with whitespace and annotation.
    ^			assert start
    \D*			any number of non-numeric characters
    0*			any number of zeroes
    (			#1
      (			  #2
	\d{1,$ante}	    1 to $ante digits
      |			  or
	\d{1,3}	 	   1 to 3 digits
	(		    #3
	  [, ]		      exactly one of comma or space
	  \d{3,3}	      3 digits
	){1,4}		    1 to 4 repetitions of #3
      |			  or
	\d		    1 digit
	(		    #4
	  [, ]		      exactly one of comma or space
	  \d{3,3}	      3 digits
	){5,5}		    5 repetitions of #4
      )			  #2
      (			  #5
	\.		    full stop
	\d{1,$post}	    1 to $post digits
      )?		  0 or 1 of #5
    )			#1
    \D*			any number of non-numeric characters
    $			assert end */
  ) {
    $input = (strpos($input, '-') === FALSE ? '' : '-') . (($matches[1]{0} == '.') ? '0' : '') . str_replace(array(',', ' '), '', $matches[1]);
    return $input;
  } else {
    friendly_error("The $name (entered as &lsquo;$input&rsquo;) is not in an acceptable form.&nbsp;
     An acceptable form would be something like &lsquo;2018.75&rsquo;.&nbsp;
     Note that you can only have up to $ante digits before the decimal point,
     and up to $post digits after the decimal point.", TRUE);
  }
}
