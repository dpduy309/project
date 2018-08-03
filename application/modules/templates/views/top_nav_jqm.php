<div data-role="navbar">
	<ul>
		<?php 
			foreach ($top_nav_btns as $value) {
				if($value['btn_target_url']==$current_url){
					$top_btn_css = ' class="ui-btn-active"';
				}else{
					$top_btn_css = ' class="ui-btn-active"';
				}

				if($value['text'] == 'Login')
				{
					$top_btn_css .= ' rel="external"';
				}
		?>


		<li>
			<a href="<?= $value['btn_target_url'] ?>" data-icon="<?= $value['icon'] ?>" <?= $top_btn_css ?>>
				<?= $value['text'] ?>
			</a>
		</li>
		<?php } ?>

	</ul>
</div><!-- /navbar -->