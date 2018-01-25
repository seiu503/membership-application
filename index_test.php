<?php
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

  function mysqli_con() {
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
      if ($key == '') {
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
      if (!$stmt->execute() ) {
        $stmterror = $stmt->error;
        //echo "Execute Statement Error: $stmterror\n";
        echo "Database or data issue. Please try again later.";
        exit;
      }

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
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0">
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <link rel="icon" type="image/x-icon" href="favicon.ico">

    <title>Member Application</title>

    <link rel="stylesheet" href="css/app_20171004.css">
    <link rel="stylesheet" href="font-awesome-4.1.0/css/font-awesome.css">

    <!--   google analytics -->
    <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-74074286-1', 'auto');
    ga('send', 'pageview');
    </script>

    <!--   pardot -->
    <script type="text/javascript">
    piAId = '172302'; piCId = '1470'; (function() { function async_load(){ var s = document.createElement('script'); s.type = 'text/javascript'; s.src = ('https:' == document.location.protocol ? 'https://pi' : 'http://cdn') + '.pardot.com/pd.js'; var c = document.getElementsByTagName('script')[0]; c.parentNode.insertBefore(s, c); if(window.attachEvent) { window.attachEvent('onload', async_load); } else { window.addEventListener('load', async_load, false); } })();
    </script>

    <!--   libraries -->
    <script src="scripts/jquery-1.11.1.min.js" type="text/javascript"></script>
    <script src="scripts/jquery.inputmask.js" type="text/javascript"></script>
    <script src="https://cdn.optimizely.com/js/5716112250.js"></script>
    <script src="scripts/jquery.inputmask.numeric.extensions.js" type="text/javascript"></script>
    <script src="scripts/jquery.inputmask.regex.extensions.js" type="text/javascript"></script>
    <script src="scripts/jquery.inputmask.date.extensions.js" type="text/javascript"></script>
    <script type="text/javascript" src="scripts/agencylist.js"></script>
    <script type="text/javascript" src="scripts/signup_script.js"></script>


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
        <h1>Building a Just and Vibrant Society</h1>
        <div class="center">
          <img class="banner-img" src="https://storage.pardot.com/171302/38383/BannerCollage.jpg" alt="SEIU Local 503 members at work"/>
        </div>
        <hr>
        <p class="instructions">
          Please complete the following form to join the tens of thousands of care providers and public service workers who make Oregon a great place to work and live. By doing so, you will commit to maintaining your membership for one year, or paying a non-member fee equivalent. Your full name, network address, and a timestamp of your submission will serve as your signature.
        </p>
        <hr>
        <form id="memberappform" action="https://sfapi.formstack.io/FormEngine/EngineFrame/UploadFile" enctype="multipart/form-data" id="form1" method="post" target="ffIframe">

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

          <h2>Person Info</h2>

           <div class="required">
              <label class="icon" for="Contact.FirstName"><i class="fa fa-user"></i></label>
              <input type="text" name="Contact.FirstName" id="Contact.FirstName" placeholder="First Name" required/>
           </div>

          <div class="required">
            <label class="icon" for="Contact.LastName"><i class="fa fa-user"></i></label>
            <input type="text" name="Contact.LastName" id="Contact.LastName" placeholder="Last Name" required/>
          </div>

          <div class="required">
            <label class="icon" for="Contact.Birthdate"><i class="fa fa-calendar"></i></label>
            <input type="text" name="Contact.Birthdate" id="Contact.Birthdate" class="date_ddmmyyyy" placeholder="Date of Birth" pattern="[0-9]*" required/>
          </div>

          <div class="required">
            <label class="icontab" for="Contact.Preferred_Language__c"><i class="fa fa-cog"></i></label>
            <span class="custom-dropdown custom-dropdown--purple">
              <select name="Contact.Preferred_Language__c" id="Contact.Preferred_Language__c" class="custom-dropdown__select custom-dropdown__select--purple">
                <option value="">Select preferred Language...</option>
                <option value="English">English</option>
                <option value="Spanish">Español</option>
                <option value="Russian">Русский</option>
                <option value="Vietnamese">Tiếng Việt</option>
                <option value="Mandarin">普通话</option>
                <option value=“Cantonese”>粤语</option>
                <option value="ASL (Sign Language)">ASL (Sign Language)</option>
                <option value="Korean">한국어</option>
                <option value="Other">Other</option>
                <option value="Somali">Soomaali</option>
                <option value="Amharic">አማርኛ</option>
                <option value="Romanian">Română</option>
                <option value="Tagalog">Tagalog</option>
              </select>
            </span>
          </div>

          <h2>Home Address</h2>

          <div class="required">
            <label class="icon" for="Contact.MailingStreet"><i class="fa fa-home"></i></label>
            <input type="text" name="Contact.MailingStreet" id="Contact.MailingStreet" placeholder="Home Street Address" required/>
          </div>

          <div class="required">
            <label class="icon" for="Contact.MailingCity"><i class="fa fa-home"></i></label>
            <input type="text" name="Contact.MailingCity" id="Contact.MailingCity" placeholder="Home City" required/>
          </div>

          <div class="required">
            <label class="icontab" for="Contact.MailingState"><i class="fa fa-home"></i></label>
            <span class="custom-dropdown custom-dropdown--purple">
              <?php select_state('Contact.MailingState', ''); ?>
            </span>
          </div>

          <div class="required">
            <label class="icon" for="Contact.MailingPostalCode"><i class="fa fa-home"></i></label>
            <input type="text" name="Contact.MailingPostalCode" id="Contact.MailingPostalCode" class = "zip" placeholder="Home Zip" pattern="[0-9]*" required/>
          </div>

          <h2>Contact Info</h2>

          <div class="required">
            <label class="icon" for="Contact.Home_Email__c"><i class="fa fa-keyboard-o"></i></label>
            <input type="text" name="Contact.Home_Email__c" id="Contact.Home_Email__c" class="email" placeholder="Personal Email" required/>
          </div>

          <div class="required">
            <div class="mobile">
              <label class="icon" for="Contact.MobilePhone"><i class="fa fa-mobile-phone"></i></label>
              <input type="text" name="Contact.MobilePhone" id="Contact.MobilePhone" class="phone_mob" placeholder="Primary Phone" pattern="[0-9]*" required />
            </div>
          </div>

          <div class="phone" id="smsdiv">
            <input type="checkbox" value="checked" id="Contact.tdc_tsw__SMS_Opt_out__c" name="Contact.tdc_tsw__SMS_Opt_out__c" />
            <label for="Contact.tdc_tsw__SMS_Opt_out__c" class="whitecheckbox" tabindex="0">Opt out of receiving mobile alerts&dagger;</label>
            <p class="small">&dagger; By providing my phone number, I understand that the Service Employees International Union (SEIU), its local unions, and affiliates may use automated calling technologies and/or text message me on my cellular phone on a periodic basis. SEIU will never charge for text message alerts. Carrier message and data rates may apply to such alerts. Reply STOP to stop receiving messages; reply HELP for more information.
          </div>

          <div id = "signature">
            <h2>Signature</h2>
            <div class="required">
              <label class="icon" for="fulContact.LastName"><i class="fa fa-pencil"></i></label>
              <input type="text" name="fulContact.LastName" id="fulContact.LastName" placeholder="Full Name for Signature" required/>
            </div>
            <p class="small">Enter your full legal name. This will act as your signature.</p>
          </div>

          <div id="termsdiv" tabindex="0">
            <input type="checkbox" value="checked" id="termsagree" name="termsagree">
            <label for="termsagree" class="checkbox">I agree to the membership <a href="#terms" onClick="terms()">terms</a></label>
          </div>

          <div class="memberterms">
            <p class="small">
              Your full name, the network address you are accessing this page from, and the timestamp of submission will serve as signature indicating: I hereby designate SEIU Local 503, OPEU (or any successor Union entity) as my desired collective bargaining agent. I also hereby authorize my employer to deduct from my wages, commencing with the next payroll period, all Union dues and other fees or assessments as shall be certified by SEIU Local 503, OPEU (or any successor Union entity) and to remit those amounts to such Union. This authorization/delegation is unconditional, made in consideration for the cost of representation and other actions in my behalf by the Union and is made irrespective of my membership in the Union. This authorization is irrevocable for a period of one year from the date of execution and from year to year thereafter unless not less than thirty (30) and not more than forty-five (45) days prior to the end of any annual period or the termination of the contract between my employer and the Union, whichever occurs first, I notify the Union and my employer in writing, with my valid signature, of my desire to revoke this authorization.
              <a href="#hideterms" onClick="terms()">hide terms</a>
            </p>
          </div>

           <input type = "hidden" name="referrer" value="<?php echo $referrer; ?>">
           <input type = "hidden" name="campaign" value="<?php echo $campaign; ?>">
           <input type="hidden" name="Contact.OtherStreet" id="Contact.OtherStreet"/>
           <input type="hidden" name="Contact.OtherCity" id="Contact.OtherCity"  />
           <input type="hidden" name="Contact.OtherState" id="Contact.OtherState" />
           <input type="hidden" name="Contact.OtherPostalCode" id="Contact.OtherPostalCode"  />
           <input type="hidden" name="Contact.Account_name_Pardot_sync__c" id="Contact.Account_name_Pardot_sync__c"  />

          <div id="messages"></div>

          <a href="" id="submit" class="button">Sign up</a>
        </form>

        <!--  Hidden form with values to post to MDB, field names match MDB fields -->
        <form action="https://seiu503signup.org/process_new.php" method="post" name="hidden_form">
          <input type="hidden" name="email" value="%%email{html}%%" />
          <input type="hidden" name="company" value="%%company{html}%%" />
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
