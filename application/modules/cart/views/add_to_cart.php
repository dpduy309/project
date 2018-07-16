<div style="border-radius: 7px;">
	<?php  
		echo form_open('store_basket/add_to_basket');
	?>
	<table class="table">
		<tr>
			<td>Item ID:</td>
			<td><?= $item_id ?></td>
		</tr>
		

		<?php  
			if($num_colours > 0){
		?>
		<tr>
			<td>Colour: </td>
			<td>
				<?php  
						$addition_dd_code = 'class="form-control"';
						$options = array(

						);

					// $shirts_on_sale = array('small', 'large');
					echo form_dropdown('item_colour', $colour_options, $submitted_colour,$addition_dd_code);

				?>


			</td>
		</tr>
		<?php } ?>


		<?php  
			if($num_sizes > 0){
		?>
		<tr>
			<td>Size: </td>
			<td>
				<?php  
						$addition_dd_code = 'class="form-control"';
						$options = array(

						);

					// $shirts_on_sale = array('small', 'large');
					echo form_dropdown('item_size', $size_options, $submitted_size,$addition_dd_code);

				?>


			</td>
		</tr>
		<?php } ?>

		<tr>
			<td>Qty: </td>
			<td>
				<div class="col-md-4" style="padding-left: 0px;">
					<input name="item_qty" type="text" class="form-control">
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: center;">
				<button type="submit" class="btn btn-primary" name="submit" value="Submit" type="submit"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>Add To Basket</button>
			</td>
		</tr>
	</table>
	<?php 
		echo form_hidden('item_id',$item_id);
		echo form_close(); 
	?>
</div>