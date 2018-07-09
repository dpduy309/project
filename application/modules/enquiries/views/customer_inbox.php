<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<h1>Your <?= $folder_type ?></h1>
<?php  
	if(isset($flash))
	{
		echo $flash;
	}
?>

	<?php $create_msg_url = base_url('yourmessage/create'); ?>
	<p style="margin-top: 30px;">
	<a href="<?= $create_msg_url ?>"><button type="button" class="btn btn-primary">Compose Message</button></a>
	</p>	


						<table class="table table-striped table-bordered bootstrap-datatable datatable">
						  <thead>
							  <tr style="background-color: #666;color: white;">
							  	  <th>&nbsp;</th>
								  <th>Date Sent</th>
								  <th>Sent By</th>
								  <th>Subject</th>
								  <th>Actions</th>
							  </tr>
						  </thead>   
						  <tbody>
						  	<?php 
						  		$this->load->module('site_settings');
						  		$this->load->module('timedate');
						  		$this->load->module('store_accounts');

						  		$teamname = $this->site_settings->_get_support_team_name();

						  		foreach($query->result() as $row){ 
						  			$view_url = base_url('yourmessage/view/'.$row->code);

						  			$customer_data['firstname'] = $row->firstname;
						  			$customer_data['lastname'] = $row->lastname;
						  			$customer_data['company'] = $row->company;
						  			$opened = $row->opened;

						  			if($opened == 1)
						  			{
						  				$icon = '<span class="glyphicon glyphicon-envelope"></span>';
						  			}else
						  			{
						  				$icon = '<span style="color: orange;" class="glyphicon glyphicon-envelope"></span>';
						  			}

						  			$date_sent = $this->timedate->get_nice_date($row->date_created, 'mini');

						  			if($row->sent_by == 0)
						  			{
						  				$sent_by = $teamname;
						  			}else
						  			{
						  				$sent_by = $this->store_accounts->_get_customer_name($row->sent_by,$customer_data);
						  			}
						  	?>
								<tr>
							  	  <td class="span1" style="text-align: center"><?= $icon ?></td>
								  <td><?= $date_sent ?></td>
								  <td><?= $sent_by ?></td>
								  <td class="col-md-6"><?= $row->subject?></td>
								  <td class="col-md-1">
								  	<a class="btn btn-info" href="<?= $view_url ?>">
								  		<span class="glyphicon glyphicon-eye-open"></span> View
								  	</a>
								  </td>
							  </tr>	
						
							<?php 
								} 
							?>
							
						  </tbody>
					  </table>


