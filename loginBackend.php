<?php session_start();
	require_once("bc_db.php");
	require("password_compat-master/lib/password.php");
	if(empty($_POST)){

		echo '<script language="javascript">';
		echo 'setTimeout(function(){alert("Please enter in data for both the Email and Password fields.")}, 1);';
		echo '</script>';
		echo "<script>window.location = 'index.php?p=login0'</script>";
	}
	else{
		foreach ( $_POST as $key => $value )
		{
			if ( ( !is_string($value) && !is_numeric($value) ) || !is_string($key) )
				continue;

			if ( get_magic_quotes_gpc() )
				$value = htmlspecialchars( stripslashes((string)$value) );
			else
				$value = htmlspecialchars( (string)$value );

			//key		
			//echo htmlspecialchars( (string)$key );
			 
			if(isset($email)){
				$password = $value;
			}
			else{
				$email = $value;
			}
		}
	}
	/*echo $email;
	echo "\r\n";
	echo $password;*/
	
	if(empty($value)){
		echo '<script language="javascript">';
		echo 'setTimeout(function(){alert("Please enter in data for both the Email and Password fields.")}, 1);';
		echo '</script>';
		echo "<script>window.history.back()</script>";
	}
	else{
		/*$password = password_hash($password, PASSWORD_BCRYPT)."\n";*/
		
		$hash = "SELECT password FROM users WHERE email = '".$email."'";
		
		if ($result = dbq($hash)) {
			/* fetch object array */
			while ($row = $result->fetch_row()) {
				//printf ("%s", $row[0]);
				$hash = $row[0];
			}
			/* free result set */
			$result->close();
		}
		

		if (password_verify($password, $hash)) {		
			$sql = "SELECT email, password FROM users WHERE email = '".$email."' AND password = '".$hash."'";
			
			$result = dbqn($sql);
			if($result > 0)
			{
				//echo "Valid login!";
				$_SESSION['uid']=$email;
				echo "<script>window.location = 'index.php?p=login1'</script>";
			}
			else{
				echo '<script language="javascript">';
				echo 'setTimeout(function(){alert("Invalid Login.")}, 1);';
				echo '</script>';
				echo "<script>window.location = 'index.php?p=login0'</script>";
			}
		} else {
			echo '<script language="javascript">';
			echo 'setTimeout(function(){alert("Invalid Login.")}, 1);';
			echo '</script>';
			echo "<script>window.location = 'index.php?p=login0'</script>";
		}
	}
?>



