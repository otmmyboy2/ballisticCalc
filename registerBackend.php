<?php session_start();
	require_once("bc_db.php");

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
	//echo $email;
	//echo "\r\n";
	//echo $password;
	
	if(empty($value)){
		echo '<script language="javascript">';
		echo 'setTimeout(function(){alert("Please enter in data for both the Email and Password fields.")}, 1);';
		echo '</script>';
		echo "<script>window.location = 'index.php?p=login0'</script>";
	}
	else{
		/**
		 * We just want to hash our password using the current DEFAULT algorithm.
		 * This is presently BCRYPT, and will produce a 60 character result.
		 *
		 * Beware that DEFAULT may change over time, so you would want to prepare
		 * By allowing your storage to expand past 60 characters (255 would be good)
		 */
		$password = password_hash($password, PASSWORD_BCRYPT)."\n";
		
		$sql = "INSERT INTO users(email, password) VALUES ('".$email."','".$password."')";

		$result = dbqn($sql);
		if($result > 0)
		{
			echo "User registered!";
			
			echo "<script>window.location = 'index.php?p=home'</script>";
		}
		else{
			echo '<script language="javascript">';
			echo 'setTimeout(function(){alert("Invalid Login.")}, 1);';
			echo '</script>';
			echo "<script>window.location = 'index.php?p=login0'</script>";
		}
	}
?>



