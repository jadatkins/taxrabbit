function disable(text_array) {
  var index;
  if (!text_array) {
    document.frm_main.btn_save.disabled=true;
    setTimeout('enable(document.frm_main.btn_save)', 2000);
    return;
  }
  for (j=0; j<text_array.length; j++) {
    eval(text_array[j]).disabled=true;
    setTimeout('enable(' + text_array[j] + ')', 2000);  // in case the user
    // clicks 'Back' after submitting and the browser still has the button disabled
  }
}

function enable(item) {
  item.disabled=false;
}

var yellowboxpos;
var yellowboxfinal;
var yellowboxunit;

function hide_messages() {
  yellowboxpos = 0;
  yellowboxfinal = - document.getElementById('yellow_msg_box').offsetHeight;
  yellowboxunit = Math.round(Math.sqrt(- yellowboxfinal) / 3);
  move_yellowbox();
}

function move_yellowbox() {
  if (yellowboxpos <= yellowboxfinal) {document.getElementById('yellow_msg_box').style.visibility='hidden'; return;}
  setTimeout(move_yellowbox, 18);
  yellowboxpos -= yellowboxunit;
  document.getElementById('yellow_msg_box').style.top = yellowboxpos + 'px';
}

function weekday(box, text)
{
  var xmlhttpw;
  if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttpw=new XMLHttpRequest();
    }
  else
    {// code for IE6, IE5
    xmlhttpw=new ActiveXObject("Microsoft.XMLHTTP");
    }
  xmlhttpw.onreadystatechange=function()
    {
    if (xmlhttpw.readyState==4 && xmlhttpw.status==200)
      {
      box.innerHTML=xmlhttpw.responseText;
      }
    }
  xmlhttpw.open("GET","weekday.php?text="+encodeURI(text),true);
  xmlhttpw.send();
}

function insertcancel(caption) {
  if (!caption) caption='Go back';
  document.write('<a href="javascript:history.back()">' + caption + '</a>');
}

// Email obfuscator script 2.1 by Tim Williams, University of Arizona
// Random encryption key feature by Andrew Moulden, Site Engineering Ltd
// This code is freeware provided these four comment lines remain intact
// A wizard to generate this code is at http://www.jottings.com/obfuscator/
function insert_detail(anchor)
{ coded = "QKEmQ6VEG@qQmGQddtq.4O.YM"
  key = "xh3Obnv5EwUlsJqAyrCjHPIS9miukd6ac7p8KfNFo0GRDe1WBzTtYMVgQ4LXZ2"
  shift=coded.length
  link=""
  for (i=0; i<coded.length; i++) {
    if (key.indexOf(coded.charAt(i))==-1) {
      ltr = coded.charAt(i)
      link += (ltr)
    }
    else {     
      ltr = (key.indexOf(coded.charAt(i))-shift+key.length) % key.length
      link += (key.charAt(ltr))
    }
  }
if (anchor) document.write("<a href='mailto:"+link+"'>"+link+"</a>")
else document.write(link)
}

/*
function newform(table, target_div_id, user_id, business_id) {
  var xmlhttpf;
  var target_node = document.getElementById(target_div_id);
  if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttpf=new XMLHttpRequest();
    }
  else
    {// code for IE6, IE5
    xmlhttpf=new ActiveXObject("Microsoft.XMLHTTP");
    }
  xmlhttpf.onreadystatechange=function()
    {
    if (xmlhttpf.readyState==4 && xmlhttpf.status==200)
      {
      target_node.innerHTML=xmlhttpf.responseText;
      }
    }
  xmlhttpf.open("GET","forms/"+table+".php?user_id="+user_id+"&business_id="+business_id,true);
  xmlhttpf.send();
  return false;
}
*/