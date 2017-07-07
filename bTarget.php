<!-- Browse target class. Loads all targets currently in DB. Uses fancybox to allow zooming into images. -->
<div class='container'>
	<table class="table table-bordered table-hover table-responsive">
		<tr>
			<th>Target Name</th>
			<th>Width(CM)</th> 
			<th>Height(CM)</th>
			<th>Image</th> 
		</tr>
	<?php 
		$result = dbq("SELECT * FROM target");
		while($row = dba($result)) {		//For each row, populate the table.
			$name = $row['name'];
			$width = $row['width'];
			$height = $row['height'];
			$image = $row['image'];

			echo "<tr>";
				echo "<td>";
					echo "<p>$name</p>";
				echo "</td>";
				echo "<td>";
					echo "<p>$width</p>";
				echo "</td>";
				echo "<td>";
					echo "<p>$height</p>";
				echo "</td>";
				echo "<td>";
					echo "<a class='fancybox' rel='group' href='assets/targets/$image.jpg'><img src='assets/targets/$image.jpg' height='25%' width='auto' alt='$image.jpg'/></a>";
				echo "</td>";
			echo "</tr>";
		}
	?>
	</table>
	
	