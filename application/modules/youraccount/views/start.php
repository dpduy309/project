<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->
<?php $form_location = base_url('youraccount/submit'); 
?>
<h1>Create Account</h1>
<?php echo validation_errors("<p style='color: red;'>","</p>"); ?>
<form class="form-horizontal" action="<?= $form_location ?>" method="post">
<fieldset>

<!-- Form Name -->
<legend>Please submit your details using the form below</legend>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Username</label>  
  <div class="col-md-4">
  <input id="username" name="username" value="<?= $username ?>" type="text" placeholder="Please enter your username of choice here" class="form-control input-md" required="">
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">E-mail</label>  
  <div class="col-md-4">
  <input id="email" name="email" value="<?= $email ?>" type="text" placeholder="Please enter your contact email address" class="form-control input-md">
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Password</label>  
  <div class="col-md-4">
  <input id="pword" name="pword" value="<?= $pword ?>" type="password" placeholder="Please enter your password" class="form-control input-md">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Repeat Password</label>  
  <div class="col-md-4">
  <input id="repeat_pword" name="repeat_pword" value="<?= $repeat_pword ?>" type="password" placeholder="Please repeat your password here" class="form-control input-md">
  </div>
</div>



<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="singlebutton">Create account?</label>
  <div class="col-md-4">
    <button id="singlebutton" name="submit" value="Submit" class="btn btn-primary">Yes</button>
  </div>
</div>

</fieldset>
</form>
