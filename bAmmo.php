<!--This class allows the user to browse the currently available ammunition stored in the db.-->
<div class='container'>
	<table class="table table-bordered table-hover table-responsive">
		<tr>
			<th>Cartridge Name</th>
			<th>Bullet Weight(GR)</th> 
			<th>Bullet Diameter</th>
			<th>Velocity(FPS)</th> 
			<th>Ballistic Coefficient(G7)</th> 
		</tr>
	<?php 
		$result = dbq("SELECT * FROM ammunition");
		while($row = dba($result)) {
			$name = $row['name'];
			$bullet_weight = $row['bullet_weight_grains'];
			$bullet = $row['bullet'];
			$velocity = $row['velocity_fps'];
			$ballistic_coefficient = $row['ballistic_coefficient_g7'];

			echo "<tr>";
				echo "<td>";
					echo "<p>$name</p>";
				echo "</td>";
				echo "<td>";
					echo "<p>$bullet_weight</p>";
				echo "</td>";
				echo "<td>";
					echo "<p>$bullet</p>";
				echo "</td>";
				echo "<td>";
					echo "<p>$velocity</p>";
				echo "</td>";
				echo "<td>";
					echo "<p>.$ballistic_coefficient</p>";
				echo "</td>";
			echo "</tr>";
		}
	?>
	</table>
	