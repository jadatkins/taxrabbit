<?php
if (
  is_int(strpos($_SERVER['HTTP_USER_AGENT'], 'Gecko'))     // User Agent contains 'Gecko'
) {
  echo '  <style type="text/css">
  /* <![CDATA[ */
    span.osuper {
      vertical-align: text-top;
      font-size: smaller;
      text-decoration: underline;
  /*  line-height: 0; */
    }
  /* ]]> */
  </style>
';
  $numero_sign = 'N<span class="osuper">o</span>';
} else {
  if (true) {
  echo '  <style type="text/css">
  /* <![CDATA[ */
    span.numero {font-family: "Helvetica Neue", "Microsoft Sans Serif", Arial, Helvetica, sans-serif;}
  /* ]]> */
  </style>
';
  }
  $numero_sign = '<span class="numero">&#8470;</span>';
}
?>
