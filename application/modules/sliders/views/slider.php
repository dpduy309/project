<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
    <?php  
      $count = 0;
      foreach ($query_slides->result() as $row_slides){
        if($count==0)
        {
          $add_css = 'class="active"';
        }else{
          $add_css = '';
        }
  
    ?>
    <li data-target="#carousel-example-generic" data-slide-to="<?= $count ?>" <?= $add_css ?>></li>
    <?php 
      $count++;
      } 
    ?>
   
  </ol>

  <!-- Wrapper for slides -->
  <div class="carousel-inner" role="listbox">
    <?php  
       $count = 0;
      foreach ($query_slides->result() as $row_slides){
        $target_url = $row_slides->target_url;
        $alt_text = $row_slides->alt_text;
        $pic_path = base_url('slider_pics/'.$row_slides->picture);

        if($count==0)
        {
          $add_css = 'active';
        }else{
          $add_css = '';
        }
    ?>

    <div class="item <?= $add_css ?>">
      <?php  
        if($target_url!=''){
      ?>
       <a href="<?= $target_url ?>"> 
        <img src="<?= $pic_path ?>" alt="<?= $alt_text ?>">
      </a>
    <?php }else{ ?>
        <img src="<?= $pic_path ?>" alt="<?= $alt_text ?>">
    <?php } ?>
    </div>

    <?php 
      $count++;
      } 
    ?>


  <!-- Controls -->
  <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
    <span class="icon-prev" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
    <span class="icon-next" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>
