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


if($termsagree == 'checked')
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

/* //classification
$classification = $_POST['classification'];
$classification = cleanstring($classification, 'generic');
if(strlen($classification) >= 1)
{
$data['Classification'] = $classification;
$typeDef[] = 's';
}
else
{
	//Field is optional, assume not entered or if invalid just ignore bad data
} */


/* //hiredate
$hiredate = $_POST['hiredate'];
//echo "Hiredate is $hiredate<br>\n";
$hiredate = (string) cleanstring($hiredate, 'integer');
//echo "Clean hiredate is $hiredate<br>\n";
if(strlen($hiredate) == 8)
{
	$hiredate = eight_digit_date_to_my_format($hiredate);
	$hiredateyear = substr($hiredate, strlen($hiredate) - 4, 4);
	//check that year < 2079 or else SQL blows up
	if (($hiredateyear < 2079))
	{
		$data['Hire_Date'] = $hiredate;
		$typeDef[] = 's';
	}
	else
	{
	   //Field is optional, assume not entered or if invalid just ignore bad data
	}

}
else
{
	//Field is optional, assume not entered or if invalid just ignore bad data
} */


/* //ein
$ein = $_POST['ein'];
$ein = cleanstring($ein, 'alphanumeric');
if(strlen($ein) >= 1)
{
$data['EIN'] = $ein;
$typeDef[] = 'i';
}
else
{
	//Field is optional, assume not entered or if invalid just ignore bad data
}

//worksite
$worksite = $_POST['worksite'];
$worksite = cleanstring($worksite, 'generic');
if(strlen($worksite) >= 1)
{
$data['Worksite'] = $worksite;
$typeDef[] = 's';
}
else
{
	//Field is optional, assume not entered or if invalid just ignore bad data
} */


/* //wemail
$wemail = $_POST['wemail'];
$wemail = cleanstring($wemail, 'email');
if(strlen($wemail) >= 6)
{
$data['Work_Email_Address'] = $wemail;
$typeDef[] = 's';
}
else
{
	//Field is optional, assume not entered or if invalid just ignore bad data
} */


/* //wphone
$wphone = $_POST['wphone'];
$wphone = cleanstring($wphone, 'integer');
//Clean as integer but leave as string other wise we run in to issues because
//the 10 digit number is larger than an int field can store
if(strlen($wphone) == 10)
{
$data['Work_Phone_Number'] = $wphone;
$typeDef[] = 's';
}
else
{
	//Field is optional, assume not entered or if invalid just ignore bad data
} */

/* //wext
$wext = $_POST['wext'];
$wext = (int) cleanstring($wext, 'integer');
if(strlen($wext) >= 1)
{
$data['Work_Phone_Ext'] = $wext;
$typeDef[] = 'i';
}
else
{
	//Field is optional, assume not entered or if invalid just ignore bad data
} */

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

/* //mname
$mname = $_POST['mname'];
$mname = cleanstring($mname, 'alphaspacehyphen');
if(strlen($mname) >= 1)
{
	$data['Mname'] = $mname;
	$typeDef[] = 's';
} */

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

//For radio buttons the field name will be set to the value="" of whatever was checked.. $gender = 'M' or 'F'
//This field has been dropped
/*
//*gender -- radio  male,female
$gender = $_POST['gender'];
if($gender == 'male') { $gendercode = 'M'; }
if($gender == 'female') { $gendercode = 'F'; }
if (!isset($gendercode)) { $gendercode = 'U'; }
$data['Gender'] = $gendercode;
$typeDef[] = 's';
*/

/* //ethnicity -- drop, single character code
$ethnicity = $_POST['ethnicity'];
$ethnicity = cleanstring($ethnicity, 'alpha');
if(strlen($ethnicity) == 1)
{
$data['Ethnic_Code'] = $ethnicity;
$typeDef[] = 's';
}
else
{
	//Field is optional, assume not entered or if invalid just ignore bad data
} */

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

/* //*rphone
$rphone = $_POST['rphone'];
$rphone = cleanstring($rphone, 'integer');
//Clean as integer but leave as string other wise we run in to issues because
//the 10 digit number is larger than an int field can store
if(strlen($rphone) == 10)
{
$data['Home_Phone'] = $rphone;
$typeDef[] = 's';
}
else
{
	//$errors++;
	//$errormessages .= "Invalid Primary Phone number. Please include the area code if you have not and use the format (999) 999-9999.<br>\n";
	//Field is optional, assume not entered or if invalid just ignore bad data
}
 */


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


/* Moved to inside RMOBILE block because we are now making opt out instead of opt in
//sms
$sms = $_POST['sms'];
$sms = cleanstring($sms, 'alpha');
if(strlen($sms) >= 2)
{
	$textagree = 0;
	if($sms == 'no') { $textagree = 0; }
	if($sms == 'yes') { $textagree = 1; }
	$data['SMS'] = $textagree;
    $typeDef[] = 'i';
}
else
{
	//Field is optional, assume not entered or if invalid just ignore bad data
}

*/

//*Fullname
//Since we always need this, I've moved it to the top
//so we can include the data in error logs

//For single checkboxes the Post will equal whatever our value="" is on the front
//As a standard I'm using "checked" to indicate checked

//*termsagree -- this is processed first thing, if terms not agreed to, then fail out

/* changed to a checkbox instead of radio
//*scholarship
$scholarship = $_POST['scholarship'];
$scholarship = cleanstring($scholarship, 'alpha');
if((strlen($scholarship) == 1) && ($scholarship == 'P' || $scholarship == 'S'))
{
	$data['Scholarship'] = $scholarship;
    $typeDef[] = 's';
}
else
{
	//There may be an issue.. we default to political committee... I'll assume something funky
	//is going on and box not shown or checked, because of the legal and politics involved I don't
	//want to simply assume Political was selected
}
*/

//If there is an issue, then we assume political.. this has been broken
//because I wrote it for a value of "checked" when it was being set to "S"
//because previously we had a radio box for "S" or "P"
//so even if people selected to give to scholarship, they were forced in to political
//this was fixed 3/4/2016, looks to have been broken since 8/9/2014

/*
$scholarship = $_POST['scholarship'];
$scholarship = cleanstring($scholarship, 'alpha');

echo $scholarship;

if($scholarship == 'checked')
{
	$scholarship = 'S';
	$data['Scholarship'] = $scholarship;
	$typeDef[] = 's';
}
else
{
	$scholarship = 'P';
	$data['Scholarship'] = $scholarship;
	$typeDef[] = 's';
}

*/




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

//For development testing
//echo "Error count: $errors <br>\n";
//echo "<hr>Errors: <br>\n$errormessages<hr><br>\n";
//print_r($data);
//print_r($typeDef);

//echo "<br><br><br><hr><br><br>";

$insert = error_sql_insert($errordata, $errortypeDef, $table);

//Take to thank you page
header("Location: index_new_2.php?campaign=$campaign&email=$remail"); /* Redirect browser */
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

//For development testing
//echo "Error count: $errors <br>\n";
//echo "<hr>Errors: <br>\n$errormessages<hr><br>\n";
//print_r($data);
//print_r($typeDef);

//echo "<br><br><br><hr><br><br>";

$insert = error_sql_insert($errordata, $errortypeDef, $table);

//echo $insert;


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