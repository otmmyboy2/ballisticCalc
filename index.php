<?php 
	session_start(); 
	require 'common.php';			//Common resources required by all pages(ie, translation, bootstrap library,etc)
	require_once("bc_db.php");		//Database connector and helper interfaces
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="assets/rifle.ico">
	
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-62675827-1', 'auto');
		ga('send', 'pageview');
	</script>
	
	<!-- Add jQuery library -->
	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
	
	<!-- Add interact library, used to move dots on target -->
	<script src="js/interact-1.2.4.min.js"></script>
	
	<!-- Charting library for calculation graphs -->
	<script src="http://code.highcharts.com/highcharts.js"></script>
	
	<!-- Bootstrap CSS v3.0.0 or higher -->
	<link rel="stylesheet" href="css/bootstrap.min.css">
	
	<!-- My Stylesheet -->
	<link rel="stylesheet" href="css/style.css">
	
	<!-- FormValidation CSS file -->
	<link rel="stylesheet" href="formvalidation/dist/css/formValidation.min.css">

	<!-- Bootstrap JS -->
	<script src="js/bootstrap.min.js"></script>

	<!-- FormValidation plugin and the class supports validating Bootstrap form -->
	<script src="formvalidation/dist/js/formValidation.min.js"></script>
	<script src="formvalidation/dist/js/framework/bootstrap.min.js"></script>
		
	<!-- Add fancyBox -->
	<link rel="stylesheet" href="fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
	<script type="text/javascript" src="fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>
		
    <title>Ballistic Calculator</title>
  </head>

  <body>
	
    <nav class="navbar navbar-inverse navbar-fixed-top">		<!-- All part of the navbar -->
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="?p=home">Ballistic Calculator</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
		
		<?php		
			if (isset($_SESSION['uid'])) {				//If user is logged in, display logout button
				echo "<div class='navbar-form navbar-right'>";
					echo "<a href='?p=loggingOut' class='btn btn-danger' value='Logout'>Logout</a>";
				echo "</div>";
			}
			else{										//Else show login form
				echo "<form method='POST' action='loginBackend.php' id='loginForm' class='navbar-form navbar-right'>";
					echo "<div class='form-group'>";
						echo "<input type='email' id='email' name='email' placeholder='Email' class='form-control'>";
					echo "</div>";
					echo " ";
					echo "<div class='form-group'>";
						echo "<input type='password' id='password' name='password' placeholder='Password' class='form-control'>";
					echo "</div>";
					echo " ";
					echo "<button type='submit' class='btn btn-success' value='Login'>Log in</button>";
				echo "</form>";
			}
		?>
        		  
        </div><!--/.navbar-collapse -->
      </div>
    </nav>

    <!-- Main jumbotron, used for QA page -->
    <div class="jumbotron">
      <div class="container">
        <h1>Ballistic Calculation</h1>
        <p>What is it, why does it exist, what is it used for, why is the sky blue? Questions and answers galore after the jump....</p>
        <p><a class="btn btn-primary btn-lg" href="?p=qa" role="button">Q&A &raquo;</a></p>
      </div>
    </div>

    <?php 		 
		switch (isset($_GET['p']) ? $_GET['p'] : 'Error!') {
		case "home":							//Homepage
			include("home.php");
			break;
			
		case "contact":							//Contact us form
			include("contact.php");
			break;
			
		case "contact1":						//Contact form sending success
			include("contact1.php");
			include("contact.php");
			break;
			
		case "contact0":						//Contact form sending failure
			include("contact0.php");
			include("contact.php");
			break;
			
		case "calendar":						//Upcoming competition calendar
			include("calendar.php");
			break;
			
		case "login1":							//Login success
			include("login1.php");				
			include("home.php");
			break;
			
		case "login0":							//Login Failure
			include("login0.php");				
			include("home.php");
			break;
			
		case "calc":							//Ballistic calculation
			include("calc.php");
			break;
			
		case "browse":							//Browse ammunition/targets/calendar
			include("browse.php");
			break;
			
		case "qa":								//QA page
			include("qa.php");
			break;
			
		case "bAmmo":							//Browse ammunition page
			include("bAmmo.php");
			break;
			
		case "bTarget":							//Browse target page. Loads fancybox to display images prettily.?>						
			<script type="text/javascript">
				$(document).ready(function() {
					$(".fancybox").fancybox();
				});
			</script><?php	
			include("bTarget.php");
			break;
			
		case "loggingOut":						//Logs user out, clears session
			session_destroy();		
			echo "<script>window.location = 'index.php?p=logout'</script>";
			break;	
		
		case "logout":							//Displays the home page and logout confirmation
			include("logout.php");				
			include("home.php");
			break;

			
		case "calcBackend":						//Calculation results page?>
			<script type="text/javascript">
				$(document).ready(function() {
					$(".fancybox").fancybox();
				});
			</script><?php
			include("calcBackend.php");			
			break;
			
		default:
			echo "<script>window.location = 'index.php?p=home'</script>";
		}

		$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

		if (strpos($url,'home') == false && strpos($url,'logout') == false && strpos($url,'login0') == false && strpos($url,'login1') == false) {				//for all pages apart from home page show user "Back" and "Home" buttons
			echo '<ul class="pager">
				<li><a onclick="back()" style="cursor: default;" role="button">&laquo; Back</a></li>
				<li><a href="?p=home" style="cursor: default;" role="button">Home</a></li>
			</ul>';
		}
		?>
		<hr>
		<footer>
			<p>&copy; Sharpshooter Solutions 2015</p>		
			<?php 
			/*if (!function_exists("gettext"))		//checks if gettext is installed. Used for translation.
			{
				echo "gettext is not installed";
			}
			else
			{
				echo "gettext is installed";
			}
			//echo gettext("Back");*/
			?>
		</footer>
    </div> <!-- /container -->
  </body>
</html>

<script>
	$(document).ready(function() {
		$('#loginForm').formValidation({		//live form validation, not part of bootstrap framework but integrates well.
			framework: 'bootstrap',
			icon: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},err: {
				// You can set it to tooltip or popover
				container: 'popover'
			},
			fields: {
				email: {
					validators: {
						notEmpty: {
							message: 'Email is required.'
						},
						emailAddress: {
							message: 'This is not a valid email address.'
						}
					}
				},
				password: {
					validators: {
						notEmpty: {
							message: 'Password is required.'
						},
						stringLength: {
							min: 6,
							max: 30,
							message: 'Password must be between 6 and 30 characters.'
						}
					}
				}
			}
		});
	});
</script>
