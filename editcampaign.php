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


<?php 

$allowedip = 0;

$ipaddress = (string) $_SERVER['REMOTE_ADDR'];
$ipaddress = cleanstring($ipaddress, 'ip');

if(($ipaddress == '204.202.193.3') or ($ipaddress == '38.104.105.18') or ($ipaddress == '67.168.208.186'))
{
	$allowedip = 1;
}

if($allowedip)
{
	/* Start of allowed ip */
	/**********************************************************/
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





$stmt->close();
$db->close();

	
	
$typeDef = array();
$data = array();

$page = 'editcampaign.php';

$data['Page'] = $page;
$typeDef[] = 's';


$useragent = (string) $_SERVER['HTTP_USER_AGENT'];
$useragent = cleanstring($useragent, 'useragent');
$data['UserAgent'] = $useragent;
$typeDef[] = 's';


$data['IP_Address'] = $ipaddress;
$typeDef[] = 's';


$mysqli = new mysqli("localhost", "memberappuser", "memberapppass", "memberapp");

$table = 'views';	

$insert = error_sql_insert($data, $typeDef, $table);

//echo $insert;

$mysqli->close();



?>

<!DOCTYPE html>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
   <link rel="icon" type="image/x-icon" href="favicon.ico">

  <title>Edit Campaign <?php echo $campaign; ?></title>

  <link rel="stylesheet" href="css/app.css">
  <link rel="stylesheet" href="font-awesome-4.1.0/css/font-awesome.css">
  <script src="scripts/jquery-1.11.1.min.js" type="text/javascript"></script>
  <style> 
  .container { width: 900px !important;}
  #memberapp { width: 700px !important;}
  </style>
  
  <script type="text/javascript">
   $(function() {
    
	validform = 1;
	
	$( "#submit" ).on('click', function( event ) {
		
		if (validform == 1) {
		event.preventDefault();
		$("form").submit();
		return;
		}
		
	});
  });
  </script>
</head>
<body>
<div class="container">
<div id="memberapp">
<h1>Campaign Code: <?php echo $campaign; ?></h1>
<hr>
  <form id="campaignchange" action="updatecampaign.php" method="post">
 <h2>Main Page</h2>
 <div class="required"><label>In Page CSS</label><textarea id = 'inpagecss' rows='4' cols='59' name='specialinstructions'><?php echo $inpagecss; ?></textarea></div>
 <div class="required"><label>Header HTML</label><textarea id = 'headerhtml' rows='4' cols='59' name='specialinstructions'><?php echo $headerhtml; ?></textarea></div>
 <div class="required"><label>Main Text HTML</label><textarea id = 'maintexthtml' rows='4' cols='59' name='specialinstructions'><?php echo $maintexthtml; ?></textarea></div>
 <div class="required"><label>Footer HTML</label><textarea id = 'footerhtml' rows='4' cols='59' name='specialinstructions'><?php echo $footerhtml; ?></textarea></div>

<h2>Thank You Page</h2>
 <div class="required"><label>In Page CSS</label><textarea id = 'tyinpagecss' rows='4' cols='59' name='specialinstructions'><?php echo $tyinpagecss; ?></textarea></div>
 <div class="required"><label>Header HTML</label><textarea id = 'tyheaderhtml' rows='4' cols='59' name='specialinstructions'><?php echo $tyheaderhtml; ?></textarea></div>
 <div class="required"><label>Main Text HTML</label><textarea id = 'tymaintexthtml' rows='4' cols='59' name='specialinstructions'><?php echo $tymaintexthtml; ?></textarea></div>
 <div class="required"><label>Footer HTML</label><textarea id = 'tyfooterhtml' rows='4' cols='59' name='specialinstructions'><?php echo $tyfooterhtml; ?></textarea></div>
 
<a href="" id="submit" class="button">Save</a>
</form>
</div>
</div>
</body>
</html>



<?php 
} 
/* End of allowed ip */
/**********************************************************/
else
{
	echo "Page access denied.";
}


