<h1><?= $block_title ?></h1>
<p><?= $showing_statement ?></p>
<?= $site_pagination ?>

<div class="row">
<?php  
	foreach ($query->result() as $row) {
		$item_title = $row->item_title;
		$item_price = $row->item_price;
		$was_price = $row->was_price;
		$small_pic = $row->small_pic;
		$small_pic_path = base_url()."small_pics/".$small_pic;
		$item_page = base_url().$item_segments.$row->item_url;
?>
	<div class="col-md-4">
		
	
	<div class="img-thumbnail" style="text-align: center; margin: 6px;">
		<a href="<?= $item_page ?>"><img src="<?= $small_pic_path ?>" title="<?= $item_title ?>" class="img-fluid"></a>
		<br>
		<h6 style="margin-top: 12px;"><?= $item_title ?></h6>
		<div style="clear: both; color: red; font-weight: bold;">
			<?= number_format($item_price) ?>
			<?php if($was_price > 0) { ?>
			<span style="color: #999; text-decoration: line-through;"><?= $was_price ?></span>
			<?php } ?>
		</div>
	</div>
	</div>
<?php
	}
?>

</div>
<?= $site_pagination ?>
