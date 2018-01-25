<?php 
$typeDef = array();
$data = array();

$page = "cape.php";
$data['Page'] = $page;
$typeDef[] = 's';


//someone could pass us weird shit from this
//like javascript and potentially cause a cross site scripting problem
//so clean this up a bit before including in page
$referrer = $_SERVER['HTTP_REFERER'];
$referrer = cleanstring($referrer, 'referrer');
$data['Referrer'] = $referrer;
$typeDef[] = 's';

$ipaddress = (string) $_SERVER['REMOTE_ADDR'];
$ipaddress = cleanstring($ipaddress, 'ip');
$data['IP_Address'] = $ipaddress;
$typeDef[] = 's';

$useragent = (string) $_SERVER['HTTP_USER_AGENT'];
$useragent = cleanstring($useragent, 'useragent');
$data['UserAgent'] = $useragent;
$typeDef[] = 's';

$mysqli = new mysqli("localhost", "memberappuser", "memberapppass", "memberapp");

$table = 'views';	

/* print_r($data);
print_r($typeDef); */


$insert = sql_insert($data, $typeDef, $table);
 
$mysqli->close();

$memberappcode = $_GET['appid'];
$memberappcode = cleanstring($memberappcode, 'alphanumeric');

$frommemberapp = 0;

if(strlen($memberappcode > 8))
{
	$frommemberapp = 1;
}
?>

<!DOCTYPE html>
<html>

<head>

   <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
   <link rel="icon" type="image/x-icon" href="favicon.ico">

  <title>Political Action Check-off</title>

  <link rel="stylesheet" href="css/cape.css">
  <link rel="stylesheet" href="font-awesome-4.1.0/css/font-awesome.css">
  <script src="scripts/jquery-1.11.1.min.js" type="text/javascript"></script>
  <script src="scripts/jquery.inputmask.js" type="text/javascript"></script>
  <script src="scripts/jquery.inputmask.numeric.extensions.js" type="text/javascript"></script>
  <script src="scripts/jquery.inputmask.regex.extensions.js" type="text/javascript"></script>
  <script src="scripts/jquery.inputmask.date.extensions.js" type="text/javascript"></script>
  <script type="text/javascript" src="scripts/agencylist.js"></script>
  <script type="text/javascript">

  $(function() {
    $('.date_ddmmyyyy').inputmask('mm/dd/yyyy', { showMaskOnFocus: true, showMaskOnHover: false });
	//$('.money').inputmask({mask : '$9{1,4}.99'}, { numericInput: true, showMaskOnFocus: true, showMaskOnHover: false });
	//$('.money').inputmask('Regex', {regex: "^\$(([1-9]\d{0,2}(,\d{3})*)|(([1-9]\d*)?\d))(\.\d\d)?$" }, { numericInput: true, showMaskOnFocus: false, showMaskOnHover: false });
	$('.money').inputmask('$[9][9][9][9].99', { numericInput: true, showMaskOnFocus: true, showMaskOnHover: false, clearMaskOnLostFocus: true});
	
	$( "#ten" ).click(function() {
		var text = '10.00';
		$( 'input[type="text"][name="amount"]' ).val( text );
		});
		
	$( "#thirteen" ).click(function() {
		var text = '13.00';
		$( 'input[type="text"][name="amount"]' ).val( text );
		});
		
	$( "#fifteen" ).click(function() {
		var text = '15.00';
		$( 'input[type="text"][name="amount"]' ).val( text );
		});
	
	$( "#submit" ).on('click', function( event ) {
	
	var validform = 1;
	var errortext = '';
	
		//Check valid options
	var unit = $( 'select[name="unit"] option:selected' ).val();
	var agencynumber = $( 'select[name="agencynumber"] option:selected').val();
	
	var fname = $( 'input[type="text"][name="fname"]' ).val();
	var lname = $( 'input[type="text"][name="lname"]' ).val();
	var dob = $( 'input[type="text"][name="dob"]' ).val();
	
	var amount = $('input[type="text"][name="amount"]' ).val();
	
	amount = amount.replace(/[^0-9.]/g, '')//Strip all funky characters: find anything not a digit or period, g for all occurances, replace with nothing
	
	var fullname = $( 'input[type="text"][name="fullname"]' ).val();

	var frommemberapp = $( 'input[type="hidden"][name="frommemberapp"]' ).val();
	
	var memappcode = $( 'input[type="hidden"][name="memappcode"]' ).val();
	
	/*alert("From: " + frommemberapp + 
	"\n AppCode: " + memappcode + 
	"\n Amount: " + amount);*/
	
	if(frommemberapp != 1)
	{
		
			if(parseInt(unit,10) <= 0 || (typeof unit == 'undefined') || unit.length < 1)
			{
				validform = 0;
				errortext = errortext + "<li>Employment unit invalid" + "</li>";
			}
			
			if(parseInt(agencynumber,10) <= 0 || (typeof agencynumber == 'undefined') || agencynumber.length < 1)
			{
				validform = 0;
				errortext = errortext + "<li>Employment agency invalid. Select/reselect agency. Select/reselect employment unit first." + "</li>";
			}
			
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
			
			//Can't use current year as someone may have incorrect time on their machine
			//and javascript runs client side -- 2047, full retirement age year of this programmer!
			var validdob = isValidDate(dob, 1900, 2047);
			if(!validdob)
			{
				validform = 0;
				errortext = errortext + "<li>Date of birth invalid format or year less than 1900 or greater than 2047" + "</li>";
			}
		}
	
		if((typeof fullname == 'undefined') || fullname.length < 1)
			{
				validform = 0;
				errortext = errortext + "<li>Full name for signature is invalid" + "</li>";
			}
			
		//amount should be at least length of 3, if we get $___._2 this will become .20 and not .02, by making
        //sure after stripping the nonvalid characters that we have atleast 3 characters, we end up with .2 as invalid but .20 and .02 valid		
		if(parseFloat(amount) < .01 || (typeof amount == 'undefined') || amount.length < 3 || parseFloat(amount) == 'NaN')
			{
				validform = 0;
				errortext = errortext + "<li>Contribution amount is invalid" + "</li>";
			}
		else
		{
			var r = confirm("CAPE deduction will be: $" + amount + "/month");
			if(r == false)
			{
				event.preventDefault();
				validform = 0;
				return;
			}
		}
			
			
		if (validform == 1) {
		event.preventDefault();
		$("form").submit();
		return;
		}
		
	if(parseInt(unit,10) >= 1)
		{
			showAgencies();
		}
	

		if(parseInt(agencynumber,10) > 1)
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
</script>
  
 <?php
 
 if($frommemberapp) //app code should be 8 random alpha plus the integer ID
 {
	echo "<style> #optional { display: none; } </style>";
 }
 
 ?>
</head>
<body>

<div class="header">
<div class="logo">
<img id="logo" src="images/logo.png" />
</div>
<div class="banner">
<h1>Members Building Political Power Together</h1>
<p>Citizen Action for Political Education</p>
</div>
</div>
<div class="container">
<div id="capeapp">
 <h1>CAPE Signup</h1>
 <hr>
  <p class="instructions">Please complete all required fields. Required fields are indicated by a light purple background. Submission of this form indicates an agreement to the terms shown. Your full name, network address, and a timestamp of your submission will serve as your signature. You will be given the opportunity to confirm the deduction amount as the form is submitted.</p>
<hr>
<form id="capeform" action="processcape.php" method="post">
<div id="optional">
<h2>Employment Info</h2>
  
  <div>
 <label class="icontab"><i class="fa fa-cog"></i></label>
  <span class="custom-dropdown custom-dropdown--purple">
  <?php select_agencytype(); ?>
   </span>
	</div>
   <div id="agencylist">
   <label class="icontab"><i class="fa fa-cog"></i></label>
   <span class="custom-dropdown custom-dropdown--purple">
   <select name="agencyplaceholder" id="agencyplaceholder" class="custom-dropdown__select custom-dropdown__select--purple" required disabled>
	<option value=''>Select an employment group first...</option>
	</select>
	</span>
   </div>
   
  
  <label class="icon" for="fname"><i class="fa fa-user"></i></label>
  <input type="text" name="fname" id="fname" placeholder="First Name" required/>
  
  <label class="icon" for="mname"><i class="fa fa-user"></i></label>
  <input type="text" name="mname" id="mname" placeholder="Middle Name"/>
  
  <label class="icon" for="lname"><i class="fa fa-user"></i></label>
  <input type="text" name="lname" id="lname" placeholder="Last Name" required/>
  
  <label class="icon" for="dob"><i class="fa fa-calendar"></i></label>
  <input type="text" name="dob" id="dob" class="date_ddmmyyyy" placeholder="Date of Birth" required/>
</div>

<h2>CAPE Contribution</h2>

  <p class="small">Select a predefined amount or enter in a contribution amount</p>

  <div class="list">
  <ul class="unilist">
  <li id="ten" ><span class="unibull">&#x25A0;</span><p class="select">Contribute $10.00/month</p></li>
  <li id="thirteen" ><span class="unibull">&#x25A0;</span><p class="select">Contribute $13.00/month</p></li>
  <li id="fifteen"><span class="unibull">&#x25A0;</span><p class="select">Contribute $15.00/month</p></li>
  </ul>
  </div>

  <label class="icon" for="amount"><i class="fa fa-money"></i></label>
  <input type="text" name="amount" id="amount" class="money" placeholder="Monthly Contribution Amount" required/>
  <p class="small">Contribution amount in dollars per month</p>
  
  <div id = "signature">
   <h2>Signature</h2>
  <label class="icon" for="fullname"><i class="fa fa-pencil"></i></label>
	<input type="text" name="fullname" id="fullname" placeholder="Full Name for Signature" required/>
	<p class="small">Enter your full legal name. This will act as your signature.</p>
  </div>
  
  <input type = "hidden" name="referrer" value="<?php echo $referrer; ?>">
  <input type = "hidden" name="frommemberapp" value="<?php echo $frommemberapp; ?>">
  <input type = "hidden" name="memappcode" value="<?php echo $memberappcode; ?>">
  
   <div id="messages"></div>
   
   <a href="" id="submit" class="button">Sign Up</a>
  </form>
</div>

<div id="info">
<h3>This contribution qualifies for the Oregon Political Tax Credit. Single filers are eligible for a 
credit of up to $50 per year; joint filers up to $100 per year.</h3>

<p>I hereby authorize my Employer to deduct the designated amount from  my monthly earnings as a 
contribution to SEIU local 503, OPEU CAPE. My contribution will be used to support member-endorsed 
candidates and for expenditures in connection with elections for Local, Legislative, Statewide
and Federal offices. These elected officials make critical decisions on salaries, healthcare, 
retirement and other benefits and laws affecting SEIU Local 503, OPEU members. A portion of this 
contribution (as much as 48% for the average contributor) may be used by SEIU for federal elections. 
This contribution is not deductible for federal income tax purposes.</p>

<p>The contribution amounts indicated above are only suggestions and I may choose not to contribute or 
to vary my contribution amount without reprisal from my Union or my Employer. As per federal law, 
only union members and union executive/administrative staff who are U.S. citizens or lawful permanent 
residents are eligible to contribute to SEIU COPE (the Federal Committee on Political Education). This 
authorization is made voluntarily and is not a condition of my employment or membership in the 
union. This authorization shall remain in effect until revoked in writing by me. This contribution 
is in addition to union dues.</p>


<p>Dues and fees to SEIU Local 503, OPEU are not deductible as charitable contributions for federal 
income tax purposes. Dues paid to SEIU Local 503, OPEU may qualify as business expenses, and may not 
be deductible in limited circumstances subject to various restrictions imposed by the Internal 
Revenue Code. Changes which may from time to time occur in state and federal law will not alter 
these deduction requests, unless so indicated by me.</p>

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
	if($filtertype == 'generic') { $filter = "/[^0-9a-zA-Z @.\-#+]/"; }
	if($filtertype == 'useragent') { $filter = "/[^0-9a-zA-Z @.\-#+()\/]/"; }
	if($filtertype == 'referrer') { $filter = "/[^0-9a-zA-Z @.\-#+()\/]/"; }
	if($filtertype == 'email') { $filter = "/[^0-9a-zA-Z@-.+]/"; }
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

function sql_insert ($data, $typeDef, $table) {
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

		global $insertid;
		$insertid = $mysqli->insert_id;
		
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
