<?php
include_once 'dbConfig.php'; 
//$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
$con= mysqli_connect(HOST,USER,PASSWORD,DATABASE);

// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

//$result = mysqli_query($con,"SELECT * FROM ".TABLE);

/*while($row = mysqli_fetch_array($result))
  {
  echo $row['usrName'] . " " . $row['usrMail'];
  echo "<br>";
  }*/

//mysqli_close($con);

//mysql_connect(HOST, USER, PASSWORD)or die("cannot connect"); 
//mysql_select_db(DATABASE)or die("cannot select DB");


?>