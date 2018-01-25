<?php


$agencytype = $_GET["agencytype"];

$agencytype = (int) $agencytype; //must do conversion for parameterized queries to work

 
	
	$mysqli = new mysqli("localhost", "memberappuser", "memberapppass", "memberapp");
	
	if ($mysqli->connect_errno) {
		//echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	
	if (!($stmt = $mysqli->prepare("SELECT Agency_Number,Agency_Name FROM agencies WHERE Agency_Type = (?)"))) {
		//echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
		
	if (!$stmt->bind_param("i", $agencytype)) {
		//echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}
		
	if (!$stmt->execute()) {
		//echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	/* Select queries return a resultset */
	$result = $stmt->bind_result($agency_num,$agency_name);

	echo "<label class=\"icontab\"><i class=\"fa fa-cog\"></i></label>";
	echo "<span id=\"agencynumber\" class=\"custom-dropdown custom-dropdown--purple\">";
	echo "<select name=\"agencynumber\" class=\"custom-dropdown__select custom-dropdown__select--purple\" required>\n";
	echo "<option value=''>Select an agency...</option>";
	
	while ($stmt->fetch()) {
			printf("<option value='%s'>%s</option>\n", $agency_num,$agency_name);
	}
		
	echo "</span>";
	
	$stmt->close();
	$mysqli->close();
	
	echo "</select>";
	
?>