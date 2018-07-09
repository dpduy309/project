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

  <body>

    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
      <a class="navbar-brand" href="<?= base_url() ?>">Home</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
          <?php 
            echo Modules::run('store_categories/_draw_top_nav');
           ?>

      <!--  
        <form class="form-inline my-2 my-lg-0">
          <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form> -->
      </div>
    </nav>

    <div class="container" style="min-height: 650px">
      <?php  
        if($customer_id>0)
        {
          include('customer_panel_top.php');
        }

        if(isset($page_content))
        {
          echo nl2br($page_content);

            if(!isset($page_url)){
              $page_url = 'homepage';
            }

            if($page_url == "")
            {
              //homepage
              require_once('content_homepage.php');
            }elseif($page_url == "contact-us")
            {
              echo Modules::run('contactus/_draw_form');
            }


        }elseif(isset($view_file))
        {
          $this->load->view($view_module.'/'.$view_file);
        }
      ?>
    </div>
  <hr>
    <footer class="container">
      <p>&copy; Company 2017-2018</p>
    </footer>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="<?php echo base_url()?>/assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="<?php echo base_url();?>/assets/js/vendor/popper.min.js"></script>
    <script src="<?php echo base_url();?>/dist/js/bootstrap.min.js"></script>
  </body>
</html>
