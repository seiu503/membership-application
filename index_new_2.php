<?php

//someone could pass us weird shit from this
//like javascript and potentially cause a cross site scripting problem
//so clean this up a bit before including in page
$referrer = 'UNKNOWN';
if(isset($_SERVER['HTTP_REFERER'])) {
$referrer = $_SERVER['HTTP_REFERER'];
$referrer = cleanstring($referrer, 'referrer');
}

$campaign = '';
if(isset($_GET['campaign'])) {
$campaign = $_GET['campaign'];
$campaign = cleanstring($campaign, 'alphanumeric');
}

$email = '';
if(isset($_GET['email'])) {
$email = $_GET['email'];
$email= cleanstring($email, 'email');
}

$typeDef = array();
$data = array();

$page = 'optional_data.php';

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
<html  lang="en">

<head>

   <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
   <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0">
   <link rel="icon" type="image/x-icon" href="favicon.ico">

  <title>Member Application</title>

  <link rel="stylesheet" href="css/app_20171004.css">
  <link rel="stylesheet" href="font-awesome-4.1.0/css/font-awesome.css">
  <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-74074286-1', 'auto');
  ga('send', 'pageview');

  </script>

  <script type="text/javascript">
  piAId = '172302'; piCId = '1470'; (function() { function async_load(){ var s = document.createElement('script'); s.type = 'text/javascript'; s.src = ('https:' == document.location.protocol ? 'https://pi' : 'http://cdn') + '.pardot.com/pd.js'; var c = document.getElementsByTagName('script')[0]; c.parentNode.insertBefore(s, c); if(window.attachEvent) { window.attachEvent('onload', async_load); } else { window.addEventListener('load', async_load, false); } })();
  </script>

  <script src="scripts/jquery-1.11.1.min.js" type="text/javascript"></script>
  <script src="scripts/jquery.inputmask.js" type="text/javascript"></script>
  <script src="https://cdn.optimizely.com/js/5716112250.js"></script>
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


$('#AfAmdiv').keypress(function (e) {
 var key = e.which;
 if(key == 13)  // the enter key code
  {
    $('input[id = AfAm]').click();
    return false;
  }
});

$('#MENAdiv').keypress(function (e) {
 var key = e.which;
 if(key == 13)  // the enter key code
  {
    $('input[id = MENA]').click();
    return false;
  }
});

$('#AsAmdiv').keypress(function (e) {
 var key = e.which;
 if(key == 13)  // the enter key code
  {
    $('input[id = AsAm]').click();
    return false;
  }
});

$('#HLdiv').keypress(function (e) {
 var key = e.which;
 if(key == 13)  // the enter key code
  {
    $('input[id = HL]').click();
    return false;
  }
});

$('#NAIdiv').keypress(function (e) {
 var key = e.which;
 if(key == 13)  // the enter key code
  {
    $('input[id = NAI]').click();
    return false;
  }
});

$('#NHPIdiv').keypress(function (e) {
 var key = e.which;
 if(key == 13)  // the enter key code
  {
    $('input[id = NHPI]').click();
    return false;
  }
});

$('#Wdiv').keypress(function (e) {
 var key = e.which;
 if(key == 13)  // the enter key code
  {
    $('input[id = W]').click();
    return false;
  }
});

$('#Odiv').keypress(function (e) {
 var key = e.which;
 if(key == 13)  // the enter key code
  {
    $('input[id = O]').click();
    return false;
  }
});

$('#Ddiv').keypress(function (e) {
 var key = e.which;
 if(key == 13)  // the enter key code
  {
    $('input[id = D]').click();
    return false;
  }
});

$('#lgbtqIDdiv').keypress(function (e) {
 var key = e.which;
 if(key == 13)  // the enter key code
  {
    $('input[id = lgbtqID]').click();
    return false;
  }
});

 $('#transIDdiv').keypress(function (e) {
 var key = e.which;
 if(key == 13)  // the enter key code
  {
    $('input[id = transID]').click();
    return false;
  }
});


 $('#disabilityIDdiv').keypress(function (e) {
 var key = e.which;
 if(key == 13)  // the enter key code
  {
    $('input[id = disabilityID]').click();
    return false;
  }
});

 $('#blindIDdiv').keypress(function (e) {
 var key = e.which;
 if(key == 13)  // the enter key code
  {
    $('input[id = blindID]').click();
    return false;
  }
});

 $('#deafIDdiv').keypress(function (e) {
 var key = e.which;
 if(key == 13)  // the enter key code
  {
    $('input[id = deafID]').click();
    return false;
  }
});


	$( "#submit" ).on('click', function( event ) {

	var validform = 1;
	var errortext = '';
	//Check valid options

	var classification = $( 'input[type="text"][name="classification"]' ).val();

	var language = $( 'select[name="language"] option:selected').val();
	var hiredate = $( 'input[type="text"][name="hiredate"]' ).val();
	var mstreet = $( 'input[type="text"][name="mstreet"]' ).val();
	var mcity = $( 'input[type="text"][name="mcity"]' ).val();
	var mstate = $( 'select[name="mstate"]' ).val();
	var mzip = $( 'input[type="text"][name="mzip"]' ).val();
	var wemail = $( 'input[type="text"][name="wemail"]' ).val();
	var wphone = $( 'input[type="text"][name="wphone"]' ).val();
  var remail = $( 'input[type="text"][name="remail"]' ).val();


  if(remail.length < 1)
  {
    validform = 0;
    errortext = errortext + "<li>Personal email address is invalid" + "</li>";
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

		if (validform == 1) {
		event.preventDefault();
		$("form").submit();
		return;
		}


		$( "#messages" ).html("<h3>Errors with your submission:</h3><ul>" +errortext+"</ul>");
		event.preventDefault();
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

</script>
</head>
<body>
<div class="header">
<div class="banner">
<div class="logo">
<img id="logo" alt="SEIU 503 In It Together" src="https://storage.pardot.com/171302/38397/SEIU503_InItTogether.png" />
</div>
</div>
</div>
<div class="container">
<div id="memberapp">
  <h1>Member Optional Information</h1>
  <hr>
      <p class="instructions">
      Your membership application has been received and will be reviewed shortly. In the mean time, please help your fellow union members get to know you better by telling us a little more about yourself. SEIU Local 503 is committed to honoring the
          diversity of all members. This optional demographic information helps us understand the social identities of our membership.</p>
      <hr>
  <form id="memberappform_optional" action="https://go.pardot.com/l/171302/2017-08-28/hd5x6" method="post">

			   <h2>Check as many as apply to your race/ethnicity...</h2>
              <fieldset name="ethnicity" id="ethnicity" class="checkbox-list">
                <div class="checkbox-set"  tabindex="0" id="AfAmdiv">
                  <input type="checkbox"   id="AfAm" name="ethnicity" value="African or African-American">
                  <label for="AfAm" class="whitecheckbox">African or African-American</label>
                </div>
                <div class="checkbox-set"  tabindex="0" id="MENAdiv">
                  <input type="checkbox"   id="MENA" name="ethnicity" value="Arab American, Middle Eastern, or North African">
                  <label for="MENA" class="whitecheckbox">Arab American, Middle Eastern or North African</label>
                </div>
                <div class="checkbox-set"  tabindex="0" id="AsAmdiv">
                  <input type="checkbox"   id="AsAm" name="ethnicity" value="Asian or Asian American">
                  <label for="AsAm" class="whitecheckbox">Asian or Asian American</label>
                </div>
                <div class="checkbox-set"  tabindex="0" id="HLdiv">
                  <input type="checkbox"   id="HL" name="ethnicity" value="Hispanic or Latino">
                  <label for="HL" class="whitecheckbox">Hispanic or Latinx</label>
                </div>
                <div class="checkbox-set"  tabindex="0" id="NAIdiv">
                  <input type="checkbox"   id="NAI" name="ethnicity" value="Native American or Indigenous">
                  <label for="NAI" class="whitecheckbox">Native American or Indigenous</label>
                </div>
                <div class="checkbox-set"  tabindex="0" id="NHPIdiv">
                  <input type="checkbox"   id="NHPI" name="ethnicity" value="Native Hawaiian or Other Pacific Islander">
                  <label for="NHPI" class="whitecheckbox">Native Hawaiian or Other Pacific Islander</label>
                </div>
                <div class="checkbox-set"  tabindex="0" id="Wdiv">
                  <input type="checkbox"   id="W" name="ethnicity" value="White">
                  <label for="W" class="whitecheckbox">White</label>
                </div>
                <div class="checkbox-set"  tabindex="0" id="Odiv">
                  <input type="checkbox"   id="O" name="ethnicity" value="Other">
                  <label for="O" class="whitecheckbox">Other</label>
                </div>
                <div class="checkbox-set"  tabindex="0" id="Ddiv">
                  <input type="checkbox"   id="D" name="ethnicity" value="Declined">
                  <label for="D" class="whitecheckbox">Prefer not to say</label>
                </div>
              </fieldset>

            <h2>Other social identities</h2>
            <fieldset>
              <div class="checkbox-set"  tabindex="0" id="lgbtqIDdiv">
                <input type="checkbox"   id="lgbtqID" name="lgbtqID" value="True">
                <label for="lgbtqID" class="whitecheckbox">I identify as <i>LGBTQIA+</i></label>
              </div>
            </fieldset>
            <fieldset>
              <div class="checkbox-set"  tabindex="0" id="transIDdiv">
                <input type="checkbox"   id="transID" name="transID" value="True">
                <label for="transID" class="whitecheckbox">I identify as <i>transgender</i></label>
              </div>
            </fieldset>
            <fieldset>
              <div class="checkbox-set"  tabindex="0" id="disabilityIDdiv">
                <input type="checkbox"   id="disabilityID" name="disabilityID" value="True">
                <label for="disabilityID" class="whitecheckbox">I identify as a <i>person with a disability</i></label>
              </div>
            </fieldset>
            <fieldset>
              <div class="checkbox-set"  tabindex="0" id="blindIDdiv">
                <input type="checkbox"   id="blindID" name="blindID" value="True">
                <label for="blindID" class="whitecheckbox">I identify as <i>blind or visually impaired</i></label>
              </div>
            </fieldset>
            <fieldset>
              <div class="checkbox-set"  tabindex="0" id="deafIDdiv">
                <input type="checkbox"   id="deafID" name="deafID" value="True">
                <label for="deafID" class="whitecheckbox">I identify as <i>deaf or hearing impaired</i></label>
              </div>
            </fieldset>
           <div class="optional">
  			     <label class="icontab" for="pronouns">
              <i class="fa fa-user"></i>
             </label>
              <span class="custom-dropdown custom-dropdown--white">
                <select name="gender" id="gender" class="custom-dropdown__select custom-dropdown__select--white">
                  <option value=''>Gender...</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                  <option value="Non-Binary">Non-Binary</option>
                  <option value="Other">Other</option>
                </select>
              </span>
            </div>

            <div class="optional">
              <label class="icontab" for="pronouns">
                <i class="fa fa-user"></i>
              </label>
              <span class="custom-dropdown custom-dropdown--white">
                <select name="pronouns" id="pronouns" class="custom-dropdown__select custom-dropdown__select--white">
                  <option value=''>Select your pronouns...</option>
                  <option value="She/Her/Hers">She/Her/Hers</option>
                  <option value="He/Him/His">He/Him/His</option>
                  <option value="They/Them/Their(s)">They/Them/Their(s)</option>
                  <option value="Other">Other</option>
                </select>
              </span>
            </div>


    <h2>Employment Info</h2>

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
  <span class="custom-dropdown custom-dropdown--white">
  <?php select_state('mstate', ''); ?>
   </span>
</div>

<div class="optional">
  <label class="icon" for="mzip"><i class="fa fa-envelope"></i></label>
  <input type="text" name="mzip" id="mzip" class = "zip" placeholder="Mailing Zip" pattern="[0-9]*" />
</div>

<div class="required">
<label class="icon" for="remail"><i class="fa fa-keyboard-o"></i></label>
<input type="text" name="remail" id="remail" class="email" placeholder="Personal Email (required)" value="<?php echo $email; ?>" required/>
</div>





   <input type = "hidden" name="referrer" value="<?php echo $referrer; ?>">
   <input type = "hidden" name="campaign" value="<?php echo $campaign; ?>">

   <div id="messages"></div>

   <a href="" id="submit" class="button">Submit</a>
  </form>
  <p class="privacy"><a href="http://www.seiu.org/privacy/" target="_blank">Privacy Policy</a></p>
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

	$result = $mysqli->query("SET NAMES utf8");

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
    echo "<select name=\"$fieldname\" id=\"$fieldname\" class=\"custom-dropdown__select custom-dropdown__select--white\" $pseudoclass>\n";
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
