<?php echo view("include/upload_js"); ?>


<section class="section section_custom" id="comment_bulk_tag_campaign">
	<div class="section-header">
		<h1 class="modal-title text-center"><i class="fa fa-edit"></i> <?php echo lang("Edit Comment & Bulk Tag Campaign"); ?></h1>
		<div class="section-header-breadcrumb">
		  <div class="breadcrumb-item">
		  	<a href="<?php echo base_url("comment_reply_enhancers/post_list"); ?>">
		  	<?php echo lang("Tag Campaign"); ?></a>
		  </div>
		  <div class="breadcrumb-item">
		  	<a href="<?php echo base_url("comment_reply_enhancers/bulk_tag_campaign_list"); ?>">
		  		<?php echo lang("Campaign List"); ?>
		  	</a>
		  </div>
		  <div class="breadcrumb-item"><?php echo $page_title; ?></div>
		</div>
	</div>
	<div class="section-body">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<form action="#" enctype="multipart/form-data" id="bulk_tag_campaign_form" method="post">
							<input type="hidden" name="campaign_id" value="<?php echo $xdata[0]["id"];?>">
							<input type="hidden" name="tag_campaign_tag_machine_enabled_post_list_id" value="<?php echo $xdata[0]["tag_machine_enabled_post_list_id"];?>">
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label><?php echo lang("campaign name") ?> *
											<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo lang("campaign name"); ?>" data-content="<?php echo lang("put a name so that you can identify it later"); ?>"><i class='fa fa-info-circle'></i> </a>
										</label>
										<input value="<?php echo $xdata[0]["campaign_name"];?>" type="text" class="form-control"  name="campaign_name" id="campaign_name">
									</div>
								</div>

								<div class="col-12 col-md-6">
									<div class="form-group">
				                        <label><?php echo lang("Select Commenter Range") ?> *
				                        	<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo lang("Select Commenter Range");?>" data-content="<?php echo lang("This range is sorted by comment time in decending order.") ?>"><i class='fa fa-info-circle'></i> </a>
				                        </label>
				                        <select name="commenter_range" id="commenter_range"  class="form-control select2" style="width:100%;"> 
					                        <?php echo $commenter_range;?>                              
				                        </select>
				                    </div> 
								</div>

								<div class="col-12">
									<div class="form-group">
										<label><?php echo lang("Tag Content") ?> *
											<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo lang("Tag Content") ?>" data-content="<?php echo lang("Content to bulk tag commenters."); ?>"><i class='fa fa-info-circle'></i> </a>
										</label>
										<textarea class="form-control" name="message" id="message" placeholder="<?php echo lang("Content to bulk tag commenters.");?>" style="height:100px !important;"><?php echo $xdata[0]["tag_content"];?></textarea>
									</div>
								</div>
								
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label><?php echo lang("Do not tag these commenters") ?>
				                        	<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo lang("Do not tag these commenters") ?>" data-content="<?php echo lang("You can choose one or more. The commenters you choose here will be unlisted from this campaign and will not be tagged. Start typing a commenter name, it is auto-complete.") ?>"><i class='fa fa-info-circle'></i> </a>
				                        </label>
				                        <select style="width:100%;"  name="exclude[]" id="exclude" multiple class="tokenize-sample form-control exclude_autocomplete select2">   
					                        <?php 
					                        foreach ($xtag_exclude as $key => $value) 
				                       		{
				                       			echo  "<option selected value='".$value["commenter_fb_id"]."'>".$value["commenter_name"]."</option>";
				                       		}
					                        ?>                                  
				                        </select>
				                    </div>
								</div>

								<div class="col-12 col-md-6">
									<div class="row">
										<div class="col-12 col-md-6">
											<div class="form-group">
												<label><?php echo lang("image/video upload") ?>
													<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo lang("image/video upload") ?>" data-content="<?php echo lang("upload image or video to embed with your bulk tag comment.") ?>"><i class='fa fa-info-circle'></i></a>
												</label>

												<div class="form-group">      
							                        <div id="image_video_upload"><?php echo lang("upload") ?></div>	     
												</div>
												<input type="hidden" value="<?php echo $xdata[0]["uploaded_image_video"];?>" name="uploaded_image_video" id="uploaded_image_video">
											</div>
										</div>
										<div class="col-12 col-md-6">
											<?php if($xdata[0]["uploaded_image_video"]!="") 
											{
												$ext_exp=explode('.', $xdata[0]["uploaded_image_video"]);
												$ext=array_pop($ext_exp);
												$video_array=array("flv","mp4","wmv");
											?>
											<div class="form-group text-center">
												<label></label><br>
											<?php
												if(!in_array($ext,$video_array))
												{
													echo '<a href="#" title="'.lang('See image').'" id="img_preview" img-src="'.base_url("upload/comment_reply_enhancers/").$xdata[0]["uploaded_image_video"].'" class="btn btn-primary" ><i class="fas fa-image"></i></a>';
												}
												else
												{
													echo '<a href="#" title="'.lang('See Video').'" id="vid_preview" vid-src="'.base_url("upload/comment_reply_enhancers/").$xdata[0]["uploaded_image_video"].'" class="btn btn-primary" ><i class="fas fa-video"></i></a>';
												}
												echo '</div>';
											} ?>
										</div>
									</div>
								</div>

								<!-- <div class="form-group" id="custom_input_div">				                       
								    <label>
								   		<?php echo lang("Tag List")." [".lang("Up to").": ".$item_per_range."]";?> * 
								    	<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo lang("Tag These Commenters") ?>" data-content="<?php echo lang("Select the commenters you want to tag.") ?>"><i class='fa fa-info-circle'></i> </a>
								    </label>
								    <select style="width:100px;"  name="include[]" id="include" multiple="multiple" class="tokenize-sample form-control include_autocomplete">                                     
								    </select>
								</div>	 -->

								<input type="hidden" name="schedule_type" value="later" id="schedule_type">

								<div class="col-12 col-md-6">
									<div class="form-group schedule_block_item">
										<label><?php echo lang("Schedule time") ?>  <a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo lang("schedule time") ?>" data-content="<?php echo lang("Select date and time when you want to process this campaign.") ?>"><i class='fa fa-info-circle'></i> </a></label>
										<input value="<?php echo $xdata[0]["schedule_time"];?>" placeholder="<?php echo lang("time");?>"  name="schedule_time" id="schedule_time" class="form-control datetimepicker" type="text"/>
									</div>
								</div>

								<div class="col-12 col-md-6">
									<div class="form-group schedule_block_item">
										<label><?php echo lang("Time zone") ?>
											 <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo lang("time zone") ?>" data-content="<?php echo lang("server will consider your time zone when it process the campaign.") ?>"><i class='fa fa-info-circle'></i> </a>
										</label>
										<?php
										$time_zone[''] = lang("please select");
										echo form_dropdown('time_zone',$time_zone,$xdata[0]["time_zone"],'class="form-control select2" id="time_zone" required style="width:100%;"'); 
										?>
									</div>
								</div>
							</div>
						</form>
						
						<div class="clearfix"></div>
						<div class="card-footer padding-0" style="margin-top:20px;">
							<button class="btn btn-lg btn-primary" id="submit_post" name="submit_post" type="button"><i class="fas fa-edit"></i> <?php echo lang("Update Campaign") ?> </button>
							<a class="btn btn-lg btn-light float-right" onclick='goBack("comment_reply_enhancers/bulk_tag_campaign_list",0)'><i class="fas fa-times"></i> <?php echo lang("Cancel") ?> </a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
	$somethingwentwrong = lang("something went wrong.");
	$pleasewait = lang("please wait").'...';
	$areyousure = lang("are you sure");
	$startcommenternames = lang("Start typing commenter names you want to excude from tag list");
	$list_of_commenters = lang("List of commenters which this campaign will tag");
	$campaign_name_is_required=lang("Campaign name is required.");
	$tag_content_is_required=lang("Tag content is required.");
	$you_have_not_selected_commenters=lang("You have not selected commenters.");
	$no_subscribed_commenter_found=lang("No subscribed commenter found.");
	$reply_content_is_required=lang("Reply content is required.");
	$pleaseselectscheduletimetimezone = lang("Please select schedule time/time zone.");
	$item_per_range=(config('MyConfig')->item_per_range ?? 10);
    if($item_per_range=='') $item_per_range=50;
    $tag_machine_enabled_post_list_id=$xdata[0]["tag_machine_enabled_post_list_id"];
 ?>
<script>
	var base_url="<?php echo site_url(); ?>";
	var somethingwentwrong="<?php echo $somethingwentwrong;?>";
	var pleasewait="<?php echo $pleasewait;?>";
	var areyousure="<?php echo $areyousure;?>";
	var startcommenternames="<?php echo $startcommenternames;?>";
	var item_per_range="<?php echo $item_per_range;?>";
	var list_of_commenters="<?php echo $list_of_commenters;?>";
	var campaign_name_is_required="<?php echo $campaign_name_is_required;?>";
	var tag_content_is_required="<?php echo $tag_content_is_required;?>";
	var you_have_not_selected_commenters="<?php echo $you_have_not_selected_commenters;?>";
	var no_subscribed_commenter_found="<?php echo $no_subscribed_commenter_found;?>";
	var reply_content_is_required="<?php echo $reply_content_is_required;?>"
	var tag_machine_enabled_post_list_id="<?php echo $tag_machine_enabled_post_list_id;?>"
</script>

<script>

$("document").ready(function(){

	$('[data-toggle="popover"]').popover(); 
	$('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});


	$('.exclude_autocomplete').tokenize({
        datas: base_url+"comment_reply_enhancers/commenter_autocomplete/"+tag_machine_enabled_post_list_id,
        placeholder: startcommenternames,
        dropdownMaxItems: 20,
        tokensMaxItems: item_per_range
    });



    $(document).on('click','#submit_post',function(){    
        event.preventDefault();	    	

    	var campaign_name = $("#campaign_name").val();
    	var message = $("#message").val();
    	var commenter_range = $("#commenter_range").val();
    	
    	if(campaign_name=="")
    	{
    		swal('<?php echo lang("Warning"); ?>',"<?php echo lang('Campaign name is required.');?>", 'warning');
    		return;
    	}

    	if(message=="")
    	{
    		swal('<?php echo lang("Warning"); ?>',"<?php echo lang('Tag content is required.');?>", 'warning');
    		return;
    	}

    	if(commenter_range=="" || commenter_range==null || typeof(commenter_range) == "undefined")
    	{    		
    		swal('<?php echo lang("Warning"); ?>',"<?php echo lang('You have not selected commenters.');?>", 'warning');
    		return;
    	}

    	var schedule_type = 'later';
    	var schedule_time = $("#schedule_time").val();
    	var time_zone = $("#time_zone").val();
    	var pleaseselectscheduletimetimezone = "<?php echo $pleaseselectscheduletimetimezone; ?>";
    	if(schedule_type=='later' && (schedule_time=="" || time_zone==""))
    	{
    		swal('<?php echo lang("Warning"); ?>',"<?php echo lang('Please select schedule time/time zone.');?>",'warning');
    		return;
    	}

	    $(this).addClass('btn-progress')
    	var that = $(this);  	
      	
      	var queryString = new FormData($("#bulk_tag_campaign_form")[0]);
      	$.ajax({
			type:'POST' ,
			url: base_url+"comment_reply_enhancers/edit_bulk_tag_campaign_action",
			data: queryString,
			cache: false,
			contentType: false,
			processData: false,
			dataType:'JSON',
			success:function(response)
	       	{  
	       		$(that).removeClass('btn-progress');

      			if(response.status=='1') 
      			{
      				var span = document.createElement("span");
      				span.innerHTML = response.message;
      				swal({ title:'<?php echo lang("Campagin has been updated successfully."); ?>', content:span,icon:'success'});
      			}
      			else
      			{
      				var span = document.createElement("span");
      				span.innerHTML = '';
      				swal({ title:response.message, content:span,icon:'error'});
      			}
	       	}
      	});
    });


  	$("#image_video_upload").uploadFile({
        url:base_url+"comment_reply_enhancers/upload_image_video",
        fileName:"myfile",
        maxFileSize:100*1024*1024,
        showPreview:false,
        returnType: "json",
        dragDrop: true,
        showDelete: true,
        multiple:false,
        maxFileCount:1, 
        acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF,.flv,.mp4,.wmv,.WMV,.MP4,.FLV",
        deleteCallback: function (data, pd) {
            var delete_url="<?php echo site_url('comment_reply_enhancers/delete_uploaded_file');?>";
            $.post(delete_url, {op: "delete",name: data},
                function (resp,textStatus, jqXHR) {
                	$("#uploaded_image_video").val('');                
                });
           
         },
         onSuccess:function(files,data,xhr,pd)
           {
               // var data_modified = base_url+"upload/commenttagmachine/"+data;
               $("#uploaded_image_video").val(data);                 
               $("#img_preview").hide();		
               $("#vid_preview").hide();   		
           }
    });

    // to preview attachment if available
      $(document).on('click', '#img_preview,#vid_preview', function(event) {
      	event.preventDefault();

      	$("#preview_modal").modal();

      	var imgSrc = $("#img_preview").attr("img-src");
      	var vidSrc = $("#vid_preview").attr("vid-src");
      	if(imgSrc != "" && typeof(imgSrc) != "undefined")
      	{
      		$(".modal-body").append('<img id="showImage" src="'+imgSrc+'" alt="" style="width:100%">');
      	} 
      	
      	if(vidSrc != "" && typeof(vidSrc) != "undefined")
      	{
      		$(".modal-body").append('<video width="100%" controls style="border:1px solid #ccc"><source src="'+vidSrc+'"></video>');
      	}


      });

      $("#preview_modal").on('hidden.bs.modal', function (){
      	$(".modal-body").html("");
      });

});
</script>






<style type="text/css" media="screen">
	.popover
	{
	    min-width: 300px !important;
	}
	.tokenize-sample,.Tokenize{border:none !important;padding:0 !important;}
	
	.preview{padding:10px 0 !important;}


	.hidden
	{
		display: none;
	}

	.TokensContainer{height: 140px !important;}	
	.content-wrapper{background: #fff;}
	.ajax-upload-dragdrop{width:100% !important;}
	.content-wrapper{background: #eee !important;}
</style>


<div class="modal fade" id="preview_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    	</div>
      <div class="modal-body"></div>
    </div>
  </div>
</div>