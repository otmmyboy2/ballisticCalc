<div class="container">
	<?php
		require_once("bc_db.php");
		$ammoId = $_POST["ammoInput"];
		$targetId = $_POST["targetInput"];
		$barrel = $_POST["barrelInput"];
		if (array_key_exists('range1Input', $_POST)) {
			$range1 = $_POST["range1Input"];
		}else{
			$range1 = $_POST["range3Input"];
			$range2 = $_POST["range2Input"];
			$range3 = $_POST["range3Input"];
		}
		$height = $_POST["heightInput"];
		$crossDir = $_POST["crosswindDirInput"];
		$crossSpeed = $_POST["crosswindSpeedInput"];
		$cm = 5.857142587142;
		$in = 14.8771726114880;
		
		
		if(empty($ammoId) || empty($targetId) || empty($crossDir)){
			echo '<script language="javascript">';
			echo 'setTimeout(function(){alert("Please enter in data for all of the fields.")}, 1);';
			echo '</script>';
			echo "<script>window.history.back()</script>";
		}elseif(is_numeric($barrel) && is_numeric($height) && is_numeric($crossDir) && is_numeric($crossSpeed) && $barrel > 0 && $height >= 0 && $crossSpeed >= 0){
		
			$height = floatval($height);
			$barrel = floatval($barrel);
			$request = "SELECT * FROM ammunition WHERE id = '".$ammoId."'";
			
			if ($result = dbq($request)) {
				/* fetch object array */
				while ($row = $result->fetch_row()) {
					$bWeight = $row[2];
					$diameter = $row[3];
					$velocity = $row[4];
					$sd = $row[5];
					$bc = $row[6];
				}
				/* free result set */
				$result->close();
			}
			
			$maxBarrel = 0;
			if($diameter == 0.223){
				$maxBarrel = 26;			
			}else if($diameter == 0.308){
				$maxBarrel = 28;	
			}else if($diameter == 0.300){
				$maxBarrel = 24.25;	
			}
			echo "<table style='width:100%'><th>Technical Data:</th>";
			//echo "<tr><td>Range</td><td>$range1</td></tr>";
			//echo "<tr><td>Range the rifle is zeroed at (Yards)</td><td>$range2</td></tr>";
			//echo "<tr><td>Range other than the rifle is zeroed at (Yards)</td><td>$range3</td></tr>";
			
			//Formula to get reduction in muzzle velocity from a 26 inch barrel to the user defined barrel.
			$mvReduction = $maxBarrel - $barrel;		//Difference between the user barrel and the max barrel length
			$mvReduction *= $sd;						//Multiply the result by the standard deviation to get the muzzle velocity lost from the optimum
			$mv = $velocity - $mvReduction; 			//velocity of ammunition - velocity lost through barrel length to get the muzzle velocity
			echo "<tr><td>Muzzle Velocity(FPS)</td><td>$mv</td></tr>";
			
			//Formula to get the remaining velocity of the projectile.
			$rv = sqrt($mv);							//First square root the muzzle velocity gotten from the formula above.
			$rv1 = $range1 / $bc;						//Divide the range by the ballistic coefficient
			$rv1 *= 0.00863;						
			$rv -= $rv1;								//Subtract rv1 from rv and store in rv
			$rv = pow($rv, 2);							//rv to the power of 2 to get the solution
			echo "<tr><td>Remaining Velocity(FPS)</td><td>$rv</td></tr>";
			
			//Formula to get the a dummy variable to simplify the overall equation
			$k = sqrt($mv) * $bc;						//Square root of muzzle velocity times ballistic coefficient
			$k = 2.878 / $k;							//2.878 over $k to get the result
			echo "<tr><td>Dummy Variable 1</td><td>$k</td></tr>";
			
			//Formula to get the flight time of the projectile
			$tf = $range1 * $k;
			$tf *= 0.003;
			$tf = 1 - $tf;
			$tf *= $mv;
			$tf1 = $range1 * 3;
			$tf = $tf1 / $tf;
			if($tf < 0){
				$tf *=-1;
			}
			if(empty($range2) && empty($range3)){
				echo "<tr><td>Flight Time of Projectile(Seconds)</td><td>$tf</td></tr>";
			}
			
			//Another dummy variable for simplifying the equation.
			$f = $mv - $rv;
			$f *= 0.37;
			$f = $f / $mv;
			$f = 1 - $f;
			$f *= 193;
			//echo "Dummy Variable 2: ",$f,"<br>";
			
			//Formula for total drop from line of departure.
			$dr = pow($tf, 2);
			$dr *= $f;
			if(empty($range2) && empty($range3)){
				echo "<tr><td>Total Drop from line of Departure(Inches)</td><td>$dr</td></tr>";
			}
				
			//Wind deflection in a crosswind(10 MPH) from 9 oclock or 3 oclock.
			if(empty($range2) && empty($range3)){
				$wd = 3 * $range1;
			}else{
				$wd = 3 * $range3;
			}
			$wd = $wd / $mv;
			$wd = $tf - $wd;
			$wd *= 180;
			$wd = $wd / 10;							//Originally intended to be a 10MPH crosswind these 2 lines allow the user to change the wind speed.
			$wd *= $crossSpeed;
			echo "<tr><td>Crosswind direction</td><td>$crossDir</td></tr>";
			if($crossDir == 3 && $wd > 0){						//If the crosswind is coming from 3oclock, the wind deflection will need to be in the minus plane.
				$wd *= -1;
			}
			echo "<tr><td>Crosswind Deflection(Inches, X axis)</td><td>$wd</td></tr>";
			
			//Maximum height of trajectory above sight line (inches).
			$mh = pow($tf, 2);
			$mh *= 48.6;
			$mh1 = 0.4 * $height;
			$mh = $mh - $mh1;
			if(empty($range2) && empty($range3)){
				echo "<tr><td>Maximum height of trajectory above sight line(inches)</td><td>$mh</td></tr>";
			}
			
			//Get target measurements, image location and unit of measurement.
			$requestTarget = "SELECT * FROM target WHERE id = '".$targetId."'";
			if ($requestTarget = dbq($requestTarget)) {
				/* fetch object array */
				while ($rowTarget = $requestTarget->fetch_row()) {
					$image = $rowTarget[4];
					$unit = $rowTarget[5];
					//echo "Unit of measurement: ",$unit,"<br>";
				}
				/* free result set */
				$requestTarget->close();
			}
			$targetX = $wd;
			$targetX *= $in;
			
			//If calculation is simple
			if(empty($range2) && empty($range3)){
				//Elevation required (MOA- one inch at 100 yards, 2 at 200, etc).
				$el = $dr + $height;
				$el *= 100;
				$el = $el / $range1;
				echo "<tr><td>Elevation(MOA)</td><td>$el</td></tr></table>";
				//Depending on what unit of measurement the target has used the targetY variable will calculate the relative distance the point of aim needs to be on in the Y axis.
				$targetY = $range1 / 100;
				$targetY *= $el;
				$mhG = $mh;			//Following two variables for the 2d graphs(max height of trajectory(mh) and time of flight(tf))
				$tfG = $tf;
				$tfG *= 1000;
				if($unit == 0){
					//$unit = "cm";
					//inches to cm, cm = inches / 0.39370
					$targetY *= $cm;
				}elseif($unit == 1){
					//$unit = "inches";
					//cm to inches, inches = cm * 0.39370
					$targetY *= $in;
				}
			}elseif(!empty($range2) && !empty($range3) && $range2 > 0 && $range3 > 0 ){	//Else calculation is complex
				//Remaining velocity using $range3.
				$rv2 = sqrt($mv);							
				$rv3 = $range3 / $bc;				
				$rv3 = 0.00863 * $rv3;						
				$rv2 -= $rv3;							
				$rv2 = pow($rv2, 2);
				echo "<tr><td>Remaining Velocity for Range 2(FPS)</td><td>$rv2</td></tr>";
			
				//Dummy variable using $range3.
				$f2 = $mv - $rv2;
				$f2 *= 0.37;
				$f2 = $f2 / $mv;
				$f2 = 1 - $f2;
				$f2 *= 193;
				//echo "Dummy Variable for Range 2: ",$f2,"<br>";
				
				//Flight time of projectile $range3.
				$tf2 = $range3 * $k;
				$tf2 *= 0.003;
				$tf2 = 1 - $tf2;
				$tf2 *= $mv;
				$tf3 = $range3 * 3;
				$tf2 = $tf3 / $tf2;
				echo "<tr><td>Flight Time of Projectile(Seconds)</td><td>$tf2</td></tr>";
				
				//Maximum height of trajectory above sight line (inches).
				$mh2 = pow($tf2, 2);
				$mh2 *= 48.6;
				$mh3 = 0.4 * $height;
				$mh2 = $mh2 - $mh3;
				echo "<tr><td>Maximum height of trajectory above sight line(inches)</td><td>$mh2</td></tr>";
				
				//Drop from line of departure $range2.
				$dr2 = pow($tf2, 2);
				$dr2 *= $f2;
				echo "<tr><td>Drop from line of Departure(Inches)</td><td>$dr2</td></tr>";
								
				//Elevation required for range other than zeroed (MOA).
				$el1 = $dr2 + $height;
				$el1 *= 100;
				$el1 = $el1 / $range3;
				echo "<tr><td>Elevation for range other than zeroed(MOA)</td><td>$el1</td></tr>";
				
				//Elevation required for range zeroed (MOA). First flight time and drop from line of departure need to be calculated for range2(the zeroed range).
				//Remaining velocity using $range2.
				$rv4 = sqrt($mv);							
				$rv5 = $range2 / $bc;				
				$rv5 = 0.00863 * $rv5;						
				$rv4 -= $rv5;							
				$rv4 = pow($rv4, 2);
				
				//Dummy variable using $range2.
				$f3 = $mv - $rv4;
				$f3 *= 0.37;
				$f3 = $f3 / $mv;
				$f3 = 1 - $f3;
				$f3 *= 193;
				
				//Flight time of projectile $range2.
				$tf4 = $range2 * $k;
				$tf4 *= 0.003;
				$tf4 = 1 - $tf4;
				$tf4 *= $mv;
				$tf5 = $range2 * 3;
				$tf4 = $tf5 / $tf4;
				
				//Drop from line of departure $range2.
				$dr3 = pow($tf4, 2);
				$dr3 *= $f3;
				
				//Elevation required for range zeroed(range2) in MOA.
				$el0 = $dr3 + $height;
				$el0 *= 100;
				$el0 = $el0 / $range2;
				echo "<tr><td>Elevation for range zeroed(MOA)</td><td>$el0</td></tr>";
				
				//Bullet path above or below line of sight (inches).
				$bp = $el0 - $el1;
				$bp *= $range3;
				$bp = $bp / 100;
				echo "<tr><td>Bullet Path above of below line of sight(Inches)</td><td>$bp</td></tr>";
				
				//Set the point of aim Y axis depending on the target's unit of measurement.
				$targetY = $range3 / 100;
				$targetY *= -$bp;
				if($unit == 0){
					//$unit = "cm";
					//inches to cm, cm = inches / 0.39370
					$targetY *= $cm;
				}elseif($unit == 1){
					//$unit = "inches";
					//cm to inches, inches = cm * 0.39370
					$targetY *= $in;
				}
				$aimX = $wd;
				$aimY = $range3 /100;
				$aimY *= $el1;
				echo "<th>Target measurements for current calculation:</th>";
				echo "<tr><td>X axis(Inches)</td><td>$aimX</td></tr>";
				echo "<tr><td>Y axis(Inches)</td><td>$aimY</td></tr>";
				$tfG = $tf4;		//Following two variables for the 2d graphs(max height of trajectory(mh) and time of flight(tf))
				$mhG = $mh2;
				$tfG *= 1000;
			}
			//echo "Aim X: ",$targetX,"<br>";
			//echo "Aim Y: ",$targetY,"<br>";
			echo "</table>";
			echo "</div>";
			
			echo "<div class='info-text'>";
			echo"<p class='text-info'><br><b>Click and drag the red dot(projectile impact point) or the green dot(aiming point) to desired location.<br>
				The green dot is the point to aim at to hit the red dot.</b></p>";
			echo "</div>";		

			echo "<div class='image-center'>";
				echo "<a class='fancybox' rel='group' href='assets/targets/$image.jpg'><img src='assets/targets/$image.jpg 'height='400px' width='auto' alt='$image' id='$image'/></a>";
				echo "<div class='dot-start'>";
					echo "<div class='circle draggable' draggable='true' style='position:absolute;left:50%;top:40%;'>";
						echo "<div class='circle-aim' style='position:absolute;left:".$targetX."px;bottom:".$targetY."px;'></div>";
					echo "</div>";
				echo "</div>";
			echo "</div>";
		}else{
			echo '<script language="javascript">';
			echo 'setTimeout(function(){alert("Invalid data entered.")}, 1);';
			echo '</script>';
			echo "<script>window.history.back()</script>";
		}
	?>
	<div class="container" style="position: relative; top: 100px;">
	<div id="yGraph" style="height: 300px;"></div>
	<div id="xGraph" style="height: 300px;"></div>
	<script>
		$(function () {
			var mhG = <?php echo $mhG; ?>;
			var tfG = <?php echo $tfG; ?>;
			var rangeG = <?php echo $range1; ?>;
			var wdG = <?php echo $wd; ?>;
			
			$('#yGraph').highcharts({
				chart: {
					type: 'spline'
				},
				title: {
					text: 'Height of Trajectory(Y Axis)'
				},
				subtitle: {
					text: 'Inches'
				},
				xAxis: {
					categories: ['Shooter', 'Half Distance - '+rangeG/2+' (Yards)', 'Target - '+rangeG+' (Yards)']
				},
				yAxis: {
					title: {
						text: 'Height(Y Axis, Inches)'
					}
				},
				tooltip: {
					crosshairs: true,
					shared: true
				},
				plotOptions: {
					spline: {
						marker: {
							radius: 4,
							lineColor: '#666666',
							lineWidth: 1
						}
					}
				},
				series: [ {
					name: 'Inches',
					marker: {
						symbol: 'circle'
					},
					animation: {
						duration: tfG
					},
					data: [{
						y: 0,marker: {symbol: 'url(http:assets/biathlon-641.png)'}},
						mhG, 
						{y: 0,marker: {symbol: 'url(http:assets/target.png)'}}]
				}],
				/*subtitle: {
					text: 'Not supported in IE6 and IE7',
					verticalAlign: 'bottom',
					align: 'right',
					y: null,
					style: {
						fontSize: '10px'
					}
				}*/
			});
			
			$('#xGraph').highcharts({
				chart: {
					type: 'spline'
				},
				title: {
					text: 'Wind Deflection of Trajectory(X Axis)'
				},
				subtitle: {
					text: 'Inches'
				},
				xAxis: {
					categories: ['Shooter', 'Half Distance - '+rangeG/2+' (Yards)', 'Target - '+rangeG+' (Yards)']
				},
				yAxis: {
					reversed: true,
					title: {
						text: 'Wind Deflection(X Axis, Inches)'
					}
				},
				tooltip: {
					crosshairs: true,
					shared: true
				},
				plotOptions: {
					spline: {
						marker: {
							radius: 4,
							lineColor: '#666666',
							lineWidth: 1
						}
					}
				},
				series: [ {
					name: 'Inches',
					marker: {
						symbol: 'circle'
					},
					animation: {
						duration: tfG
					},
					data: [{
						y: 0,marker: {symbol: 'url(http:assets/biathlon-641.png)'}},
						wdG/2, 
						{y: wdG,marker: {symbol: 'url(http:assets/target.png)'}}]
				}],
				/*subtitle: {
					text: 'Not supported in IE6 and IE7',
					verticalAlign: 'bottom',
					align: 'right',
					y: null,
					style: {
						fontSize: '10px'
					}
				}*/
			});
		});
		
		// target elements with the "draggable" class
		interact('.draggable')
		.draggable({
			// enable inertial throwing
			inertia: false,
			// keep the element within the area of it's parent
			restrict: {
				restriction: "parent",
				endOnly: true,
				elementRect: { top: 0, left: 0, bottom: 1, right: 1 }
			},
			// call this function on every dragmove event
			onmove: dragMoveListener,
			// call this function on every dragend event
			onend: function (event) {
				var textEl = event.target.querySelector('p');
				textEl && (textEl.textContent =
				'moved a distance of ' + (Math.sqrt(event.dx * event.dx + event.dy * event.dy)|0) + 'px');
			}
		});

		function dragMoveListener (event) {
			var target = event.target,
			// keep the dragged position in the data-x/data-y attributes
			x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx,
			y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

			// translate the element
			target.style.webkitTransform =
			target.style.transform =
			'translate(' + x + 'px, ' + y + 'px)';

			// update the posiion attributes
			target.setAttribute('data-x', x);
			target.setAttribute('data-y', y);
		}

		// this is used later in the resizing demo
		window.dragMoveListener = dragMoveListener;
	</script>
	
	