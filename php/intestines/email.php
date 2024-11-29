<?php

function digest_email(&$addr) {
  if ($addr == '') return;
  if (!emailaddr_isvalid($addr)) {
    $matches = array();
    if (preg_match('/\S <(.+@.+)>$/', $addr, $matches)) {
      if (emailaddr_isvalid($matches[1])) {
        $addr = $matches[1];
        return;
      }
    }
    trigger_error("&lsquo;$addr&rsquo; is not a valid e-mail address.", E_USER_ERROR);
  }
}

/* This emailaddr_isvalid function was adapted from two similar functions.
One was written by Dave Child and published on 1 June 2004 at
        http://www.addedbytes.com/php/email-address-validation
and the other was written by Douglas Lovell and published on 1 June 2007 at
        http://www.linuxjournal.com/article/9585
I took the code from these sites on 6 August 2009. */

/**
Validate an email address.
Provide email address (raw input)
Returns true if the email address has the email address format.
*/
function emailaddr_isvalid($email)
{
  $isValid = true;
  $atIndex = strrpos($email, "@");
  if (is_bool($atIndex) && !$atIndex)
  {
    $isValid = false;
  }
  else
  {
    $domain = substr($email, $atIndex+1);
    $local = substr($email, 0, $atIndex);
    $localLen = strlen($local);
    $domainLen = strlen($domain);
    if ($localLen < 1 || $localLen > 64)
    {
      // local part length exceeded
      $isValid = false;
    }
    else if ($domainLen < 1 || $domainLen > 255)
    {
      // domain part length exceeded
      $isValid = false;
    }
    else if ($local[0] == '.' || $local[$localLen-1] == '.')
    {
      // local part starts or ends with '.'
      $isValid = false;
    }
    else if (preg_match('/\\.\\./', $local))
    {
      // local part has two consecutive dots
      $isValid = false;
    }
    else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
    {
      // character not valid in domain part
      $isValid = false;
    }
    else if (preg_match('/\\.\\./', $domain))
    {
      // domain part has two consecutive dots
      $isValid = false;
    }
    else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
               str_replace("\\\\","",$local)))
    {
      // character not valid in local part unless local part is quoted
      if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local)))
      {
        $isValid = false;
      }
    }

    // Check if domain is IP. If not, it should be valid domain name
    if (!preg_match("/^\[?[0-9\.]+\]?$/", $domain)) {
      $domain_array = explode(".", $domain);
      if (sizeof($domain_array) < 2) {
        return false; // Not enough parts to domain
      }
      for ($i = 0; $i < sizeof($domain_array); $i++) {
        if (!preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/",
              $domain_array[$i])) {
          return false;
        }
      }
    }

  }
  return $isValid;
}
