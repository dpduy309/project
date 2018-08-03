<div id="btm_nav">
	<?php  
		foreach ($query->result() as $row) {
			$page_url = base_url().$row->page_url;
			$page_title = $row->page_title;
			echo anchor($page_url,$page_title);
		}
	?>
</div>