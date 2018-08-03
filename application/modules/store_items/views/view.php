<?php  
	echo Modules::run('templates/_draw_breadcrumbs',$breadcrumbs_data);
	if(isset($flash)){
		echo $flash;
	}
?>




<div class="row" style="margin-top: 24px;">
	<div class="col-md-4">
		<a href="#" data-featherlight="<?= base_url();?>big_pics/<?= $big_pic ?>">
			<img src="<?= base_url();?>big_pics/<?= $big_pic ?>" class="img-fluid" alt="<?= $item_title ?>">
		</a>
	</div>
	<div class="col-md-4">
		<h1><?php echo $item_title; ?></h1>
		<h4><?= 'Our Price: '.$item_price ?></h4>
		<div style="clear: both;">
		<?= nl2br($item_description) ?>
		</div>
	</div>
	<div class="col-md-4">
		<?= Modules::run('cart/_draw_add_to_cart',$update_id); ?>
	</div>

</div>