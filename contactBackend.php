<!-- Backend of contact form, uses PHPMailer to send email to account specified. Combined model & controller. -->
<?php
	require '/PHPMailer-master/PHPMailerAutoload.php';
	$email = $_POST["email"];
	$comments = $_POST["comments"];

	if(empty($email) || empty($comments)){	//Form validation for older browsers

		echo '<script language="javascript">';
		echo 'setTimeout(function(){alert("Please enter in data for all of the fields.")}, 1);';
		echo '</script>';
		echo "<script><script>window.history.back()</script>";
	}
	else{
		$mail = new PHPMailer();
		
		$mail->IsSMTP();
		//$mail->SMTPDebug = 1;
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = 'tls';
		$mail->Host = "smtp.gmail.com";
		$mail->Port = 587;
		$mail->IsHTML(true);

		$mail->Username = "XXXXX";
		$mail->Password = "XXXXX";
		 
		$mail->SetFrom = "commentsForm@sharpshooterie.com";
		$mail->FromName = "Contact Form";
		$mail->AddAddress("XXXXX");

		$mail->Subject = "Re: Contact Form";
		$mail->Body = "Email: $email <br>
						Comments: $comments";
		 
		if(!$mail->Send()){
			echo "Message could not be sent. <p>";
			echo "Mailer Error: " . $mail->ErrorInfo;
			exit;
		}
		$mail->SMTPDebug = 1;
		
		//echo "Message has been sent";
		//echo "<br>Email: $email<br>Comments: $comments<br>";

		$string = "Email: {$email}, Comments: {$comments}";
		echo "<script>window.location = 'index.php?p=contact1'</script>";
		/*$sql = "INSERT INTO email (email, comments, timestamp) VALUES ('".$email."', NOW())";
				dbq($sql);	
				echo "<script>window.location = '/home/'</script>";
		*/
	}
?>