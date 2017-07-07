<?php
// Database settings
$db_name="sharpsho_ballistic_calc";
$db_user="root";
$db_pass="root";
$db_host="localhost";

// Open link
$mysql =  new mysqli("$db_host", "$db_user", "$db_pass", "$db_name") or die("Database not available.");

// Set query count to 0
$sqlcount = 0;

// Error handler
function log_error($details) {

	global $mysql;

	$details = addslashes($details);

	$query = "INSERT INTO error(details, date_time) VALUES ('$details',NOW())";
	$result = @$mysql->query($query);
	$sqlcount += 1;

	$message = "<div class=\"error\"><p>Internal Error - The error has been logged in our system and will be fixed as soon as possible.</p></div>";
	return $message;
}

//database query
function dbq($sql) {
	global $mysql;
	global $sqlcount;
	global $err_tag;

	$query = $sql;
	$result = @$mysql->query($query) or die(@log_error( "$err_tag - ".$mysql->error." - SQL:--- ".$sql));
	$sqlcount += 1;
	return $result;
}

//get associative array record from database result
function dba($result, $result_type = MYSQLI_ASSOC) {
	return $result->fetch_array($result_type);
}

//get query result and return it as an array, or as a value if only one is returned from the query
//(don't use this where there is more than one record returned by the query) just use dbq and loop with dba
function dbqa($sql, $result_type = MYSQLI_ASSOC) {
	$result = dbq($sql);
	$r = array();
	while($row = dba($result, $result_type))
		$r[] = $row;
	switch(count($r)) {
		case 0:
			return null;
		case 1:
			if(count($r[0]) == 1) {
				return end($r[0]);
			} else {
				return $r[0];
			}
		default:
			return $r;
	}
	while($row = dba($result))
		$r[] = $row;
	return (count($r) == 1? (count($r[0]) == 1? end($r[0]): $r[0]): $r);
}

//get count of records from database result
function dbn($result) {
	return $result->num_rows;
}


function dbe($str) {
	global $mysql;
	return $mysql->real_escape_string($str);
}


function dbi() {
	global $mysql;
	return $mysql->insert_id;
}

function dbs($result, $offset = 0) {
	$result->data_seek($offset);
}

//get count of records from database query
function dbqn($sql) {
	global $mysql;
	global $sqlcount;
	global $err_tag;

	$query = $sql;
	$result = @$mysql->query($query) or die(log_error( "$err_tag - ".$mysql->error." - SQL:--- ".$sql));
	$sqlcount += 1;

	return $result->num_rows;
}

?>
