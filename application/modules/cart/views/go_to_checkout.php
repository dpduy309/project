<h1>Please create an account.</h1>
<p>You do not need to create an account with us. However, if you do then you'll be able to enjoy:</p>
<p>
	<ul>
		<li>Order Tracking</li>
		<li>Downloadable Order Form</li>
		<li>Priority Technical Support</li>
	</ul>
</p>
<p>Creating an account only take a minute or so.</p>
<p>Would you like to create an account?</p>

<div class="col-md-10" style="margin-top: 36px">
<?php echo form_open('cart/submit_choice'); ?>
	<button class="btn btn-success" name="submit" value="Yes - Let's Do It" type="submit">
		<span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>
		Yes - Let's Do It
	</button>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<button class="btn btn-danger" name="submit" value="No Thanks" type="submit">
		<span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span>
		No Thanks
	</button>
<?php 
echo form_hidden('checkout_token',$checkout_token);
echo form_close(); ?>
</div>