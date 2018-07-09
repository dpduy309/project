 <ul class="navbar-nav mr-auto">
    <?php  
    $this->load->module('store_categories');
      foreach ($parent_categories as $key => $value) {
          $parent_cat_id = $key;
          $parent_cat_title = $value;
    ?>
  <li class="nav-item dropdown">

    <a class="nav-link dropdown-toggle" href="http://example.com" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= $parent_cat_title ?></a>
    <div class="dropdown-menu" aria-labelledby="dropdown01">
      <?php  
        $query = $this->store_categories->get_where_custom('parent_cat_id',$parent_cat_id);
        foreach ($query->result() as $row) {
          $cat_url = $row->cat_url;
          echo '<a class="dropdown-item" href="'.$target_url_start.$cat_url.'">'.$row->cat_title.'</a>';
        }
      ?>
    </div>
  </li>
<?php  
  }

?>


</ul>
