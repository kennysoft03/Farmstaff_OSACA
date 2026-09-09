<!DOCTYPE html>

	<!-- begin::Head -->
	<head>

		<!--begin::Base Path (base relative path for assets of this page) -->
		<!-- <base href="../../../../"> -->

		<!--end::Base Path -->
		<meta charset="utf-8" />
		<title>Steveak | Page Not Found</title>
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
				<?php //echo $side_menu; ?>
				<!-- end:: Aside -->

				<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">

					<!-- begin:: Header -->
					<div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed ">

						<!-- begin:: Header Menu -->
						<?php echo $header_menu; ?>
						<!-- end:: Header Menu -->

						<!-- begin:: Header Topbar -->
						<div class="kt-header__topbar">
							
						</div>
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