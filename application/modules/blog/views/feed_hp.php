<?php  
	$this->load->module('timedate');
	foreach ($query->result() as $row) {
		$picture = $row->picture;
		$thumbnail_name = str_replace('.','_thumb.',$picture);
		$thumbnail_path = base_url().'blog_pics/'.$thumbnail_name;
		$date_published = $this->timedate->get_nice_date($row->date_published,'mini');
		$blog_url = base_url().'blog/article/'.$row->page_url;
?>

	<div class="row" style="margin-bottom: 12px;">
		<div class="col-md-3" >
			<img src="<?= $thumbnail_path ?>" class="img-fluid img-thumbnail" > 
		</div> 
		<div class="col-md-9">
			<h4><a href="<?= $blog_url ?>" ><?= $row->page_title ?></a></h4>
				
			<p style="font-size: 0.8em;">
				<?= $row->author; ?> - 
				<span style="color: #999;"><?= $date_published; ?></span>
			</p>

			<p><?= $row->page_content ?></p>
		</div>
	</div>


<?php } ?>
