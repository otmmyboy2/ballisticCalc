<!-- Contact page, allows user to enter comments and email address, both required. -->
<div class="container">
	<form method="POST" action="contactBackend.php" id="contactForm">
		<div class="form-group">
			<label for="emailInput">Email address</label>
			<input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
		</div>
		<div class="form-group">
			<label for="commentsArea">Comments</label>
			<textarea class="form-control" rows="3" id="comments" name="comments" placeholder="Comments?....."></textarea>
		</div>
		<button type="submit" style="margin-left: 44%;" class="btn btn-primary">Submit</button>		
		<a class="btn btn-reset" onclick="refresh()" role="button"> Reset</a>
	</form>
	
	<script>
		$(document).ready(function() {
			$('#contactForm').formValidation({		//Validation for email and comments fields.
				framework: 'bootstrap',
				icon: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
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
					comments: {
						validators: {
							notEmpty: {
								message: 'Comments are required.'
							}
						}
					}
				}
			});
		});
	</script>
		