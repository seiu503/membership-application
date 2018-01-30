var xmlhttp;

function showAgencies()
{

var e = document.getElementById('unit');
var intval = e.options[e.selectedIndex].value;

xmlhttp=GetXmlHttpObject();
if (xmlhttp==null)
  {
  alert ("Browser does not support HTTP Request");
  return;
  }
var url="getagencies_2018.php";
url=url+"?agencytype="+intval;
console.log(url);
xmlhttp.onreadystatechange=stateChanged;
xmlhttp.open("GET",url,true);
xmlhttp.send(null);
}

function stateChanged() {
  if (xmlhttp.readyState==4) {
    console.log(xmlhttp.responseText);
    document.getElementById("agencylist").innerHTML=xmlhttp.responseText;
  }
}

function GetXmlHttpObject()
{
if (window.XMLHttpRequest)
  {
  // code for IE7+, Firefox, Chrome, Opera, Safari
  return new XMLHttpRequest();
  }
if (window.ActiveXObject)
  {
  // code for IE6, IE5
  return new ActiveXObject("Microsoft.XMLHTTP");
  }
return null;
}