<?php  
  function _attempt_make_active($link_text)
  {
     if(current_url()==base_url('youraccount/welcome') AND ($link_text=="Your Messages"))
     {
        echo 'class="active"';
     }
  }

?>

<ul class="nav nav-tabs" style="margin-top: 24px;">
  <li class="nav-item">
    <a class="nav-link" <?= _attempt_make_active('Your Messages')?> href="<?= base_url('youraccount/welcome');?>">Your Messages</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="<?= base_url('yourorders/browse');?>">Your Orders</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">Update Your Profile</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="<?= base_url('youraccount/logout');?>">Log Out</a>
  </li>
</ul>