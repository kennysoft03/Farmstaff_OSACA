<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo isset($heading) ? $heading : 'Error'; ?> - Steveak Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .error-container {
            max-width: 500px;
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 {
            color: #dc3545;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            margin-bottom: 30px;
        }
        .back-link {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }
        .back-link:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1><?php echo isset($heading) ? $heading : 'Error'; ?></h1>
        <p><?php echo isset($message) ? $message : 'An error occurred.'; ?></p>
        <a href="<?php echo site_url('admin/login'); ?>" class="back-link">Back to Login</a>
    </div>
</body>
</html>
						<!-- end:: Header Topbar -->
					</div>

					<!-- end:: Header -->
					<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
						<!-- begin:: Content -->
						<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
							<div class="row">
								<div class="col-lg-2">
								</div>
								<div class="col-lg-8">
									<div class="kt-portlet kt-portlet--last kt-portlet--head-lg kt-portlet--responsive-mobile" id="kt_page_portlet">
										<!--begin::Portlet-->
										<div class="kt-portlet kt-portlet--tabs">
											<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
											<div class="kt-portlet">
												<div class="kt-portlet__body kt-portlet__body--fit">
													<div class="kt-invoice-2">
														<div class="kt-invoice__head">
															<div class="row" style="padding-top: 30px; text-align:center;">
																<div class="col-lg-2">
																	<img height="100" width="100" src="<?php echo base_url() . 'assetsa/media/logos/logo.png'; ?>">
																</div>
																<div href="#" class="col-lg-10">
																	
																	<h5>204-979-9171</h5>
																	
																</div>
															</div>
															<div class="row" style="padding-top: 40px; text-align:center;">
																<div class="col-lg-12" ><img src="<?php echo base_url() . 'assetsa/media/logos/404.jpg'; ?>"></div>
																<p><h3>VIEW DOES NOT EXIST  OR HAS BEEN MOVED KINDLY CONTACT THE ADMINISTRATOR FOR FURTHER SUPPORT</h3></p>
															</div>
															
																
														</div>
														
														
														<div class="kt-invoice__actions">
															
														</div>
													</div>
												</div>
											</div>
										</div>
										</div>
									</div>
								</div>
								<div class="col-lg-2">
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

		<!-- end:: Page -->

		<!-- begin::Quick Panel -->
		<?php //echo $quick_panel_toggler; ?>	

		<!-- end::Quick Panel -->		

		

		
		<?php echo $js ; ?>	

		<script type="text/javascript">
			var validator;

			$('input.auto').autoNumeric('init');

			$('.kt-select2').select2({
            	placeholder: "Select a value",
            	width: '100%'
        	});

	        //Start: Get user Invoice Details
	        
	        function tax_receipt(params) {
				//alert(params);
				var ontheflyform    = document.createElement("form");
				
				ontheflyform.target = "_self";    
				ontheflyform.method = "POST";
				ontheflyform.action = "<?php echo base_url() . 'backoffice/get_tax_details'; ?>";
				
				// Create an input
				var ontheflyInput   = document.createElement("input");
				ontheflyInput.type  = "hidden";
				ontheflyInput.name  = "params";
				ontheflyInput.value = params;
				
				// Add the input to the form
				ontheflyform.appendChild(ontheflyInput);

				// Add the form to dom
				document.body.appendChild(ontheflyform);

				// Just submit
				ontheflyform.submit();
			}

			function atax_receipt(params) {
				//alert(params);
				var ontheflyform    = document.createElement("form");
				
				ontheflyform.target = "_self";    
				ontheflyform.method = "POST";
				ontheflyform.action = "<?php echo base_url() . 'backoffice/atax_receipt'; ?>";
				
				// Create an input
				var ontheflyInput   = document.createElement("input");
				ontheflyInput.type  = "hidden";
				ontheflyInput.name  = "params";
				ontheflyInput.value = params;
				
				// Add the input to the form
				ontheflyform.appendChild(ontheflyInput);

				// Add the form to dom
				document.body.appendChild(ontheflyform);

				// Just submit
				ontheflyform.submit();
			}

	        //End : Get user Invoice Details

	        function activity_indicator(selector,status) {
	        	// body...
				if (status=='start'){
	    			$('#'+selector).html('Working...').attr("disabled", true);
	        	}else{
	    			$('#'+selector).html('Save').attr("disabled", false);
	        	}
	        }
		</script>
	</body>

	<!-- end::Body -->
</html>