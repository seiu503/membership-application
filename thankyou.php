<?php 

//someone could pass us weird shit from this
//like javascript and potentially cause a cross site scripting problem
//so clean this up a bit before including in page
$referrer = $_SERVER['HTTP_REFERER'];
$referrer = cleanstring($referrer, 'referrer');


$memberappcode = $_GET['appid'];
$memberappcode = cleanstring($memberappcode, 'alphanumeric');
?>

<!DOCTYPE html>
<html>

<head>

   <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
   <link rel="icon" type="image/x-icon" href="favicon.ico">

  <title>Thank you</title>
  
   <link rel="stylesheet" href="css/app.css">
   </head>
<body>
<div class="header">
<div class="logo">
<img id="logo" src="images/logo.png" />
</div>
<div class="banner">
<h1>SEIU Local 503</h1>
<p>SERVICE EMPLOYEES INTERNATIONAL UNION, CTW, CLC</p>
</div>
</div>
<div class="thankscontainer">
<div id="thanksdiv">
<h1>Thank you</h1>
<p id="thanks">Thank you for your submission. It has been saved in to our database
and will be processed by our Membership Department.</p>
<p>If you would like to contribute to our political program please go to our <a href="cape.php?appid=<?php echo $memberappcode; ?>">CAPE sign-up</a></p>
</div>
</div>
</body>
</html>

<?php
/****************************************************************************************************************************/
function cleanstring($string, $filtertype)
{
	$string = strip_tags($string); //remove HTML
	$string = trim($string); //remove whitespace from front and back of string
	
	//This should be changed to a case statement with default
	$filter = "/[^0-9a-zA-Z @.\-#+]/";
	if($filtertype == 'generic') { $filter = "/[^0-9a-zA-Z @.\-#\+]/"; }
	if($filtertype == 'useragent') { $filter = "/[^0-9a-zA-Z @.\-#\+()\/]/"; }
	if($filtertype == 'referrer') { $filter = "/[^0-9a-zA-Z @.\-#\+()\/]/"; }
	if($filtertype == 'email') { $filter = "/[^0-9a-zA-Z@\-.\+]/"; }
	if($filtertype == 'alphanumeric') { $filter = "/[^0-9a-zA-Z]/"; }
	if($filtertype == 'alpha') { $filter = "/[^a-zA-Z]/"; }
	if($filtertype == 'alphaspace') { $filter = "/[^a-zA-Z ]/"; }
	if($filtertype == 'alphaspacehyphen') { $filter = "/[^a-zA-Z \-]/"; }
	if($filtertype == 'alphanumericspace') { $filter = "/[^0-9a-zA-Z ]/"; }
	if($filtertype == 'integer') { $filter = "/[^0-9]/"; }
	if($filtertype == 'float') { $filter = "/[^0-9.]/"; }
	if($filtertype == 'ip') { $filter = "/[^0-9.]/"; }
	if($filtertype == 'address') { $filter = "/[^0-9a-zA-Z \-#]/"; }
	
	$string = preg_replace($filter, "", $string); //strip anything that isnt a letter, number or included special char
	
	return $string;
}
?>