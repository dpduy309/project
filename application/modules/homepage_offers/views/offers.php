<?php  
	foreach ($query->result() as $row) {
		$item_title = $row->item_title;
		$item_price = $row->item_price;
		$was_price = $row->was_price;
		$small_pic = $row->small_pic;
		$small_pic_path = base_url()."small_pics/".$small_pic;
		$item_page = base_url()."fashional/choice/".$row->item_url; //_get_item_segments
		$item_price = number_format($row->item_price,2);
		$item_price = str_replace('.00','', $item_price);
		?>

		<div class="col-xs-3">
        <div class="offer offer-<?= $theme ?>">
          <div class="shape">
            <div class="shape-text">
              top               
            </div>
          </div>
          <div class="offer-content">
            <h3 class="lead">
              <?= $item_price ?>
            </h3>      
            <a href="<?= $item_page ?>"><img src="<?= $small_pic_path ?>" title="<?= $item_title ?>" class="img-fluid"></a>
		<br>     
            <p>
              <a href="<?= $item_page ?>"><?= $item_title  ?></a>
              <br> and so one
            </p>
          </div>
        </div>
      </div>


		<?php
	}

?>