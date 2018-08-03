<h3 class="ui-bar ui-bar-a">The Blog</h3>
<div data-role="collapsibleset" data-theme="a" data-content-theme="a" data-mini="true">

<?php  
  $this->load->module('timedate');
  foreach ($query->result() as $row) {
    $picture = $row->picture;
    $thumbnail_name = str_replace('.','_thumb.',$picture);
    $thumbnail_path = base_url().'blog_pics/'.$thumbnail_name;
    $date_published = $this->timedate->get_nice_date($row->date_published,'mini');
    $blog_url = base_url().'blog/article/'.$row->page_url;
?>

<div data-role="collapsible">
    <h3><?= $row->page_title ?></h3>
    <?= $row->author; ?> - 
        <span style="color: #999;"><?= $date_published; ?></span>
      </p>

      <p><?= $row->page_content ?></p>  
      <p style="text-align: right">
        <a href="<?= $blog_url ?>">Read More</a>
      </p>
</div>

<?php } ?>
