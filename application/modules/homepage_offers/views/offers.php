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

    $was_price = number_format($row->was_price,2);
    $was_price = str_replace('.00','', $was_price);
		?>

		<div class="col-xs-3">
        <div class="offer offer-<?= $theme ?>">
          <div class="shape">
            <div class="shape-text">
              top               
            </div>
          </div>
          <div class="offer-content">
            <h3 class="lead"><b>
              Now At
              $<?= $item_price ?></b>
            </h3>      
            <a href="<?= $item_page ?>"><img src="<?= $small_pic_path ?>" title="<?= $item_title ?>" class="img-fluid"></a>
		<br>     
            <p>
              <h4><a href="<?= $item_page ?>"><?= $item_title  ?></a><h4>
              Was Price: <span style="text-decoration: line-through">$<?= $was_price ?></span>
            </p>
          </div>
        </div>
      </div>


		<?php
	}

?>