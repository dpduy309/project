<?php  
  $first_bit = $this->uri->segment(1);
  $form_location = base_url($first_bit.'/submit_login');
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?php echo base_url();?>/favicon.ico">

    <title>Jumbotron Template for Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo base_url();?>/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?php echo base_url();?>/assets/css/jumbotron.css" rel="stylesheet">
  </head>

  <body class="text-center">
    <div class="row">
      <div class="col-md-4 offset-md-4">
    <form class="form-signin" action="<?= $form_location ?>" method="post">

      <img class="mb-4" src="https://getbootstrap.com/assets/brand/bootstrap-solid.svg" alt="" width="72" height="72">
      <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
      <label for="inputText" class="sr-only">Username or Email Address</label>
      <input type="text" name="username" id="inputText" value="<?= $username?>" class="form-control" placeholder="Username or Email Address" required autofocus>
      <label for="inputPassword" class="sr-only">Password</label>
      <input type="password" name="pword" id="inputPassword" class="form-control" placeholder="Password" required>
      <div class="checkbox mb-3">
        <?php  
          if($first_bit == "youraccount")
          {
        ?>
        <label>
          <input type="checkbox" name="remember" value="remember-me"> Remember me
        </label>
      <?php } ?>
      </div>
      <button class="btn btn-lg btn-primary btn-block" name="submit" value="Submit" type="submit">Sign in</button>
      <p class="mt-5 mb-3 text-muted">&copy; 2017-2018</p>
    </form>
  </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="<?php echo base_url()?>/assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="<?php echo base_url();?>/assets/js/vendor/popper.min.js"></script>
    <script src="<?php echo base_url();?>/dist/js/bootstrap.min.js"></script>
  </body>
</html>

