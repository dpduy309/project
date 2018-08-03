<p style="margin-top: 30px;">

	<!-- Button trigger modal -->
	<button type="button" class="btn btn-info" data-toggle="modal" data-target="#exampleModal">
	  Create New Link
	</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Create Bottom Navigation Links</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" action="<?= $form_location ?>" method="post">
			
    <div class="control-group">
        <label class="control-label" for="typeahead">Page URL:</label>
				<div class="controls">
            <?php  
                $addition_dd_code = 'id="selectError3"';
              
              echo form_dropdown('page_id', $options, '',$addition_dd_code);

            ?>
          </div>
		</div>
      </div>
      <div class="modal-footer">
        <button  class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">Close</button>
        <button  class="btn btn-primary" name="submit" value="Submit" type="submit">Submit</button>
      </div>

	</form>	

    </div>
  </div>
</div>

</p>