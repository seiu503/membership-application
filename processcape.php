<?php

$errors = 0;
$insertid = 0;
$errormessages = '';
$typeDef = array();
$data = array();

//Build up the two arrays, one with the types of s for string, i for integer, etc.
//The other with data in the format of $key and $value with the table field name
//and then what we want to insert, like "Fname"=>"Jim" using $data['Fname'] = $firstname
//and then $typeDef[] = 's' to indicate it is a string type

//If from the memberapp, then we can ignore almost all of the other input
$frommemberapp = $_POST['frommemberapp'];
$frommemberapp = (int) cleanstring($frommemberapp, 'integer');
if($frommemberapp === 1 || $frommemberapp === 0) //Use triple equals to insure that code is set and valid int not just unset 0
{ 
   //Don't need to store this as memappcode will indicate that
}
else
{
	$errors++;
	$errormessages .= "Invalid flag set for referral from member app.<br>\n"; //This should never happen as it is from a drop down
}


//echo "From MemberApp = $frommemberapp <br><br>";


if($frommemberapp)
{
	$memappcode = $_POST['memappcode'];
	$memappcode = cleanstring($memappcode, 'alphanumeric');
	if(strlen($memappcode) > 8) 
	{ 
		$data['MemAppCode'] = $memappcode;
		$typeDef[] = 's';
	}
	else
	{
		$errors++;
		$errormessages .= "Invalid Member App Code.<br>\n";
	}
}

//echo "App Code $memappcode <br><br>";

if(!$frommemberapp)
{
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

//mname
$mname = $_POST['mname'];
$mname = cleanstring($mname, 'alphaspacehyphen');
if(strlen($mname) >= 1)
{
	$data['Mname'] = $mname;
	$typeDef[] = 's';
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

} //end of if statement for data fields when not coming from the memberapp


$amount = $_POST['amount'];
$amount = cleanstring($amount, 'float');
$amount = floatval($amount);
if($amount > .01 && $amount < 9999.99)
{
	$data['MonthlyAmount'] = $amount;
	$typeDef[] = 'd';	
}
else 
{ 
	$errors++; 
	$errormessages .= "Contribution amount is invalid.<br>\n"; 
} 



//*fullname
$fullname = $_POST['fullname'];
$fullname = cleanstring($fullname, 'alphaspace');
if(strlen($fullname) >= 1)
{
	$data['FullName'] = $fullname;
	$typeDef[] = 's';	
}
else 
{ 
	$errors++; 
	$errormessages .= "Full name for signature is invalid.<br>\n"; 
} 




//Because this is our submission script, referrer to
//this page should always be seiu503signup.org -- capture for security reasons

//Passing the referrer from the form page as a hidden 
//form input captures the referrer for the form
$submissionfrom = (string) $_SERVER['HTTP_REFERER'];
$submissionfrom = cleanstring($submissionfrom, 'referrer');
$data['SubmitFrom'] = $submissionfrom;
$typeDef[] = 's';

//This could be an empty string if someone accessed
//the page directly rather than through an email or other link
$referrer = $_POST['referrer'];
$referrer = cleanstring($referrer, 'referrer');
$data['Referrer'] = $referrer;
$typeDef[] = 's';


$useragent = (string) $_SERVER['HTTP_USER_AGENT'];
$useragent = cleanstring($useragent, 'useragent');
$data['UserAgent'] = $useragent;
$typeDef[] = 's';

$ipaddress = (string) $_SERVER['REMOTE_ADDR'];
$ipaddress = cleanstring($ipaddress, 'ip');
$data['IP_Address'] = $ipaddress;
$typeDef[] = 's';

//Store error count and errors
$data['ErrorCount'] = (int) $errors;
$typeDef[] = 'i';

$data['Errors'] = $errormessages;
$typeDef[] = 's';



$mysqli = new mysqli("localhost", "memberappuser", "memberapppass", "memberapp");

$table = 'cape';	

//For development testing
/*echo "Error count: $errors <br>\n";
echo "<hr>Errors: <br>\n$errormessages<hr><br>\n";
print_r($data);
print_r($typeDef);*/

//echo "<br><br><br><hr><br><br>";

$insert = sql_insert($data, $typeDef, $table);
//echo $insert;
 
$mysqli->close();

//Take to thank you page
header("Location: thankyou.html"); /* Redirect browser */
exit();

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