<?php header("Content-type: text/css; charset: UTF-8"); ?>

h2 {margin-bottom: 0.75em;}

table {border-collapse: collapse;}

th {text-align: left;}

table#tblincome thead th {border-bottom: solid 1px Black;}
table#tblincome th, table#tblincome td {padding-left: 3px; padding-right: 3px;}
table#tblincome tr.odd     {background-color: White;}
table#tblincome tr.even    {background-color: #E0E4F0;}
table#tblincome tr.negodd  {background-color: #FFDCDC;}
table#tblincome tr.negeven {background-color: #FFCACA;}
table#tblincome td.fee, td.expl {text-align: right;}
table#tblincome .cc, table#tblincome .date, table#tblincome th.fee, table#tblincome .paid {text-align: center;}
th.note img, td.note img {width: 16px; height: 16px;}

<?php
if (
  is_int(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))     // User Agent contains 'MSIE'
and
  is_int(strpos($_SERVER['HTTP_USER_AGENT'], 'Windows'))  // User Agent contains 'Windows'
) {
  echo "table#tblincome td.paid {font-family: Webdings;}\n";
  $tick = 'a';
} else {
  echo 'table#tblincome td.paid {font-family:
      "Lucida Grande", "Zapf Dingbats", "Arial Unicode MS", "Lucida Sans",
      "DejaVu Sans", Junicode, Code2000, "Chrysanthi Unicode", "Arev Sans",
      OpenSymbol, StarSymbol, "Unicode Symbols", "Everson Mono Unicode",
      "Free Serif", "MS Gothic", "MS PGothic", "MS UI Gothic", "MS Mincho",
      "MS PMincho", CN-Arial, CN-Times, "HY Shin Myeongjo Std Acro", "Kochi Gothic",
      "Kochi Mincho", "Kozuka Mincho Pro Acro", "Sazanami Mincho", "Y.OzFontN";
    }
';
  $tick = '&#10003;';
}
?>

table#key {
  margin-top: 2em;
  margin-left: auto; margin-right: auto;
  page-break-inside: avoid;
  font-size: x-small;
  border: solid 1px;
}

table#key h3 {font-size: small;}
table#key td.abbr {text-align: center;}
table#key td.full {padding-left: 2px; padding-right: 2px; border-right: solid 1px;}
