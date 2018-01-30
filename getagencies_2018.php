<?php


$agencytype = $_GET["agencytype"];

$agencytype = (int) $agencytype; //must do conversion for parameterized queries to work



	$mysqli = new mysqli("localhost", "memberappuser", "memberapppass", "memberapp");

	if ($mysqli->connect_errno) {
		//echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

	if (!($stmt = $mysqli->prepare("SELECT Agency_Number,Agency_Name FROM agencies WHERE Agency_Type = (?)"))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt->bind_param("i", $agencytype)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	/* Select queries return a resultset */
	$result = $stmt->bind_result($agency_num,$agency_name);


	echo "<div class='ff-col-1 ff-label-col' id='agency-list-label'><label class='ff-label'>Employer</label><span class='requiredSpan ff-required-mark'>*</span></div><div class='ff-col-2 ff-field-col' id='agency-list'>";
  echo "<span class=\"ff-select-type ff-singlepicklist\" id=\"agency-list-wrap\">";
	echo "<select name=\"agencynumber\" class=\"inner-select\" required>\n";
	echo "<option value=''>Select an agency...</option>";

	while ($stmt->fetch()) {
			printf("<option value='%s'>%s</option>\n", $agency_num,$agency_name);
	}

	echo "</span>";

	$stmt->close();
	$mysqli->close();

	echo "</select></div>";

?>