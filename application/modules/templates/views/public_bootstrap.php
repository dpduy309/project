<!doctype html>
<html lang="en" <?php 
  if(isset($use_angularjs)){
    echo ' ng-app="myApp"';
  }
 ?> >
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?php echo base_url();?>/favicon.ico">

    <title>Share Food</title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo base_url();?>/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?php echo base_url();?>/assets/css/jumbotron.css" rel="stylesheet">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  

    <?php 
      if(isset($use_featherlight))
      { ?>
        <link href="<?=base_url('adminfiles/css/featherlight.min.css')?>" type="text/css" rel="stylesheet" />
     <?php
      }

      if(isset($use_angularjs)){
      echo '<script type="text/javascript" src="https://code.angularjs.org/1.4.9/angular.min.js"></script>';
    }
    ?>


  </head>

  <body style="background-image: url('../img/dark_brick_wall.png');">
  <div class="container-fluid dctop">
    <div class="container" style="height: 100px">
      <div class="row">
        <?= Modules::run('templates/_draw_page_top') ?>
      </div>
    </div>
  </div>
  
    <nav class="navbar navbar-expand-md navbar-dark" style="background-color: #212121">
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

    <div class="container roundbtm" style="background-color: #fff;">
      <div id="stage">
      <?php  
        echo Modules::run('sliders/_attempt_draw_slider');
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
      <?= Modules::run('btm_nav/_draw_btm_nav')?>
      <p>&copy; Company 2017-2018</p>
    </footer>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="<?php echo base_url()?>/assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="<?php echo base_url();?>/assets/js/vendor/popper.min.js"></script>
    <script src="<?php echo base_url();?>/dist/js/bootstrap.min.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <?php 
      if(isset($use_featherlight)) { ?>
      <script src="<?=base_url('js/featherlight.min.js');?>" type="text/javascript" charset="utf-8"></script>
     <?php } ?>
  </body>
</html>
