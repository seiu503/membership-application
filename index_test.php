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
    <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
    <meta content="utf-8" http-equiv="encoding">
    <link rel="icon" type="image/x-icon" href="favicon.ico">

    <title>Member Application</title>

    <link rel="stylesheet" href="https://seiu503signup.org/css/app_2018.css">

    <!--   google analytics -->
    <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-74074286-1', 'auto');
    ga('send', 'pageview');
    </script>

    <!-- WM facebook -->
    <!-- Facebook Pixel Code -->
    <script>
      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window, document,'script',
      'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', '992059957611940');
      fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
      src="https://www.facebook.com/tr?id=992059957611940&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Facebook Pixel Code -->

    <!--   pardot -->
    <script type="text/javascript">
    piAId = '172302'; piCId = '1470'; (function() { function async_load(){ var s = document.createElement('script'); s.type = 'text/javascript'; s.src = ('https:' == document.location.protocol ? 'https://pi' : 'http://cdn') + '.pardot.com/pd.js'; var c = document.getElementsByTagName('script')[0]; c.parentNode.insertBefore(s, c); if(window.attachEvent) { window.attachEvent('onload', async_load); } else { window.addEventListener('load', async_load, false); } }})();
    </script>

    <!--   libraries -->
    <script src="https://seiu503signup.org/scripts/jquery-1.11.1.min.js" type="text/javascript"></script>
    <script src="https://cdn.optimizely.com/js/5716112250.js"></script>
    <script src="https://seiu503signup.org/scripts/inputmask/inputmask.dependencyLib.js"></script>
    <script src="https://seiu503signup.org/scripts/inputmask/inputmask.js"></script>
    <script src="https://seiu503signup.org/scripts/inputmask/inputmask.extensions.js"></script>
    <script src="https://seiu503signup.org/scripts/inputmask/inputmask.numeric.extensions.js"></script>
    <script src="https://seiu503signup.org/scripts/inputmask/inputmask.date.extensions.js"></script>
    <script src="https://seiu503signup.org/scripts/inputmask/inputmask.phone.extensions.js"></script>
    <script src="https://seiu503signup.org/scripts/jquery.waituntilexists.js" type="text/javascript"></script>

    <!--   scripts -->
    <script type="text/javascript" src="https://seiu503signup.org/scripts/agencylist_2018.js"></script>
    <script type="text/javascript" src="https://seiu503signup.org/scripts/signup_script.js"></script>


    </script>
  </head>

  <body>
    <div class="header">
      <div class="banner">
        <div class="logo">
          <img id="logo" alt="SEIU 503 In It Together" src="images/SEIU503_InItTogether.png" />
        </div>
      </div>
    </div>
    <div class="container">
      <div id="memberapp">
        <h1>Commiting to SEIU 503 means it can fight for you and your fellow workers.</h1>
        <hr>
        <p class="instructions">
          Please complete the following form to join the tens of thousands of care providers and public service workers who make Oregon a great place to work and live. By doing so, you will commit to maintaining your membership for one year, or paying a non-member fee equivalent. Your full name, network address, and a timestamp of your submission will serve as your signature.
        </p>
        <hr>
        <!-- formstack form -->
        <script type="text/javascript" id="jsFastForms" src="https://sfapi.formstack.io/FormEngine/Scripts/Main.js?d=fA7bKuk39vCPchdNMeGXfXqG7ugrV6lc1yLNJDyVWVys8VYkDIBldsls85c3EeqI"></script>

        <div id="messages"></div>
        <div class="loader__wrap" id="spinner">
          <div class="loader__bkg">
            <div class="loader">
              Loading...
            </div>
          </div>
         </div>

        <p class="privacy"><a href="http://www.seiu.org/privacy/" target="_blank">Privacy Policy</a></p>
      </div>
    </div>
    <div id="hidden-php-vars">
      <?php select_agencytype(); ?>
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
  echo "<select name=\"unit\" id=\"unit\" class=\"inner-select\" onChange=\"showAgencies()\" required>\n";
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
