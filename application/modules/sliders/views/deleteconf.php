<h1><?= $headline ?></h1>

<?php 
	if(isset($flash)){
		echo $flash;
	}
?>

<div class="row-fluid sortable">
				<div class="box span12">
					<div class="box-header" data-original-title>
						<h2><i class="halflings-icon white edit"></i><span class="break"></span>Delete Success</h2>
						<div class="box-icon">
							<a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
							<a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
						</div>
					</div>
					<div class="box-content">

						<p>Are you sure that you want delete the entire slider? </p>
						<?php 
								$attributes = array('class' => 'form-horizontal');
								echo form_open('sliders/delete/'.$update_id,$attributes);

							?>
						  <fieldset>
							<div class="control-group" style="height: 200px;">
							
							  <button type="submit" class="btn btn-danger" name="submit" value="Yes - Delete Slider">Yes - Delete Slider</button>
							  <button type="submit" name="submit" value="Cancel" class="btn">Cancel</button>

							</div>
						  </fieldset>
						</form>   
					</div>
				</div><!--/span-->

</div>
