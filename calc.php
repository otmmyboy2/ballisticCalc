<div class="container">	
	<div class="form-group">
		<label for="complexity">Select calculation type:</label>
		<select class="form-control" id="complexity" name="complexity">
			<option value="simple">Simple</option>
			<option value="complex">Complex</option>
		</select>
	</div>
	<form method="POST" action="?p=calcBackend"   autocomplete="on" id="calcForm">
		<div class="form-group">
			<label for="ammoInput">Ammunition</label>
			<select class="form-control" name="ammoInput" id="ammoInput">
				<?php 
					$result = dbq("SELECT * FROM ammunition");
					while($row = dba($result)) {
						$id = $row['id'];
						$name = $row['name'];
						echo "<option value='$id'>";
							echo "<p>$name</p>";
						echo "</option>";
					}
				?>
			</select>
		</div>
		<div class="form-group">
			<label for="targetInput">Target</label>
			<select class="form-control" name="targetInput" id="targetInput">
				<?php 
					$result = dbq("SELECT * FROM target");
					while($row = dba($result)) {
						$id = $row['id'];
						$name = $row['name'];
						echo "<option value='$id'>";
							echo "<p>$name</p>";
						echo "</option>";
					}
				?>
			</select>
		</div>
		<div class="form-group">
			<label for="barrelInput">Barrel length (Inches)</label>
			<input min="1" max="28" type="number" step="0.1" class="form-control" id="barrelInput" name="barrelInput" placeholder="">
		</div>
		<div class="form-group" id="r1">
			<label for="range1Input">Range (Yards)</label>
			<input type="number" step="1" min="100" max="5000" class="form-control" id="range1Input" name="range1Input" placeholder="">
		</div>
		<div class="form-group" id="r2">
			<label for="range2Input">Range the rifle is zeroed at (Yards)</label>
			<input type="number" step="1" min="100" max="5000" class="form-control" id="range2Input" name="range2Input" placeholder="">
		</div>
		<div class="form-group" id="r3">
			<label for="range3Input">Range other than the rifle is zeroed at (Yards)</label>
			<input type="number" step="1" min="100" max="5000" class="form-control" id="range3Input" name="range3Input" placeholder="">
		</div>
		<div class="form-group">
			<label for="heightInput">Height of sight above bore (Inches)</label>
			<input min="0.01" max="12" type="number" step="0.01" class="form-control" id="heightInput" name="heightInput" placeholder="">
		</div>
		<div class="form-group">
			<label for="crosswindDirInput">Crosswind direction</label>
			<select class="form-control" name="crosswindDirInput" id="crosswindDirInput">
				<option value="3">3 o'clock</option>
				<option value="9">9 o'clock</option>
			</select>
		</div>
		<div class="form-group">
			<label for="crosswindSpeedInput">Crosswind speed (MPH)</label>
			<input type="number" min="0" max="50" class="form-control" id="crosswindSpeedInput" name="crosswindSpeedInput" placeholder="">
		</div>
		<button type="submit" style="margin-left: 44%;" class="btn btn-primary">Submit</button>		
		<a class="btn btn-reset" onclick="refresh()" role="button"> Reset</a>
	</form>

	<script>
		$(function() {
			$("#complexity").change(function() {
				if (document.getElementById('complexity').value == "simple") {
					document.getElementById('range1Input').disabled = false;
					document.getElementById('range2Input').disabled = true;
					document.getElementById('range3Input').disabled = true;
				}else{
					document.getElementById('range1Input').disabled = true;
					document.getElementById('range2Input').disabled = false;
					document.getElementById('range3Input').disabled = false;
				}
			}).trigger('change');
		});
		$(document).ready(function() {
			$('#calcForm').formValidation({
				framework: 'bootstrap',
				icon: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
				},
				fields: {
					ammoInput: {
						validators: {
							notEmpty: {
								message: 'Ammunition is required.'
							}
						}
					},
					targetInput: {
						validators: {
							notEmpty: {
								message: 'Target is required.'
							}
						}
					},
					crosswindDirInput: {
						validators: {
							notEmpty: {
								message: 'Crosswind direction is required.'
							}
						}
					},
					barrelInput: {
						validators: {
							notEmpty: {
								message: 'Barrel length is required.'
							},
							numeric: {
								message: 'Barrel length must be a number.'
							},
							between: {
								min: 1,
								max: 28,
								message: 'Barrel length must be between 1 and 28 inches.'
							}
						}
					},
					range1Input: {
						validators: {
							notEmpty: {
								message: 'Range is required.'
							},
							numeric: {
								message: 'Range must be a number.'
							},
							between: {
								min: 100,
								max: 5000,
								message: 'Range must be between 100 and 5000 yards.'
							}
						}
					},
					range2Input: {
						validators: {
							notEmpty: {
								message: 'Range the rifle is zeroed at is required.'
							},
							numeric: {
								message: 'Range the rifle is zeroed at must be a number.'
							},
							between: {
								min: 100,
								max: 5000,
								message: 'Range the rifle is zeroed at must be between 100 and 5000 yards.'
							},
							different: {
								field: 'range1Input',
								message: 'The range cannot be the same as the Range the rifle is zeroed at.'
							}
						}
					},
					range3Input: {
							notEmpty: {
								message: 'Range other than the rifle is zeroed at is required.'
							},
						validators: {
							numeric: {
								message: 'Range other than the rifle is zeroed at must be a number.'
							},
							between: {
								min: 100,
								max: 5000,
								message: 'Range other than the rifle is zeroed at must be between 100 and 5000 yards.'
							},
							different: {
								field: 'range2Input',
								message: 'The range other than the rifle is zeroed at cannot be the same as the Range the rifle is zeroed at.'
							}
						}
					},
					crosswindSpeedInput: {
						validators: {
							notEmpty: {
								message: 'The Crosswind Speed is required.'
							},
							numeric: {
								message: 'Crosswind speed must be a number.'
							},
							between: {
								min: 0,
								max: 50,
								message: 'Crosswind speed must be between 0.0 and 50.0 MPH.'
							}
						}
					},
					heightInput: {
						validators: {
							notEmpty: {
								message: 'The height above bore axis is required.'
							},
							numeric: {
								message: 'The height above bore axis must be a number.'
							},
							between: {
								min: 0,
								max: 12,
								message: 'The height above bore axis must be between 0.0 and 12.0 inches.'
							}
						}
					}
				}
			});
		});
	</script>
	