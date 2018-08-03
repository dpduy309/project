<?= form_open($form_location) ?>

<div class="ui-body ui-body-a ui-corner-all">
	<h3>Contact Us</h3>
	<?php  
		echo validation_errors("<p style='color: red;'>","</p>");
	?>

	<label for="text-basic">Name:</label>
    <input type="text" name="yourname" id="text-basic" value="<?= $yourname ?>">

    <label for="text-basic">Email:</label>
    <input type="text" name="email" id="text-basic" value="<?= $email ?>">

    <label for="text-basic">Telephone Number:</label>
    <input type="text" name="telnum" id="text-basic" value="<?= $telnum ?>">

    <label for="textarea">Message:</label>
    <textarea cols="40" rows="8" name="message" id="textarea"><?= $message ?></textarea>
	

    <button type="submit" name="submit" value="Submit" class="ui-shadow ui-btn ui-corner-all">Submit</button>
</div>

<?= form_close(); ?>