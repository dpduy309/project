<?php echo Modules::run('homepage_blocks/_draw_blocks') ?>

<div class="row">
  <div class="col-md-8">
    <h2>The Blog</h2>
      <?= Modules::run('blog/_draw_feed_hp')  ?>
  </div>
 
  <div class="col-md-4">
    <h2>You Are What You Eat</h2>
    <iframe width="300" height="200" src="https://www.youtube.com/embed/PZ4pctQMdg4" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
  </div>
</div>

