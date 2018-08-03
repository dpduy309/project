<p style="margin-top: 30px;">
<a href="<?= base_url('sliders/create/'.$parent_id)?>"><button type="button" class="btn btn-default">Previous Page</button></a>

	<!-- Button trigger modal -->
	<button type="button" class="btn btn-info" data-toggle="modal" data-target="#exampleModal">
	  Create New Slide
	</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Create Slide</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" action="<?= $form_location ?>" method="post">
			<p>
				<div class="control-group">
          <label class="control-label" for="typeahead">Target URL</label>
          <div class="controls">
          <input type="text" name="target_url" class="span6" placeholder="Enter the target URL here">  
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="typeahead">Alt-Text </label>
          <div class="controls">
          <input type="text" name="alt_text" class="span6" placeholder="Enter the Alt-Text here">  
          </div>
        </div>
			</p> 
      </div>
      <div class="modal-footer">
        <button  class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">Close</button>
        <button  class="btn btn-primary" name="submit" value="Submit" type="submit">Submit</button>
      </div>

      <?php  
      	echo form_hidden('parent_id',$parent_id);
      ?>
	</form>	

    </div>
  </div>
</div>

</p>