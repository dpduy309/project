<div style="border-radius: 7px;">
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
					echo form_dropdown('submitted_colour', $colour_options, $submitted_colour,$addition_dd_code);

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
					echo form_dropdown('submitted_size', $size_options, $submitted_size,$addition_dd_code);

				?>


			</td>
		</tr>
		<?php } ?>

		<tr>
			<td>Qty: </td>
			<td>
				<div class="col-md-4" style="padding-left: 0px;">
					<input type="text" class="form-control">
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: center;">
				<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>Add To Basket</button>
			</td>
		</tr>
	</table>
	
</div>