<?php 

//someone could pass us weird shit from this
//like javascript and potentially cause a cross site scripting problem
//so clean this up a bit before including in page
$referrer = 'UNKNOWN';
if(isset($_SERVER['HTTP_REFERER'])) {
$referrer = $_SERVER['HTTP_REFERER'];
$referrer = cleanstring($referrer, 'referrer');
}

$campaign = 'default';
if(isset($_GET['campaign'])) {
$campaign = $_GET['campaign'];
$campaign = cleanstring($campaign, 'alphanumeric');
}


//Load our dynamic data
$db = new mysqli("localhost", "memberappuser", "memberapppass", "memberapp");
$stmt = $db->stmt_init();
$stmt->prepare("SELECT AnalyticsID,InPageCSS,HeaderHTML,MainTextHTML,FooterHTML FROM campaign WHERE CampaignCode = ?");
$stmt->bind_param("s", $campaign);
if(!$stmt->execute() ) {  
		$stmterror = $stmt->error; 
		//echo "Execute Statement Error: $stmterror\n"; 
		echo "Database or data issue. Please try again later.";
		exit; }
 /* bind result variables */
$stmt->store_result();
$stmt->bind_result($analyticsid, $inpagecss,$headerhtml,$maintexthtml,$footerhtml);
$rowsfound = $stmt->num_rows;
if($rowsfound == 1) { 
	$stmt->fetch();		
}
else
{
	//echo "Num rows: " . $stmt->num_rows;
	echo "Database or data issue. Please try again later.";
	exit;
	
}

//echo "<br>AnalyticsID: " . $analyticsid;



if($analyticsid >= 1) { 
    $stmt = $db->stmt_init();
	$stmt->prepare("SELECT AnalyticsTypeID,AnalyticsCodes FROM analytics WHERE AnalyticsID = ?");
	$stmt->bind_param("i", $analyticsid);
	if(!$stmt->execute() ) {  
			$stmterror = $stmt->error; 
			//echo "Execute Statement Error: $stmterror\n"; 
			echo "Database or data issue. Please try again later.";
			exit; }
	$stmt->bind_result($analyticstypeid, $analyticscodes);
	$stmt->fetch();
	
	//echo "<br>AnalyticsType: " . $analyticstypeid;

	if($analyticstypeid >= 1) {
    $stmt = $db->stmt_init();		
	$stmt->prepare("SELECT AnalyticsJavascript FROM analyticstypes WHERE AnalyticsTypeID = ?");
	$stmt->bind_param("i", $analyticstypeid);
	if(!$stmt->execute() ) {  
			$stmterror = $stmt->error; 
			//echo "Execute Statement Error: $stmterror\n"; 
			echo "Database or data issue. Please try again later.";
			exit; }

	$stmt->bind_result($analyticsjavascript);
	$stmt->fetch();	
	}
	
	//echo "<br>Javascript is: " . $analyticsjavascript;
	
	$codesarray = explode(",", $analyticscodes);
	
	$arraycount = 1;
	foreach($codesarray as $code)
	{
		$replace = "<" . "code" . $arraycount . ">";
		$analyticsjavascript = str_replace($replace,$code,$analyticsjavascript);
		
		$arraycount = $arraycount + 1;
	}

	
}

$stmt->close();
$db->close();

	
	
$typeDef = array();
$data = array();

$page = 'index.php';

$data['Page'] = $page;
$typeDef[] = 's';


$useragent = (string) $_SERVER['HTTP_USER_AGENT'];
$useragent = cleanstring($useragent, 'useragent');
$data['UserAgent'] = $useragent;
$typeDef[] = 's';

$ipaddress = (string) $_SERVER['REMOTE_ADDR'];
$ipaddress = cleanstring($ipaddress, 'ip');
$data['IP_Address'] = $ipaddress;
$typeDef[] = 's';


$mysqli = new mysqli("localhost", "memberappuser", "memberapppass", "memberapp");

$table = 'views';	

$insert = error_sql_insert($data, $typeDef, $table);

//echo $insert;

$mysqli->close();

function mysqli_con()
{
   $mysqli = new mysqli("localhost", "memberappuser", "memberapppass", "memberapp");
   return $mysqli;
}	
function bindParameters(&$statement, &$params) {
  $args   = array();
  $args[] = implode('', array_values($params));

  foreach ($params as $paramName => $paramType) {
    $args[] = &$params[$paramName];
    $params[$paramName] = null;
  }

  call_user_func_array(array(&$statement, 'bind_param'), $args);
}


function error_sql_insert ($data, $typeDef, $table) {
    $mysqli = mysqli_con();
    $keys = array_keys($data);
    $vals = array_values($data);
    
    $key = '';
    $val = '';
    $type = '';
    foreach($keys AS $key_value) {
        if($key == '') {
            $key = $key_value;
            $val = '?';
        } else {
            $key .= ', '.$key_value;
            $val .= ', ?';
        }
    }

	
    if ($stmt = $mysqli->prepare("INSERT INTO $table ($key, ViewTime) values ($val, NOW())")) {

        for($i = 0; $i < count($typeDef); $i++) {
            /* Set params value for binding */
            $params[$keys[$i]] = $typeDef[$i];
        }

        /* Bind our params */
        bindParameters($stmt, $params);

        for($i = 0; $i < count($typeDef); $i++) {
            /* Set params for query */
            $params[$keys[$i]] = $vals[$i];
        }
        
        /* Execute the prepared Statement */
        if(!$stmt->execute() ) {  
		$stmterror = $stmt->error; 
		//echo "Execute Statement Error: $stmterror\n"; 
		echo "Database or data issue. Please try again later.";
		exit; }

        /* Echo results */
        $insert = "Success";

        /* Close the statement */
        $stmt->close(); 
    } else {
	
		$prepareerror = $mysqli->error;
		//echo "Prepare Statement Error: $prepareerror\n";
		echo "Data or database issue. Please try again later.";
		exit;
		
        /* Error */
        $insert = "Failure";
    }
    return $insert; 
}


?>

<!DOCTYPE html>
<html>

<head>

   <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
   <link rel="icon" type="image/x-icon" href="favicon.ico">

  <title>Member Application</title>

  <link rel="stylesheet" href="css/app.css">
  <link rel="stylesheet" href="font-awesome-4.1.0/css/font-awesome.css">
  <style> <?php echo $inpagecss ?> </style>
  <script>
  <?php echo $analyticsjavascript ?>

  </script>

  <script src="scripts/jquery-1.11.1.min.js" type="text/javascript"></script>
  <script src="scripts/jquery.inputmask.js" type="text/javascript"></script>
  <script src="scripts/jquery.inputmask.numeric.extensions.js" type="text/javascript"></script>
  <script src="scripts/jquery.inputmask.regex.extensions.js" type="text/javascript"></script>
  <script src="scripts/jquery.inputmask.date.extensions.js" type="text/javascript"></script>
  <script type="text/javascript" src="scripts/agencylist.js"></script>
  <script type="text/javascript">

  $(function() {
    $('.date_ddmmyyyy').inputmask('mm/dd/yyyy', { showMaskOnFocus: true, showMaskOnHover: false });
	//$('.date_ddmmyyyy').inputmask('99/99/9999', { showMaskOnFocus: false, showMaskOnHover: false });
    $('.zip').inputmask('99999', { showMaskOnFocus: false, showMaskOnHover: false });
	$('.state').inputmask('aa', { showMaskOnFocus: false, showMaskOnHover: false });
    $('.phone_us').inputmask('(999) 999-9999', { showMaskOnFocus: false, showMaskOnHover: false });
    //This extremely simple regex breaks on the state email system work emails that are @state.or.us or similar
	//$('.email').inputmask('Regex', { regex: "[a-zA-Z0-9._%-]+@[a-zA-Z0-9-]+\\.[a-zA-Z]{2,4}", showMaskOnFocus: false, showMaskOnHover: false});
	$('.ext').inputmask("integer", { showMaskOnFocus: false, showMaskOnHover: false, rightAlign: false });
	$('.phone_mob').inputmask('(999) 999-9999', { showMaskOnFocus: false, showMaskOnHover: false, "oncomplete": function(){ $(".phone").show(); document.getElementById("sms").checked = false; } });


 
	$( "#submit" ).on('click', function( event ) {

	var validform = 1;
	var errortext = '';
	//Check valid options
	var unit = $( 'select[name="unit"] option:selected' ).val();
	var agencynumber = $( 'select[name="agencynumber"] option:selected').val();
	var classification = $( 'input[type="text"][name="classification"]' ).val();
	var fname = $( 'input[type="text"][name="fname"]' ).val();
	var lname = $( 'input[type="text"][name="lname"]' ).val();
	var dob = $( 'input[type="text"][name="dob"]' ).val();
	var hiredate = $( 'input[type="text"][name="dob"]' ).val();
	var rstreet = $( 'input[type="text"][name="rstreet"]' ).val();
	var rcity = $( 'input[type="text"][name="rcity"]' ).val();	
	var rstate = $( 'select[name="rstate"]' ).val();
	var rzip = $( 'input[type="text"][name="rzip"]' ).val();
	var remail = $( 'input[type="text"][name="remail"]' ).val();
	var rphone = $( 'input[type="text"][name="rphone"]' ).val();
	var termsagree = $('input[type="checkbox"][name="termsagree"]').prop('checked');
	var fullname = $( 'input[type="text"][name="fullname"]' ).val();
	
/*	alert("Unit: " + unit + 
	"\n AgencyNumber: " + agencynumber + 
	"\n Classification: " + classification +
	"\n Fname: " + fname +
	"\n Lname: " + lname +
	"\n DOB: " + dob +
	"\n Rstreet: " + rstreet +
	"\n Rcity: " + rcity +
	"\n Rstate: " + rstate +
	"\n Rzip: " + rzip +
	"\n Rphone: " + rphone +
	"\n Termsagree: " + termsagree); */
	

	if(!termsagree)
	{
		validform = 0;
		errortext = errortext + "<li>You must agree to the membership terms" + "</li>";
		alert("You must agree to the membership terms in order to submit this form to become a member");
	}

	if(fullname <= 0 || (typeof fullname == 'undefined') || fullname.length < 1)
	{
		validform = 0;
		errortext = errortext + "<li>Full name for signature invalid" + "</li>";
	}
	
	if(unit <= 0 || (typeof unit == 'undefined') || unit.length < 1)
	{
		validform = 0;
		errortext = errortext + "<li>Employment unit invalid" + "</li>";
	}
	
	if(agencynumber <= 0 || (typeof agencynumber == 'undefined') || agencynumber.length < 1)
	{
		validform = 0;
		errortext = errortext + "<li>Employment agency invalid. Select/reselect agency. Select/reselect employment unit first." + "</li>";
	}
	
/*	if(classification.length < 1 || (typeof classification == 'undefined'))
	{
		validform = 0;
		errortext = errortext + "<li>Worksite/Classification is invalid" + "</li>";
	} */
	
	if(fname.length < 1)
	{
		validform = 0;
		errortext = errortext + "<li>First name is invalid" + "</li>";
	}
	
	if(lname.length < 1)
	{
		validform = 0;
		errortext = errortext + "<li>Last name is invalid" + "</li>";
	}
	
	if(remail.length < 1)
	{
		validform = 0;
		errortext = errortext + "<li>Personal email address is invalid" + "</li>";
	}
	

	
	//Can't use current year as someone may have incorrect time on their machine
	//and javascript runs client side -- 2047, full retirement age year of this programmer!
	var validdob = isValidDate(dob, 1900, 2047);
	if(!validdob)
	{
		validform = 0;
		errortext = errortext + "<li>Date of birth invalid format or year less than 1900 or greater than 2047" + "</li>";
	}

	//Check the hiredate if it has been entered
	if(typeof hiredate != 'undefined' && hiredate != '' && hiredate.length > 6)
	{
		var validhiredate = isValidDate(hiredate, 1900, 2047);
		if(!validhiredate)
		{
			validform = 0;
			errortext = errortext + "<li>Hire date invalid format or year less than 1900 or greater than 2047" + "</li>";
		}
	}
	
/*	if(rstreet.length < 1)
	{
		validform = 0;
		errortext = errortext + "<li>Residence street address is invalid" + "</li>";
	}
	
	if(rcity.length < 1)
	{
		validform = 0;
		errortext = errortext + "<li>Residence city is invalid" + "</li>";
	}
	
	if(rstate.length != 2)
	{
		validform = 0;
		errortext = errortext + "<li>Residence state is invalid" + "</li>";
	} */
	
	if(rzip.length != 5)
	{
		validform = 0;
		errortext = errortext + "<li>Residence zip code is invalid: Use standard 5 digit format " + "</li>";
	}
	
/*	if(rphone.length != 14)
	{
		validform = 0;
		errortext = errortext + "<li>Residence phone is invalid: Use (xxx) xxx-xxxx format" + "</li>";
	} */
	
		if (validform == 1) {
		event.preventDefault();
		$("form").submit();
		return;
		}
		
		if(unit >= 1)
		{
			showAgencies();
		}
	

		if(agencynumber > 1)
		{
		  $( 'select[name="agencynumber"]').val(agencynumber);
		}
		
		$( "#messages" ).html("<h3>Errors with your submission:</h3><ul>" +errortext+"</ul>");
		event.preventDefault();
		//Need to reload agencies as they are dynamically generated
	});
  });
</script>

<script type="text/javascript">
function getElementByClass(classname){
 var inc=0;
 var alltags=document.all? document.all : document.getElementsByTagName("*");
 for (i=0; i<alltags.length; i++){
   if (alltags[i].className==classname)
     customcollection[inc++]=alltags[i];
 }
 return customcollection;
}
</script>

<script type="text/javascript">
function isValidDate(txtDate, min, max)
{
  var currVal = txtDate;
  if(currVal == '')
    return false;
  
  //Declare Regex  
  var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/; 
  var dtArray = currVal.match(rxDatePattern); // is format OK?

  if (dtArray == null)
     return false;
 
  //Checks for mm/dd/yyyy format.
  dtMonth = dtArray[1];
  dtDay= dtArray[3];
  dtYear = dtArray[5];

  if (dtYear < min)
	return false;
	
  if (dtYear > max) 
	return false;
	
  if (dtMonth < 1 || dtMonth > 12)
      return false;
  else if (dtDay < 1 || dtDay> 31)
      return false;
  else if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31)
      return false;
  else if (dtMonth == 2)
  {
     var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
     if (dtDay> 29 || (dtDay ==29 && !isleap))
          return false;
  }
  return true;
}

function showThis(theclassname) {

	var allElems = document.getElementsByTagName('*');
	for (var i = 0; i < allElems.length; i++) {
		var thisElem = allElems[i];
		if (thisElem.className && thisElem.className == theclassname) {
			thisElem.style.display = 'block';
		}
	}

}

function hideThis(theclassname) {

	var allElems = document.getElementsByTagName('*');
	for (var i = 0; i < allElems.length; i++) {
		var thisElem = allElems[i];
		if (thisElem.className && thisElem.className == theclassname) {
			thisElem.style.display = 'none';
		}
	}

}

</script>
<script type="text/javascript">

var termsshown = 0;
function terms()
{
	if(termsshown != 1)
	{
		showThis('memberterms');
		termsshown = 1;
	}
	else
	{
	
		hideThis('memberterms');
		termsshown = 0;
	}
}

var textshown = 0;
function phonetext()
{

var textBox = document.getElementById("mobile");
var textLength = textBox.value.length;

	if(textLength > 0)
	{
		showThis('phone');
		textshown = 1;
		
	}
	else
	{
	
		hideThis('phone');
		textshown = 0;
	}
}

function fillmobile()
{

var textBox = document.getElementById("rphone");
var textValue = textBox.value;
    document.getElementById("rmobile").value = textValue;
	$(".phone").show();
	document.getElementById("sms").checked = false;
}

function showFields()
{
	showThis('optional');
	hideThis('showfields');
}

function hideFields()
{
	hideThis('optional');
	showThis('showfields');
}

function selectmobile()
{
	fillmobile();
	showThis('mobile');
}

function selectlandline()
{
    showThis('mobile');
}
</script>
</head>
<body>

<div class="header">
<?php echo $headerhtml ?>
<div class="container">
<div id="memberapp">
  <h1>Member Signup and Recommit</h1>
 <hr>
<?php echo $maintexthtml ?>
  <p class="instructions">Please complete all required fields. Required fields are indicated by a light purple background. <a href="#optional" onClick="showFields()">Optional fields</a> used to provide more information have a white background. Submission of this form indicates an agreement to the membership terms below. Your full name, network address, and a timestamp of your submission will serve as your signature.</p>
   <div class="optional">
  <p><a href="#hideoptional" onClick="hideFields()">Hide Optional Fields</a>
  </p>
  </div>
  
<hr>
  <form id="memberappform" action="process.php" method="post">
  <h2>Employment Info</h2>
  
  <div class="required">
  
  
  <label class="icontab"><i class="fa fa-cog"></i></label>
  <span class="custom-dropdown custom-dropdown--purple">
  <?php select_agencytype(); ?>
   </span>
	</div>
	
	<div class="required">
   <div id="agencylist">
   <label class="icontab"><i class="fa fa-cog"></i></label>
   <span class="custom-dropdown custom-dropdown--purple">
   <select name="agencyplaceholder" id="agencyplaceholder" class="custom-dropdown__select custom-dropdown__select--purple" required disabled>
	<option value=''>Select an employment group first...</option>
	</select>
	</span>
   </div>
   </div>
  
  <div class="optional">
  <label class="icon" for="classification"><i class="fa fa-cog"></i></label>
  <input type="text" name="classification" id="classification" placeholder="Job Title/Classification" />
  </div>
  
   <div class="optional">
  <label class="icon" for="hiredate"><i class="fa fa-calendar"></i></label>
  <input type="text" name="hiredate" id="hiredate" class="date_ddmmyyyy" placeholder="Hire Date" pattern="[0-9]*" />
  </div>
  
   <div class="optional">
  <label class="icon" for="ein"><i class="fa fa-cog"></i></label>
  <input type="text" name="ein" id="ein" placeholder="Employee ID or Provider Number" />
  </div>
  
   <div class="optional">
  <label class="icon" for="worksite"><i class="fa fa-cog"></i></label>
  <input type="text" name="worksite" id="worksite" placeholder="Worksite/Department" />
  </div>
  
   <div class="optional">
  <label class="icon" for="wemail"><i class="fa fa-keyboard-o"></i></label>
  <input type="text" name="wemail" id="wemail" class="email" placeholder="Work Email" />
  </div>
  
   <div class="optional">
  <label class="icon" for="wphone"><i class="fa fa-phone"></i></label>
  <input type="text" name="wphone" id="wphone" class="phone_us" placeholder="Work Phone" pattern="[0-9]*" />
  </div>
  
   <div class="optional">
  <label class="icon" for="wext"><i class="fa fa-phone"></i></label>
  <input type="text" name="wext" id="wext" class="ext" placeholder="Work Phone Extension" pattern="[0-9]*" />
  </div>
  
  
  <h2>Person Info</h2>
  
  <div class="required">
  <label class="icon" for="fname"><i class="fa fa-user"></i></label>
  <input type="text" name="fname" id="fname" placeholder="First Name" required/>
  </div>
  
   <div class="optional">
  <label class="icon" for="mname"><i class="fa fa-user"></i></label>
  <input type="text" name="mname" id="mname" placeholder="Middle Name"/>
  </div>
  
  <div class="required">
  <label class="icon" for="lname"><i class="fa fa-user"></i></label>
  <input type="text" name="lname" id="lname" placeholder="Last Name" required/>
  </div>
  
  <div class="required">
  <label class="icon" for="dob"><i class="fa fa-calendar"></i></label>
  <input type="text" name="dob" id="dob" class="date_ddmmyyyy" placeholder="Date of Birth" pattern="[0-9]*" required/>
  </div>

 <div class="optional">
  <label class="icontab" for="ethnicity"><i class="fa fa-cog"></i></label>
 <span class="custom-dropdown custom-dropdown--purple">
  <?php select_ethnicity(); ?>
   </span>
   </div>
  
  
   <div class="optional">
   <label class="icontab" for="language"><i class="fa fa-cog"></i></label>
   <span class="custom-dropdown custom-dropdown--purple">
  <?php select_language(); ?>
   </span>
   </div>
  
  <h2>Residence Address</h2>
  
   <div class="optional">
  <label class="icon" for="rstreet"><i class="fa fa-home"></i></label>
  <input type="text" name="rstreet" id="rstreet" placeholder="Residence Street Address" />
   </div>
  
  <div class="optional">
  <label class="icon" for="rcity"><i class="fa fa-home"></i></label>
  <input type="text" name="rcity" id="rcity" placeholder="Residence City" />
   </div>
  
  
  <div class="optional">
  <label class="icontab" for="rstate"><i class="fa fa-home"></i></label>
  <span class="custom-dropdown custom-dropdown--purple">
  <?php select_state('rstate', ''); ?>
   </span>
  </div>
  
  <div class="required">
  <label class="icon" for="rzip"><i class="fa fa-home"></i></label>
  <input type="text" name="rzip" id="rzip" class = "zip" placeholder="Residence Zip" pattern="[0-9]*" required/>
  </div>
  
   <div class="optional">
  <h2>Mailing Address</h2>
  <p class="small">[if different than residence address]</p>
  
  <div class="optional">
  <label class="icon" for="mstreet"><i class="fa fa-envelope"></i></label>
  <input type="text" name="mstreet" id="mstreet" placeholder="Mailing Street Address" />
   </div>
  
  <div class="optional">
  <label class="icon" for="mcity"><i class="fa fa-envelope"></i></label>
  <input type="text" name="mcity" id="mcity" placeholder="Mailing City" />
   </div>
  
  <div class="optional">
  <label class="icontab" for="mstate"><i class="fa fa-envelope"></i></label>
  <span class="custom-dropdown custom-dropdown--purple">
  <?php select_state('mstate', ''); ?>
   </span>
    </div>
  
  <div class="optional">
  <label class="icon" for="mzip"><i class="fa fa-envelope"></i></label>
  <input type="text" name="mzip" id="mzip" class = "zip" placeholder="Mailing Zip" pattern="[0-9]*" />
  </div>
  
  </div>
  
  <h2>Contact Info</h2>
  
  <div class="required">
  <label class="icon" for="remail"><i class="fa fa-keyboard-o"></i></label>
  <input type="text" name="remail" id="remail" class="email" placeholder="Personal Email" required/>
    </div>
  
  <div class="optional">
  <label class="icon" for="rphone"><i class="fa fa-phone"></i></label>
  <input type="text" name="rphone" id="rphone" class="phone_us" placeholder="Primary Phone" pattern="[0-9]*" />
    </div>
  
   <div class="optional">
  <div id="phonetype">
	<label class="question">Type:</label><input type="radio" value="yes" id="land" name="phone_type" />
    <label for="land" class="radio" onClick="selectlandline();">Landline</label>
    <input type="radio" value="no" id="cell" name="phone_type" onClick="selectmobile();" />
    <label for="cell" class="radio">Mobile</label>
   </div>
   </div>
  
  <div class="optional">
  <div class="mobile">
	  <label class="icon" for="rmobile"><i class="fa fa-mobile-phone"></i></label>
	  <input type="text" name="rmobile" id="rmobile" class="phone_mob" placeholder="Mobile Phone" pattern="[0-9]*" />
	 
	  
	  <div class="phone">
		<input type="checkbox" value="checked" id="sms" name="sms" />
		<label for="sms" class="checkbox">Opt out of receiving mobile alerts&dagger;</label>
		<p class="small">&dagger; Enter your mobile phone number if you would like to receive mobile alerts from SEIU Local 503. SEIU will never charge you for text message alerts, but carrier message and data rates may apply. Text STOP to 787753 to unsubscribe, and HELP for more info. Periodic updates, never more than 5 per month.</p>

	   </div>
   </div>
   </div>
   
   
   <div id = "signature">
   <h2>Signature</h2>
        <div class="required">
  <label class="icon" for="fullname"><i class="fa fa-pencil"></i></label>
	<input type="text" name="fullname" id="fullname" placeholder="Full Name for Signature" required/>
	<p class="small">Enter your full legal name. This will act as your signature.</p>
	 </div>
  </div>
  
   <div id="termsdiv">
   <input type="checkbox" value="checked" id="termsagree" name="termsagree">
    <label for="termsagree" class="checkbox">I agree to the membership <a href="#terms" onClick="terms()">terms</a></label>
   </div>
   <div class="memberterms">
 <p class="small">Your full name, the network address you are accessing this page from, and the timestamp of submission will serve as signature indicating: I hereby designate SEIU Local 503, OPEU (or any successor Union entity) as my desired collective bargaining agent. I also hereby authorize my employer to deduct from my wages, commencing with the next payroll period, all Union dues and other fees or assessments as shall be certified by SEIU Local 503, OPEU (or any successor Union entity) and to remit those amounts to such Union. This authorization/delegation is unconditional, made in consideration for the cost of representation and other actions in my behalf by the Union and is made irrespective of my membership in the Union. This authorization is irrevocable for a period of one year from the date of execution and from year to year thereafter unless not less than thirty (30) and not more than forty-five (45) days prior to the end of any annual period or the termination of the contract between my employer and the Union, whichever occurs first, I notify the Union and my employer in writing, with my valid signature, of my desire to revoke this authorization. <a href="#hideterms" onClick="terms()">hide terms</a></p>
   </div>
   

   <input type = "hidden" name="referrer" value="<?php echo $referrer; ?>">
   <input type = "hidden" name="campaign" value="<?php echo $campaign; ?>">
   
   <div id="messages"></div>
   
    <div class="optional">
  <p><a href="#hideoptional" onClick="hideFields()">Hide Optional Fields</a>
  </p>
  </div>
  
  <div class="showfields">
  <p><a href="#optional" onClick="showFields()">Show Optional Fields and provide more information</a>
  </p>
  </div>
  
   <a href="" id="submit" class="button">Sign up</a>
  </form>
<?php echo $footerhtml ?>
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

function select_agencytype()
{
    echo "<select name=\"unit\" id=\"unit\" class=\"custom-dropdown__select custom-dropdown__select--purple\" onChange=\"showAgencies()\" required>\n";
	echo "<option value=''>Select an employment group...</option>";
	
	$mysqli = new mysqli("localhost", "memberappuser", "memberapppass", "memberapp");
	
	if ($mysqli->connect_errno) {
		//echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	
	/* Select queries return a resultset */
	if ($result = $mysqli->query("SELECT * FROM agencytypes")) {
		 while ($row = $result->fetch_assoc()) {
        echo "<option value=\"$row[Agency_Type]\">$row[Type_Name]</option>\n";
    }
		
		/* free result set */
		$result->close();
	}
	
	$mysqli->close();
	
	echo "</select>";
}

function select_language()
{
    echo "<select name=\"language\" id=\"language\" class=\"custom-dropdown__select custom-dropdown__select--purple\">\n";
	echo "<option value=''>Select preferred language...</option>";
	
	$mysqli = new mysqli("localhost", "memberappuser", "memberapppass", "memberapp");
	
	if ($mysqli->connect_errno) {
		//echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	
	/* Select queries return a resultset */
	if ($result = $mysqli->query("SELECT * FROM languages")) {
		 while ($row = $result->fetch_assoc()) {
        echo "<option value=\"$row[Language_Code]\">$row[Language_Name]</option>\n";
    }
		
		/* free result set */
		$result->close();
	}
	
	$mysqli->close();
	
	echo "</select>";
}

function select_ethnicity()
{
    echo "<select name=\"ethnicity\" id=\"ethnicity\" class=\"custom-dropdown__select custom-dropdown__select--purple\">\n";
	echo "<option value=''>Select ethnicity...</option>";
	
	$mysqli = new mysqli("localhost", "memberappuser", "memberapppass", "memberapp");
	
	if ($mysqli->connect_errno) {
		//echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	
	/* Select queries return a resultset */
	if ($result = $mysqli->query("SELECT * FROM ethnicities")) {
		 while ($row = $result->fetch_assoc()) {
        echo "<option value=\"$row[Ethnic_Code]\">$row[Ethnic_Name]</option>\n";
    }
		
		/* free result set */
		$result->close();
	}
	
	$mysqli->close();
	
	echo "</select>";
}

function select_state($fieldname, $pseudoclass)
{
    echo "<select name=\"$fieldname\" id=\"$fieldname\" class=\"custom-dropdown__select custom-dropdown__select--purple\" $pseudoclass>\n";
	echo "<option value=''>Select State...</option>";
	
	$mysqli = new mysqli("localhost", "memberappuser", "memberapppass", "memberapp");
	
	if ($mysqli->connect_errno) {
		//echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	
	/* Select queries return a resultset */
	if ($result = $mysqli->query("SELECT * FROM states")) {
		 while ($row = $result->fetch_assoc()) {
        echo "<option value=\"$row[State_Code]\">$row[State_Name]</option>\n";
    }
		
		/* free result set */
		$result->close();
	}
	
	$mysqli->close();
	
	echo "</select>";
}
	
?>
