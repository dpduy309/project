<?php  
  $count = 0;
  $this->load->module('homepage_offers');
  foreach ($query->result() as $row) {
    $count++;
    $block_id = $row->id;
    $num_items_on_block = $this->homepage_offers->count_where('block_id',$block_id);

    if($num_items_on_block>0){
    if($count>4)
    {
      $count = 1;
    }

?>

<h3 class="ui-bar ui-bar-a"><?= $row->block_title?></h3>

     <?php  
      $this->homepage_offers->_draw_offers($block_id,$theme=2,TRUE);
  }
}
     ?>




