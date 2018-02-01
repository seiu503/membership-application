<?php

$errors = 0;
$errormessages = '';
$termschecked = 0;
$typeDef = array();
$data = array();

$errortypeDef = array();
$errordata = array();

//Passing the referrer from the form page as a hidden
//form input captures the referrer for the form

//This could be an empty string if someone accessed
//the page directly rather than through an email or other link
$referrer = $_POST['referrer'];
$referrer = cleanstring($referrer, 'referrer');
$data['Referrer'] = $referrer;
$typeDef[] = 's';

$errordata['Referrer'] = $referrer;
$errortypeDef[] = 's';


//Passing the campaign code from the form page as a hidden
//form input captures the campaign code used in the URL

//This could be an empty string if someone accessed
//the page directly rather than through an email or other link
$campaign = $_POST['campaign'];
$campaign = cleanstring($campaign, 'alphanumeric');
$data['CampaignCode'] = $campaign;
$typeDef[] = 's';



$useragent = (string) $_SERVER['HTTP_USER_AGENT'];
$useragent = cleanstring($useragent, 'useragent');
$data['UserAgent'] = $useragent;
$typeDef[] = 's';

$errordata['UserAgent'] = $useragent;
$errortypeDef[] = 's';

$ipaddress = (string) $_SERVER['REMOTE_ADDR'];
$ipaddress = cleanstring($ipaddress, 'ip');
$data['IP_Address'] = $ipaddress;
$typeDef[] = 's';

$errordata['IP_Address'] = $ipaddress;
$errortypeDef[] = 's';

//Because this is our submission script, referrer to
//this page should always be seiu503signup.org -- capture for security reasons
//This should always be one of our URLs
//Have some issue where Websense or some bot is submitting
//the form without any input and no SubmitFrom is being set
$submissionfrom = (string) $_SERVER['HTTP_REFERER'];
$submissionfrom = cleanstring($submissionfrom, 'referrer');
$data['SubmitFrom'] = $submissionfrom;
$typeDef[] = 's';

$errordata['SubmitFrom'] = $submissionfrom;
$errortypeDef[] = 's';


$fullnamefilled = 0;

//*Fullname
$fullname = $_POST['fullname'];
$fullname = cleanstring($fullname, 'alphaspace');
if(strlen($fullname) >= 1)
{
	$data['FullName'] = $fullname;
	$typeDef[] = 's';
	$fullnamefilled = 1;
}
else
{
	$errors++;
	$errormessages .= "Full name for signature is invalid.<br>\n";
}


//*termsagree -- this should never not be checked as form should not be submitted if not checked
$termsagree = $_POST['termsagree'];
$termsagree = cleanstring($termsagree, 'alpha');


if($termsagree == 'on')
{
	$termschecked = 1;
	$data['Accepted'] = $termschecked;
	$typeDef[] = 'i';
}
else
{
	$errors++;
	$errormessages .= "Membership terms not agreed to.<br>\n";
}

if($termschecked && $fullnamefilled)
{

//Build up the two arrays, one with the types of s for string, i for integer, etc.
//The other with data in the format of $key and $value with the table field name
//and then what we want to insert, like "Fname"=>"Jim" using $data['Fname'] = $firstname
//and then $typeDef[] = 's' to indicate it is a string type


//*agencynumber  -- drop, integer (max of 5 digits currently but could grow)
$agencynumber = $_POST['agencynumber'];
$agencynumber = (int) cleanstring($agencynumber, 'integer');
if(strlen($agencynumber) >= 3)
{
$data['Agency_Number'] = $agencynumber;
$typeDef[] = 'i';
}
else
{
	$errors++;
	$errormessages .= "Invalid Agency Number.<br>\n"; //This should never happen as it is from a drop down
}

//*fname
$fname = $_POST['fname'];
$fname = cleanstring($fname, 'alphaspacehyphen');
if(strlen($fname) >= 1)
{
$data['Fname'] = $fname;
$typeDef[] = 's';
}
else
{
	$errors++;
	$errormessages .= "Invalid First Name.<br>\n";
}

//*lname
$lname = $_POST['lname'];
$lname = cleanstring($lname, 'alphaspacehyphen');
if(strlen($lname) >= 1)
{
$data['Lname'] = $lname;
$typeDef[] = 's';
}
else
{
	$errors++;
	$errormessages .= "Invalid Last Name.<br>\n";
}


//*dob
$dob = $_POST['dob'];
//echo "DOB is $dob<br>\n";
$dob = (string) cleanstring($dob, 'integer');
//echo "Clean DOB is $dob<br>\n";
if(strlen($dob) == 8)
{
	$dob = eight_digit_date_to_my_format($dob);
	$dobyear = substr($dob, strlen($dob) - 4, 4);

	//check that year < 2079 or else SQL blows up
	if (($dobyear < 2079))
	{
		$data['DOB'] = $dob;
	    $typeDef[] = 's';
	}
	else
	{
	   $errors++;
	$errormessages .= "Invalid Date of Birth. Please enter with 2 digit month and day and 4 digit year following the mm/dd/yyyy format.<br>\n";
	}

}
else
{
	$errors++;
	$errormessages .= "Invalid Date of Birth. Please enter with 2 digit month and day and 4 digit year following the mm/dd/yyyy format.<br>\n";
}

//language -- drop, single character code
$language = $_POST['language'];
$language = cleanstring($language, 'alpha');
if(strlen($language) == 3)
{
$data['Language_Code'] = $language;
$typeDef[] = 's';
}
else
{
	//Field is optional, assume not entered or if invalid just ignore bad data
}

//*rstreet
$rstreet = $_POST['rstreet'];
$rstreet = cleanstring($rstreet, 'generic');
if(strlen($rstreet) >= 2)
{
$data['Res_Street'] = $rstreet;
$typeDef[] = 's';
}
else
{
	$errors++;
	$errormessages .= "Invalid Residence Street Address.<br>\n";
}

//*rcity
$rcity = $_POST['rcity'];
$rcity = cleanstring($rcity, 'alphaspacehyphen');
if(strlen($rcity) >= 2)
{
$data['Res_City'] = $rcity;
$typeDef[] = 's';
}
else
{
	$errors++;
	$errormessages .= "Invalid Residence City.<br>\n";
}


//*rstate
$rstate = $_POST['rstate'];
$rstate = cleanstring($rstate, 'alpha');
if(strlen($rstate) == 2)
{
$data['Res_State'] = $rstate;
$typeDef[] = 's';
}
else
{
	$errors++;
	$errormessages .= "Invalid Residence State.<br>\n";
}


//*rzip
$rzip = $_POST['rzip'];
$rzip = (int) cleanstring($rzip, 'integer');
if(strlen($rzip) == 5)
{
$data['Res_Zip'] = $rzip;
$typeDef[] = 'i';
}
else
{
	$errors++;
	$errormessages .= "Invalid Residence Zip. Zip should be exactly 5 digits.<br>\n";
}

 //mstreet
$mstreet = $_POST['mstreet'];
$mstreet = cleanstring($mstreet, 'generic');
if(strlen($mstreet) >= 2)
{
$data['Mail_Street'] = $mstreet;
$typeDef[] = 's';
}
else
{
	//Field is optional, assume not entered or if invalid just ignore bad data
}

 //mcity
$mcity = $_POST['mcity'];
$mcity = cleanstring($mcity, 'alphaspacehyphen');
if(strlen($mcity) >= 2)
{
$data['Mail_City'] = $mcity;
$typeDef[] = 's';
}
else
{
	//Field is optional, assume not entered or if invalid just ignore bad data
}


 //mstate
$mstate = $_POST['mstate'];
$mstate = cleanstring($mstate, 'alpha');
if(strlen($mstate) == 2)
{
$data['Mail_State'] = $mstate;
$typeDef[] = 's';
}
else
{
	//Field is optional, assume not entered or if invalid just ignore bad data
}


//mzip
$mzip = $_POST['mzip'];
$mzip = (int) cleanstring($mzip, 'integer');
if(strlen($mzip) == 5)
{
$data['Mail_Zip'] = $mzip;
$typeDef[] = 'i';
}
else
{
	//Field is optional, assume not entered or if invalid just ignore bad data
}

//remail
$remail = $_POST['remail'];
$remail = cleanstring($remail, 'email');
if(strlen($remail) >= 6)
{
$data['Email_Address'] = $remail;
$typeDef[] = 's';
}
else
{
	$errors++;
	$errormessages .= "Invalid Email Address.<br>\n";
}


//rmobile

//Set default for sms text as things are now opt out and this makes things confusing
$textagree = 0;

$rmobile = $_POST['rmobile'];
$rmobile = cleanstring($rmobile, 'integer');
//Clean as integer but leave as string other wise we run in to issues because
//the 10 digit number is larger than an int field can store
if(strlen($rmobile) == 10)
{
$data['Cell_Phone'] = $rmobile;
$typeDef[] = 's';



//SMS text is now opt out instead of opt in, so we need a cell phone first to set

//Set default for opt in now that we have a mobile number
$textagree = 1;
//sms
$sms = $_POST['sms'];
$sms = cleanstring($sms, 'alpha');
if(strlen($sms) >= 2)
{

	if($sms == 'no') { $textagree = 0; }
	//if($sms == 'yes') { $textagree = 1; }
	if($sms == 'checked') { $textagree = 0; }
	//$data['SMS'] = $textagree;
    //$typeDef[] = 'i';
}
else
{
	$textagree = 1; //should already be set, but doesn't hurt to reset to 1 now that we are opt out
}
}
else
{
	//Field is optional, assume not entered or if invalid just ignore bad data
}

//This should always be set to something now that we changed to opt out
//so have moved it down here out of the if statement
$data['SMS'] = $textagree;
$typeDef[] = 'i';


//Store error count and errors
$data['ErrorCount'] = (int) $errors;
$typeDef[] = 'i';

$data['Errors'] = $errormessages;
$typeDef[] = 's';


$mysqli = new mysqli("localhost", "memberappuser", "memberapppass", "memberapp");

$table = 'submissions';

//For development testing
//echo "Error count: $errors <br>\n";
//echo "<hr>Errors: <br>\n$errormessages<hr><br>\n";
//print_r($data);
//print_r($typeDef);

//echo "<br><br><br><hr><br><br>";

$insert = sql_insert($data, $typeDef, $table);

//echo $insert;

$mysqli->close();


//Store error count and errors
$errordata['ErrorCount'] = (int) $errors;
$errortypeDef[] = 'i';

$errordata['Errors'] = $errormessages;
$errortypeDef[] = 's';

$mysqli = new mysqli("localhost", "memberappuser", "memberapppass", "memberapp");

$table = 'errors';

function debug_to_console( $data ) {
    $output = $data;
    if ( is_array( $output ) )
        $output = implode( ',', $output);

    echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
}

// For development testing
// echo "Error count: $errors <br>\n";
// echo "<hr>Errors: <br>\n$errormessages<hr><br>\n";
// print_r($data);
// print_r($typeDef);

// echo "<br><br><br><hr><br><br>";

debug_to_console($data);

$insert = error_sql_insert($errordata, $errortypeDef, $table);
$url = "https://seiu503signup.org/p2_test.html?Contact.FirstName=" .$fname. "&Contact.LastName=" .$lname. "&Contact.Home_Email__c=" .$remail;
// Redirect to page two
// header("Location: index_new_2.php?campaign=$campaign&email=$remail");
header("Location: $url");
/* Redirect browser */

include 'ChromePhp.php';
ChromePhp::log('process_test.php > 436');
ChromePhp::log($url);

exit();
} //end if terms checked
else
{

//Store error count and errors
$errordata['ErrorCount'] = (int) $errors;
$errortypeDef[] = 'i';

$errordata['Errors'] = $errormessages;
$errortypeDef[] = 's';


$mysqli = new mysqli("localhost", "memberappuser", "memberapppass", "memberapp");

$table = 'errors';

// For development testing
echo "Error count: $errors <br>\n";
echo "<hr>Errors: <br>\n$errormessages<hr><br>\n";
print_r($data);
print_r($typeDef);

echo "<br><br><br><hr><br><br>";

$insert = error_sql_insert($errordata, $errortypeDef, $table);

// echo $insert;


  //Submission is not valid
  echo "Your submission is not valid. Please check that you have entered the required fields and agreed to the membership terms.";
  $mysqli->close();
  exit();
}


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

//Pete would like dates in format mm-dd-yyyy
//we take in mm/dd/yyyy and then strip the slashes
//this function can quickly be modified to give other
//formats or changed to return a true date format
//instead of a string with things changed
//other just do str_replace to replace / with -
function eight_digit_date_to_my_format($digitdate)
{
	if(strlen($digitdate) == 8)
	{
		//$originaldate = $digitdate; //make a copy just in case
		$newday =  substr($digitdate,  2, 2);
		$newmonth =  substr($digitdate, 0, 2);
		$newyear = substr($digitdate, 4, 4);
		$newformatteddate = $newmonth ."-". $newday ."-". $newyear;

		return $newformatteddate;
	}
	else
	{
		return false;
	}
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


    if ($stmt = $mysqli->prepare("INSERT INTO $table ($key, Date_Submitted) values ($val, NOW())")) {

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
		//echo "INSERT INTO $table ($key, Date_Submitted) values ($val, NOW())";
		echo "Data or database issue. Please try again later.";
		exit;

        /* Error */
        $insert = "Failure";
    }
    return $insert;
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


    if ($stmt = $mysqli->prepare("INSERT INTO $table ($key, ErrorTime) values ($val, NOW())")) {

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