<?php
	$seller_id= $this->session->userdata('seller_id');
	if (isset($site_sett) && is_array($site_sett)){
		$site_id        = $site_sett['id'];
		$site_title     = $site_sett['site_title'];
		$meta_desc      = $site_sett['meta_desc'];
		$meta_keys      = $site_sett['meta_keys'];
		$currentyear    = $site_sett['currentyear'];
		$admin_mail 	= $site_sett['admin_mail'];
		$adminname 		= $site_sett['adminname'];
		$address   		= $site_sett['address'];
		$welcome_intro 	= $site_sett['welcome_intro'];
		$vision 		= $site_sett['vision'];
		$mission 		= $site_sett['mission'];
		$aboutus 		= $site_sett['aboutus'];
		$history 		= $site_sett['history'];
		$youtube_code 	= $site_sett['youtube_code'];
		$youtube_img    = $site_sett['youtube_img'];
		$home_logo      = $site_sett['home_logo'];
		$welcome_image  = $site_sett['welcome_image'];

		if (isset($home_logo) && !empty($home_logo)){
			$home_logo=base_url().'uploads/'.$home_logo;
		}

		if (isset($welcome_image) && !empty($welcome_image)){
			$welcome_image=base_url().'uploads/'.$welcome_image;
		}

		if (isset($youtube_img) && !empty($youtube_img)){
			$youtube_img=base_url().'uploads/'.$youtube_img;
		}
	}else{
		$site_id     	= 0;
		$site_title  	= '';
		$meta_desc   	= '';
		$meta_keys   	= '';
		$currentyear 	= '';
		$admin_mail  	= '';
		$adminname  	= '';
		$address     	= '';
		$welcome_intro	= '';
		$vision			= '';
		$mission		= '';
		$aboutus		= '';
		$history		= '';
		$youtube_code	= '';
		$home_logo  	= '';	
		$youtube_img    = '';	
		$welcome_image  = '';	
	}
?>
<!DOCTYPE html>
<html lang="en">

	<!-- begin::Head -->
	<head>

		<!--begin::Base Path (base relative path for assets of this page) -->
		<!-- <base href="../../../../"> -->

		<!--end::Base Path -->
		<meta charset="utf-8" />
		<title>Alkebulan | Lacampagne Tropicana Resorts</title>
		<meta name="description" content="Buttons examples">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    	<?php echo $css ; ?>
    	<style>
    		.kt-select2 { width: 100% !important; }
    	</style>
	</head>

	<!-- end::Head -->

	<!-- begin::Body -->
	<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">

		<!-- begin:: Page -->

		<!-- begin:: Header Mobile -->
		<?php echo $header_mobile; ?>
		<!-- end:: Header Mobile -->

		<div class="kt-grid kt-grid--hor kt-grid--root">
			<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">

				<!-- begin:: Aside -->
				<?php echo $side_menu; ?>
				<!-- end:: Aside -->

				<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">

					<!-- begin:: Header -->
					<div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed ">

						<!-- begin:: Header Menu -->
						<?php echo $header_menu; ?>
						<!-- end:: Header Menu -->

						<!-- begin:: Header Topbar -->
						<div class="kt-header__topbar">

							<!--begin: User Bar -->
							<?php echo $userbar_menu; ?>
							<!--end: User Bar -->
						</div>
						<!-- end:: Header Topbar -->
					</div>

					<!-- end:: Header -->


					<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

						<!-- begin:: Subheader -->
						<div class="kt-subheader   kt-grid__item" id="kt_subheader">
							<div class="kt-container  kt-container--fluid ">
								<div class="kt-subheader__main">
									<h3 class="kt-subheader__title">
										 Website Settings</h3>
									<span class="kt-subheader__separator kt-hidden"></span>
									<div class="kt-subheader__breadcrumbs">
										<a href="#" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
										<span class="kt-subheader__breadcrumbs-separator"></span>
										<a href="#" class="kt-subheader__breadcrumbs-link" style="font-size: 1.5rem;"> | UNIVERSITY OF DELTA</a>
										<span class="kt-subheader__breadcrumbs-separator"></span>
										<h2><strong></strong></h2>
									</div>
								</div>
								<!-- <div class="kt-subheader__toolbar">
									<div class="kt-subheader__wrapper">
										<div class="kt-portlet__head-wrapper">
											<a href="<?php //echo base_url() . 'Alkebulan/manage_category'; ?>" class="btn btn-clean btn-icon-sm">
												<i class="la la-long-arrow-left"></i>
												Back
											</a>
											&nbsp;
										</div>
									</div>
								</div> -->
							</div>
						</div>
						<!-- end:: Subheader -->

						<!-- begin:: Content -->
						<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
							<div class="row">
								<div class="col-lg-12">
									<!--begin::Portlet-->
									<div class="kt-portlet kt-portlet--tabs">
										<div class="kt-portlet__head">
											<div class="kt-portlet__head-label">
												<h3 class="kt-portlet__head-title">
													Manage
												</h3>
											</div>
											<div class="kt-portlet__head-toolbar">
												<ul class="nav nav-tabs nav-tabs-bold nav-tabs-line nav-tabs-line-right nav-tabs-line-brand" role="tablist">
													<li class="nav-item">
														<a class="nav-link active" data-toggle="tab" href="#kt_portlet_tab_2_1" role="tab">
															<i class="la la-book"></i>
															General 
														</a>
													</li>

													<li class="nav-item">
														<a class="nav-link" data-toggle="tab" href="#kt_portlet_tab_2_2" role="tab">
															<i class="la la-calendar"></i>
															Welcome 
														</a>
													</li>
													<li class="nav-item">
														<a class="nav-link" data-toggle="tab" href="#kt_portlet_tab_3_3" role="tab">
															<i class="la la-video"></i>
															Youtube 
														</a>
													</li>
												</ul>
											</div>
										</div>
										<div class="kt-portlet__body">
											
											<div class="tab-content">
												<div class="tab-pane active" id="kt_portlet_tab_2_1">
													<!--begin::Form-->
													<form class="kt-form kt-form--label-right" id="kt_form_1">
														<div class="kt-portlet__body">
															<div class="row">
																<div class="col-xl-2"></div>
																<input type="hidden" id="site_id" name="site_id" class="form-control" placeholder="" value="<?php echo $site_id; ?>">
																<input type="hidden" id="seller_id" name="seller_id" class="form-control" placeholder="" value="<?php echo $seller_id; ?>">
																<div class="col-xl-8">
																	<div class="form-group row">
																		<label class="col-3 col-form-label">University Name </label>
																		<div class="col-9">
																			<input type="text" id="site_title" name="site_title" class="form-control" placeholder="Location Name" value="<?php echo $site_title; ?>">
																		</div>
																	</div>

																	<div class="form-group row">
																		<label class="col-3 col-form-label">Meta Kewords best Describing the University *</label>
																		<div class="col-9">
																			<input type="text" id="meta_keys" name="meta_keys" class="form-control" placeholder="Meta Titles" value="<?php echo $meta_keys; ?>">
																		</div>
																	</div>

																	<div class="form-group row">
																		<label class="col-3 col-form-label">Meta Description *</label>
																		<div class="col-9">
																			<textarea class="form-control" id="meta_desc" name="meta_desc" rows="4"><?php echo $meta_desc; ?></textarea>
																		</div>
																	</div>

																	
																	<div class="form-group row">
																		<label class="col-3 col-form-label">Administrators Email * </label>
																		<div class="col-9">
																			<input type="text" id="admin_mail" name="admin_mail" class="form-control" placeholder="Administrators Email" value="<?php echo $admin_mail; ?>">
																		</div>
																	</div>
																	<div class="form-group row">
																		<label class="col-3 col-form-label">Address * </label>
																		<div class="col-9">
																			<input type="text" id="address" name="address" class="form-control" placeholder="Church Address" value="<?php echo $address; ?>">
																		</div>
																	</div>
																	<div class="form-group row">
																		<label class="col-3 col-form-label">University  Current Vice-Chancellor * </label>
																		<div class="col-9">
																			<input type="text" id="adminname" name="adminname" class="form-control" placeholder="Current University Vice-Chancellor" value="<?php echo $adminname; ?>">
																		</div>
																	</div>
																	<div class="form-group row">
																		<label class="col-3 col-form-label">University Website Logo *</label>
																		<div class="col-3">
																			<input type="file" id="login_logo" name="login_logo" class="form-control" placeholder="Login Logo: minimum width of 1200px" style="cursor: pointer;"  accept="image/*" onchange="loadFile(event)">
																		</div> 
																		<div class="col-6">
																			<center><img id="uploadedimg" src="<?php echo $home_logo; ?>" style="width:50%; max-width:200px; height: auto;"/></center>
																		</div> 
																	</div>
																</div>
															</div>

														</div>

														<div class="kt-portlet__foot">
															<div class=" ">
																<div class="row">
																	<div class="col-lg-9 ml-lg-auto">
																		<button type="submit" class="btn btn-primary" id="btn_bottom_save">Save</button>
																		<button type="reset" class="btn btn-secondary">Cancel</button>
																	</div>
																</div>
															</div>
														</div>
													</form>
													<!--end::Form-->														
												</div>
												<div class="tab-pane " id="kt_portlet_tab_2_2">
													<!--begin::Portlet-->
													<div class="kt-portlet">
														<div class="kt-portlet__body">
															<div class="tab-content">
																<!--begin::Form-->
																<form class="kt-form kt-form--label-right" id="kt_form_2">
																	<div class="kt-portlet__body">
																		<div class="row">
																			
																			<div class="col-xl-12">
																				
																				<div class="form-group row">
																					<label class="col-3 col-form-label">Welcome Image *</label>
																					<div class="col-3">
																						<input type="file" id="welcome_image" name="welcome_image" class="form-control" placeholder="Application Logo: minimum width of 1200px" style="cursor: pointer;"  accept="image/*" onchange="loadFile2(event)">
																					</div> 
																					<div class="col-6">
																						<center><img id="uploadedimg_site" src="<?php echo $welcome_image; ?>" style="width: 100%; max-width: 440px;height: auto;"/></center>
																					</div> 
																				</div>
																				<div class="form-group row">
																					<label class="col-3 col-form-label">Welcome Intro *</label>
																					<div class="col-8">
																						<textarea class="form-control" id="welcome_intro" name="welcome_intro" rows="4"><?php echo $welcome_intro; ?></textarea>
																					</div>
																				</div>
																				<div class="form-group row">
																					<label class="col-3 col-form-label">University Mission Statement*</label>
																					<div class="col-8">
																						<textarea class="form-control" id="mission" name="mission" rows="4"><?php echo $mission; ?></textarea>
																					</div>
																				</div>

																				<div class="form-group row">
																					<label class="col-3 col-form-label">University Vision Statement *</label>
																					<div class="col-8">
																						<textarea class="form-control" id="vision" name="vision" rows="4"><?php echo $vision; ?></textarea>
																					</div>
																				</div>
																				<div class="form-group row">
																					<label class="col-2 col-form-label">About Page Content </label>
																					<div class="col-12">
																						<div class="summernote" id="summernote" name="summernote"><?=$aboutus;?></div>
																					</div>
																				</div>
																				<div class="form-group row">
																					<label class="col-2 col-form-label">University History </label>
																					<div class="col-12">
																						<div class="summernote" id="summernote2" name="summernote2"><?=$history;?></div>
																					</div>
																				</div>
																			</div>
																		</div>

																	</div>

																	<div class="kt-portlet__foot">
																		<div class=" ">
																			<div class="row">
																				<div class="col-lg-9 ml-lg-auto">
																					<button type="submit" class="btn btn-primary" id="btn_top_save_wel">Save</button>
																				</div>
																			</div>
																		</div>
																	</div>
																</form>
																<!--end::Form-->	
															</div>
														</div>
													</div>
													<!--end::Portlet-->
												</div>
												<div class="tab-pane " id="kt_portlet_tab_3_3">
													<!--begin::Portlet-->
													<div class="kt-portlet">
														<div class="kt-portlet__body">
															<div class="tab-content">
																<!--begin::Form-->
																<form class="kt-form kt-form--label-right" id="kt_form_3">
																	<div class="kt-portlet__body">
																		<div class="row">
																			<div class="col-xl-12">
																				<div class="form-group row">
																					<label class="col-3 col-form-label">Youtube Image *</label>
																					<div class="col-3">
																						<input type="file" id="youtube_img" name="youtube_img" class="form-control" placeholder="Youtube Background Image: minimum width of 1200px" style="cursor: pointer;"  accept="image/*" onchange="loadFile3(event)">
																					</div> 
																					<div class="col-6">
																						<center><img id="uploadyoutube_img" src="<?php echo $youtube_img; ?>" style="width: 100%; max-width:440px; height: auto;"/></center>
																					</div> 
																				</div>
																				<div class="form-group row">
																					<label class="col-3 col-form-label">Youtube Code * </label>
																					<div class="col-8">
																						<input type="text" id="youtube_code" name="youtube_code" class="form-control" placeholder="Youtube Video" value="<?php echo $youtube_code; ?>">
																					</div>
																				</div>
																			</div>
																		</div>

																	</div>

																	<div class="kt-portlet__foot">
																		<div class=" ">
																			<div class="row">
																				<div class="col-lg-9 ml-lg-auto">
																					<button type="submit" class="btn btn-primary" id="btn_top_save_you">Save</button>
																				</div>
																			</div>
																		</div>
																	</div>
																</form>
																<!--end::Form-->	
															</div>
														</div>
													</div>
													<!--end::Portlet-->
												</div>
												
											</div>
										</div>
									</div>
									<!--end::Portlet-->
								</div>
							</div>
						</div>

						<!-- end:: Content -->
					</div>

					<!-- begin:: Footer -->
					<?php echo $footer; ?>
					<!-- end:: Footer -->
					
				</div>

			</div>
		</div>


		<!-- begin::Scrolltop -->
		<?php echo $scroll_top; ?>			

	

		
		<?php echo $js ; ?>	
		<!-- <script src="<?php echo base_url() . 'assetsa/js/ckeditor-classic.bundle.js'; ?>"></script>
		<script src="<?php echo base_url() . 'assetsa/js/ckeditor-classic.js'; ?>"></script> -->
		<script src="<?php echo base_url() . 'assetsa/js/summernote.js'; ?>"></script>
		
		<script type="text/javascript">
			var validator;


			$('input.auto').autoNumeric('init');

			$('.kt-select2').select2({
            	placeholder: "Select a value",
            	width: '100%'
        	});

        	/*Logo Classes for Image Processing*/
	        $('#btn_top_save').on('click', function(e) {
	        	$("#kt_form_1").submit();
			});

	        $(document).on('change','#login_logo',function(){
	            var property       = document.getElementById('login_logo').files[0];
	            var file_name      = property.name;
	            var file_extension = file_name.split('.').pop().toLowerCase();

	            if(jQuery.inArray(file_extension,['jpg','jpeg','png','gif']) == -1){
	                msg="Invalid image file selected! <br>Please ensure the file you selected is a valid PNG or JPG image file and try again";
					swal.fire("Error!", msg, "error"); 
                    return false;
	            }
	        });

	        var loadFile = function(event) {
		        var reader = new FileReader();
		        reader.onload = function(){
		          var output = document.getElementById('uploadedimg');
		          output.src = reader.result;
		        };
		        reader.readAsDataURL(event.target.files[0]);
		    };
			
			$( "#kt_form_1" ).validate({
	            // define validation rules
	            rules: {
	                site_title: {
	                    required: true,
	                    minlength: 2,
	                    maxlength: 450
	                },
	                meta_keys: {
	                    required: true,
	                    minlength: 2,
	                    maxlength: 1000
	                },
	                meta_desc: {
	                    required: true,
	                },
	                address: {
	                    required: true,
	                },
	                admin_mail: {
	                    required: true,
	                },
	                currentyear: {
	                    required: true,
	                },
	            },
	            
	            //display error alert on form submit  
	            invalidHandler: function(event, validator) {             
	                var alert = $('#kt_form_1_msg');
	                alert.removeClass('kt--hide').show();
	                KTUtil.scrollTo('m_form_1_msg', -200);
	            },

	            submitHandler: function (form) {
	                //form[0].submit(); // submit the form
	                submitform();
	            }
	        });

	        function submitform(){
				var property  = document.getElementById('login_logo').files[0];
				
				try {
					var file_name = property.name;
				}catch(err) {
				  	var property='';
				}

				try {
					var file_name2 = sitelogo.name;
				}catch(err) {
				  	var sitelogo='';
				}

				var form_data = new FormData();
            	form_data.append("file",property);   
            	form_data.append("address",$("#address").val());  
            	form_data.append("admin_mail",$("#admin_mail").val()); 
            	form_data.append("site_title",$("#site_title").val());
            	form_data.append("meta_keys",$("#meta_keys").val());
            	form_data.append("adminname",$("#adminname").val());
            	form_data.append("meta_desc",$("#meta_desc").val());

	        	$.ajax({
                    url: "<?php echo base_url() . 'Alkebulan/update_site_settings'; ?>",
                    type: "POST",
                    data: form_data ,
                    contentType:false,
	                cache:false,
	                processData:false,
	                beforeSend:function(){
	                	activity_indicator('start');
			        },
                    success: function (data) {
                    	activity_indicator('stop');
                        var items= $.parseJSON(data);
                        if (items.status==true){
                            swal.fire("Successful!", items.message, "success");
                            return false;
                        }else{
                            swal.fire("Error!", items.message, "error"); 
                            return false;
                        }      
                    }
                });
	        }

	        /* Start : Welcome Image Logo processsing Classes */
	        $('#btn_top_save_wel').on('click', function(e) {
	        	$("#kt_form_2").submit();
			});

		    $(document).on('change','#welcome_image',function(){
	            var sitelogo       = document.getElementById('welcome_image').files[0];
	            var file_name2     = sitelogo.name;
	            var file_extension2 = file_name2.split('.').pop().toLowerCase();

	            if(jQuery.inArray(file_extension2,['jpg','jpeg']) == -1){
	                msg="Invalid image file selected! <br>Please ensure the file you selected is a valid JPG image file and try again";
					swal.fire("Error!", msg, "error"); 
                    return false;
	            }
	        });

	        var loadFile2 = function(event) {
		        var reader = new FileReader();
		        reader.onload = function(){
		          var output = document.getElementById('uploadedimg_site');
		          output.src = reader.result;
		        };
		        reader.readAsDataURL(event.target.files[0]);
		    };

		    $( "#kt_form_2" ).validate({
	            // define validation rules
	            rules: {
	                welcome_intro: {
	                    required: true,
	                },
	                mission: {
	                    required: true,
	                },
	                vision: {
	                    required: true,
	                },
	            },
	            
	            //display error alert on form submit  
	            invalidHandler: function(event, validator) {             
	                var alert = $('#kt_form_2_msg');
	                alert.removeClass('kt--hide').show();
	                KTUtil.scrollTo('m_form_1_msg', -200);
	            },

	            submitHandler: function (form) {
	                //form[0].submit(); // submit the form
	                submitform_welcome();
	            }
	        });

	        function submitform_welcome(){
				var property  = document.getElementById('welcome_image').files[0];
				var aboutus = $('#summernote').summernote('code');
				var history = $('#summernote2').summernote('code');
				
				try {
					var file_name = property.name;
				}catch(err) {
				  	var property='';
				}

				var form_data = new FormData();
            	form_data.append("file",property);
            	form_data.append("welcome_intro",$("#welcome_intro").val());
            	form_data.append("mission",$("#mission").val());
            	form_data.append("vision",$("#vision").val());
            	form_data.append("aboutus",aboutus);
            	form_data.append("history",history);

	        	$.ajax({
                    url: "<?php echo base_url() . 'Alkebulan/update_welcome_settings'; ?>",
                    type: "POST",
                    data: form_data ,
                    contentType:false,
	                cache:false,
	                processData:false,
	                beforeSend:function(){
	                	activity_indicator('start');
			        },
                    success: function (data) {
                    	activity_indicator('stop');
                        var items= $.parseJSON(data);
                        if (items.status==true){
                            swal.fire("Successful!", items.message, "success");
                            return false;
                        }else{
                            swal.fire("Error!", items.message, "error"); 
                            return false;
                        }      
                    }
                });
	        }

	        /* Start : Youtube Data processsing Classes */
	        $('#btn_top_save_you').on('click', function(e) {
	        	$("#kt_form_3").submit();
			});

		    $(document).on('change','#youtube_img',function(){
	            var youlogo       = document.getElementById('youtube_img').files[0];
	            var file_name2     = youlogo.name;
	            var file_extension2 = file_name2.split('.').pop().toLowerCase();

	            if(jQuery.inArray(file_extension2,['jpg','jpeg']) == -1){
	                msg="Invalid image file selected! <br>Please ensure the file you selected is a valid JPG image file and try again";
					swal.fire("Error!", msg, "error"); 
                    return false;
	            }
	        });

	        var loadFile3 = function(event) {
		        var reader = new FileReader();
		        reader.onload = function(){
		          var output = document.getElementById('uploadyoutube_img');
		          output.src = reader.result;
		        };
		        reader.readAsDataURL(event.target.files[0]);
		    };

		    $( "#kt_form_3" ).validate({
	            // define validation rules
	            rules: {
	                youtube_code: {
	                    required: true,
	                },
	            },
	            
	            //display error alert on form submit  
	            invalidHandler: function(event, validator) {             
	                var alert = $('#kt_form_2_msg');
	                alert.removeClass('kt--hide').show();
	                KTUtil.scrollTo('m_form_1_msg', -200);
	            },

	            submitHandler: function (form) {
	                //form[0].submit(); // submit the form
	                submitform_youtube();
	            }
	        });

	        function submitform_youtube(){
				var youtube_img  = document.getElementById('youtube_img').files[0];
				
				try {
					var file_name = youtube_img.name;
				}catch(err) {
				  	var youtube_img='';
				}

				var form_data = new FormData();
            	form_data.append("file",youtube_img);
            	form_data.append("youtube_code",$("#youtube_code").val());

	        	$.ajax({
                    url: "<?php echo base_url() . 'Alkebulan/update_youtube_settings'; ?>",
                    type: "POST",
                    data: form_data ,
                    contentType:false,
	                cache:false,
	                processData:false,
	                beforeSend:function(){
	                	activity_indicator('start');
			        },
                    success: function (data) {
                    	activity_indicator('stop');
                        var items= $.parseJSON(data);
                        if (items.status==true){
                            swal.fire("Successful!", items.message, "success");
                            return false;
                        }else{
                            swal.fire("Error!", items.message, "error"); 
                            return false;
                        }      
                    }
                });
	        }
	        //End : Pricing - Sale

	        function activity_indicator(status) {
	        	// body...
				if (status=='start'){
	    			$('#btn_top_save').html('Working...').attr("disabled", true);
	    			$('#btn_top_save_wel').html('Working...').attr("disabled", true);
	    			$('#btn_top_save_you').html('Working...').attr("disabled", true);
	        	}else{
	    			$('#btn_top_save').html('Save').attr("disabled", false);
	    			$('#btn_top_save_wel').html('Save').attr("disabled", false);
	    			$('#btn_top_save_you').html('Save').attr("disabled", false);
	        	}
	        }

		</script>
	</body>

	<!-- end::Body -->
</html>