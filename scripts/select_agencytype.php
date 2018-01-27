function debug_to_console( $data ) {
    $output = $data;
    if ( is_array( $output ) )
        $output = implode( ',', $output);

    echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
  }

debug_to_console( "Test" );

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

  debug_to_console( $result );

	/* free result set */
	$result->close();
}

$mysqli->close();

echo "</select>";



