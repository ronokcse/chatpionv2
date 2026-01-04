<section class="section section_custom">
	<div class="section-header">
		<h1><i class="fa fa-dashboard"></i> <?php echo lang('Recent Activity'); ?></h1>
	</div>
	
	<div class="section-body">
		<div class="card">
			<div class="card-body" style="padding: 0 !important;">   
				<div class="row">

					<div class="col-12">
			  			<div class="card">
			  				<div class="card-header">
			  					<h4><i class="fa fa-share-square"></i> <?php echo lang('Facebook Poster Campaign') ?></h4>
			  				</div>
			  				<div class="card-body data-card">
							<?php 

				  				echo "<div class='table-responsive2'><table class='table table-bordered'>";

				  				echo "<thead><tr>";
				  					echo "<th>";
			  						echo lang('User Name');
			  						echo "</th>";

			  						echo "<th>";
			  						echo lang('User Email');
			  						echo "</th>";

			  						echo "<th>";
			  						echo lang('campaign name');
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo lang('post type');
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo lang('post URL');
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo lang('Last update time');
			  						echo "</th>";
			  					echo "</tr></thead>";

				  				$sl=0;
				  				foreach ($facebook_poster as $key => $value) 
				  				{
				  					$sl++;
				  					echo "<tbody><tr>";
				  						echo "<td><a href='".base_url('admin/user_dashboard/').$value['user_id']."' target='_blank'>".$value["user_name"]."</a></td>";
				  						echo "<td><a href='".base_url('admin/user_dashboard/').$value['user_id']."' target='_blank'>".$value["user_email"]."</a></td>";
				  						echo "<td>".$value["campaign_name"]."</td>";
				  						echo "<td>".$value["post_type"]."</td>";
				  						echo "<td class='text-center'><a href='".$value["post_url"]."' target='_blank'><i class='far fa-hand-point-right'></i> ".lang('Go to Post.')."</a></td>";
				  						echo "<td class='text-center'>".date("d M y H:i",strtotime($value["last_updated_at"]))."</td>";
				  					echo "</tr></tbody>";
				  				}
				  				if($sl==0) echo "<tbody><tr><td class='text-center' colspan='6'>No data found.</td></tr></tbody>";
				  				echo "</table></div>";
				  			?>
			  				</div>
			  				<div class="card-footer">
			  				</div>
			  			</div>	
			  		</div>
			  		
			  		<div class="col-12">
			  			<div class="card">
			  				<div class="card-header">
			  					<h4><i class="fa fa-send"></i> <?php echo lang('Bulk Message Campaign') ?></h4>
			  				</div>
			  				<div class="card-body data-card">
			  					<?php 

					  				echo "<div class='table-responsive2'><table class='table table-bordered'>";

					  				echo "<thead><tr>";
					  					echo "<th>";
				  						echo lang('User Name');
				  						echo "</th>";

					  					echo "<th>";
				  						echo lang('User Email');
				  						echo "</th>";

				  						echo "<th>";
				  						echo lang('campaign name');
				  						echo "</th>";

				  						echo "<th class='text-center'>";
				  						echo lang('created at');
				  						echo "</th>";

				  						echo "<th class='text-center'>";
				  						echo lang('scheduled at');
				  						echo "</th>";

				  						echo "<th class='text-center'>";
				  						echo lang('message to sent');
				  						echo "</th>";

				  						echo "<th class='text-center'>";
				  						echo lang('successfully sent');
				  						echo "</th>";

				  						echo "<th class='text-center'>";
				  						echo lang('campaign status');
				  						echo "</th>";
				  					echo "</tr></thead>";

					  				$sl=0;
					  				foreach ($bulk_message_campaign as $key => $value) 
					  				{
					  					$sl++;
					  					echo "<tbody><tr>";
					  						echo "<td><a href='".base_url('admin/user_dashboard/').$value['user_id']."' target='_blank'>".$value["user_name"]."</a></td>";
					  						echo "<td><a href='".base_url('admin/user_dashboard/').$value['user_id']."' target='_blank'>".$value["user_email"]."</a></td>";
					  						echo "<td>".$value["campaign_name"]."</td>";
					  						echo "<td class='text-center'>".date("d M y H:i",strtotime($value["added_at"]))."</td>";
					  						echo "<td class='text-center'>".date("d M y H:i",strtotime($value["schedule_time"]))."</td>";
					  						echo "<td class='text-center'>".$value["total_thread"]."</td>";
					  						echo "<td class='text-center'>".$value["successfully_sent"]."</td>";
					  						if($value["posting_status"]=='0')
						  						echo "<td class='text-center'>".lang('pending')."</td>";
					  						else if($value["posting_status"]=='1')
						  						echo "<td class='text-center'>".lang('processing')."</td>";
					  						else if($value["posting_status"]=='2')
						  						echo "<td class='text-center'>".lang('completed')."</td>";
					  						else if($value["posting_status"]=='3')
						  						echo "<td class='text-center'>".lang('paused')."</td>";
					  						else
						  						echo "<td class='text-center'>".lang('hold')."</td>";
					  					echo "</tr></tbody>";
					  				}
					  				if($sl==0) echo "<tbody><tr><td class='text-center' colspan='6'>No data found.</td></tr></tbody>";
					  				echo "</table></div>";
					  			?>
			  				</div>
			  				<div class="card-footer">
			  				</div>
			  			</div>	
			  		</div>

			  		<div class="col-12">
			  			<div class="card">
			  				<div class="card-header">
			  					<h4><i class="fa fa-reply-all"></i> <?php echo lang('Auto reply Campaign') ?></h4>
			  				</div>
			  				<div class="card-body data-card">
		  					<?php 

				  				echo "<div class='table-responsive2'><table class='table table-bordered'>";

				  				echo "<thead><tr>";
				  					echo "<th>";
			  						echo lang('User Name');
			  						echo "</th>";

				  					echo "<th>";
			  						echo lang('User Email');
			  						echo "</th>";

			  						echo "<th>";
			  						echo lang('campaign name');
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo lang('Post ID');
			  						echo "</th>";

			  						// echo "<th class='text-center'>";
			  						// echo lang('Total comment reply');
			  						// echo "</th>";

			  						// echo "<th class='text-center'>";
			  						// echo lang('Total private reply sent');
			  						// echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo lang('Last reply time');
			  						echo "</th>";
			  					echo "</tr></thead>";

				  				$sl=0;
				  				foreach ($autoreply_campaign as $key => $value) 
				  				{
				  					$sl++;
				  					echo "<tbody><tr>";
				  						echo "<td><a href='".base_url('dashboard/index/').$value['user_id']."' target='_blank'>".$value["user_name"]."</a></td>";
				  						echo "<td><a href='".base_url('dashboard/index/').$value['user_id']."' target='_blank'>".$value["user_email"]."</a></td>";
				  						echo "<td>".$value["campaign_name"]."</td>";
				  						echo "<td><a target='_blank' href='https://facebook.com/".$value['post_id']."'>".$value["post_id"]."</a></td>";
				  						// echo "<td class='text-center'>".$value["auto_comment_reply_count"]."</td>";
				  						// echo "<td class='text-center'>".$value["auto_private_reply_count"]."</td>";
				  						echo "<td class='text-center'>".date("d M y H:i",strtotime($value["last_reply_time"]))."</td>";
				  					echo "</tr></tbody>";
				  				}
				  				if($sl==0) echo "<tbody><tr><td class='text-center' colspan='7'>No data found.</td></tr></tbody>";
				  				echo "</table></div>";
				  			?>
			  				</div>
			  				<div class="card-footer">
			  				</div>
			  			</div>	
			  		</div>

			  		<?php if($this->basic->is_exist("add_ons",array("project_id"=>21))) : ?>
			  		<div class="col-12">
			  			<div class="card">
			  				<div class="card-header">
			  					<h4><i class="fa fa-tv"></i> <?php echo lang('Facebook Live Campaign') ?></h4>
			  				</div>
			  				<div class="card-body data-card">
								<?php 

					  				echo "<div class='table-responsive2'><table class='table table-bordered'>";

					  				echo "<thead><tr>";
					  					echo "<th>";
				  						echo lang('User Name');
				  						echo "</th>";

					  					echo "<th>";
				  						echo lang('User Email');
				  						echo "</th>";

				  						echo "<th>";
				  						echo lang('campaign name');
				  						echo "</th>";

				  						echo "<th class='text-center'>";
				  						echo lang('Post URL');
				  						echo "</th>";

				  						echo "<th class='text-center'>";
				  						echo lang('Posting Status');
				  						echo "</th>";

				  						echo "<th class='text-center'>";
				  						echo lang('Last update time');
				  						echo "</th>";

				  					echo "</tr></thead>";

					  				$sl=0;
					  				foreach ($vidcaster_campaign as $key => $value) 
					  				{
					  					$sl++;
					  					echo "<tbody><tr>";
					  						echo "<td><a href='".base_url('admin/user_dashboard/').$value['user_id']."' target='_blank'>".$value["user_name"]."</a></td>";
					  						echo "<td><a href='".base_url('admin/user_dashboard/').$value['user_id']."' target='_blank'>".$value["user_email"]."</a></td>";
					  						echo "<td>".$value["campaign_name"]."</td>";
					  						echo "<td class='text-center'>".$value["post_url"]."</td>";
					  						echo "<td class='text-center'>".$value["posting_status"]."</td>";
					  						echo "<td class='text-center'>".date("d M y H:i",strtotime($value["last_updated_at"]))."</td>";
					  					echo "</tr></tbody>";
					  				}
					  				if($sl==0) echo "<tbody><tr><td class='text-center' colspan='6'>No data found.</td></tr></tbody>";
					  				echo "</table></div>";
					  			?>
			  				</div>
			  				<div class="card-footer">
			  				</div>
			  			</div>	
			  		</div>
			  		<?php endif; ?>

			  		<?php if($this->basic->is_exist("add_ons",array("project_id"=>20))) : ?>
			  		<div class="col-12">
			  			<div class="card">
			  				<div class="card-header">
			  					<h4><i class="fa fa-tasks"></i> <?php echo lang('Comboposter Campaign') ?></h4>
			  				</div>
			  				<div class="card-body data-card">
								<?php 

					  				echo "<div class='table-responsive2'><table class='table table-bordered'>";

					  				echo "<thead><tr>";
					  					echo "<th>";
				  						echo lang('User Name');
				  						echo "</th>";

					  					echo "<th>";
				  						echo lang('User Email');
				  						echo "</th>";

				  						echo "<th>";
				  						echo lang('campaign name');
				  						echo "</th>";

				  						echo "<th class='text-center'>";
				  						echo lang('Post Type');
				  						echo "</th>";

				  						echo "<th class='text-center'>";
				  						echo lang('Schedule time');
				  						echo "</th>";

				  						echo "<th class='text-center'>";
				  						echo lang('Posting Status');
				  						echo "</th>";
				  					echo "</tr></thead>";

					  				$sl=0;
					  				foreach ($comboposter_campaign as $key => $value) 
					  				{
					  					$sl++;
					  					echo "<tbody><tr>";
					  						echo "<td><a href='".base_url('admin/user_dashboard/').$value['user_id']."' target='_blank'>".$value["user_name"]."</a></td>";
					  						echo "<td><a href='".base_url('admin/user_dashboard/').$value['user_id']."' target='_blank'>".$value["user_email"]."</a></td>";
					  						echo "<td>".$value["campaign_name"]."</td>";
					  						echo "<td class='text-center'>".$value["post_type"]." Post</td>";
					  						echo "<td class='text-center'>".date("d M y H:i",strtotime($value["schedule_time"]))."</td>";
					  						echo "<td class='text-center'>".$value["posting_status"]."</td>";
					  					echo "</tr></tbody>";
					  				}
					  				if($sl==0) echo "<tbody><tr><td class='text-center' colspan='6'>No data found.</td></tr></tbody>";
					  				echo "</table></div>";
					  			?>
			  				</div>
			  				<div class="card-footer">
			  				</div>
			  			</div>	
			  		</div>
			  		<?php endif; ?>
			  		
				</div>
			</div>
		</div>
	</div>
</section>