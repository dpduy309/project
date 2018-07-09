<div class="row" style="margin-top: 24px;">
	<div class="col-md-12">
		<nav aria-label="breadcrumb">
		  <ol class="breadcrumb">
		  	<?php  
		  		foreach ($breadcrumbs_array as $key => $value) {
		    		echo '<li class="breadcrumb-item"><a href="'.$key.'">'.$value.'</a></li>';
		  		}
		  	?>
		    <li class="breadcrumb-item active" aria-current="page"><?= $current_page_title ?></li>
		  </ol>
		</nav>
	</div>
</div>