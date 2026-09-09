<?php
	$usergroup = (int) $this->session->userdata('usergroup');
	$CI = get_instance();
	if (!isset($CI->acl)) {
		$CI->load->library('acl');
	}

	$canUsersManage = $CI->acl->can('users.manage');
	$canAccessManage = $CI->acl->can('access.manage');
	$canReservationDashboard = $CI->acl->can('reservation.dashboard.view');
	$canFrontdesk = $CI->acl->can('reservation.frontdesk.view');
	$canReservationManage = $CI->acl->can('reservation.manage');
	$canReservationReports = $CI->acl->can('reservation.reports.view');
	$canFinanceReports = $CI->acl->can('finance.reports.view');

	$canPosSales = $CI->acl->can('pos.sale.record');
	$canPosSalesView = $CI->acl->can('pos.sale.view');
	$canPosStock = $CI->acl->can('pos.stock.view');
	$canPosReports = $CI->acl->can('pos.reports.view');
	$canPosShift = $CI->acl->can('pos.shift.open');
	$canPosAudit = $CI->acl->can('pos.audit.view');
	$canPosSetup = $CI->acl->can('pos.setup.manage');

	$showPosMenu = ($canPosSales || $canPosSalesView || $canPosStock || $canPosReports || $canPosShift || $canPosAudit || $canPosSetup);
	$showAnyMenu = ($canReservationDashboard || $canFrontdesk || $canReservationManage || $canReservationReports || $canFinanceReports || $showPosMenu || $canUsersManage || $canAccessManage || $usergroup === 1);
?>
<!-- begin:: Aside -->
<button class="kt-aside-close " id="kt_aside_close_btn"><i class="la la-close"></i></button>
<div class="kt-aside  kt-aside--fixed  kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop" id="kt_aside">

	<!-- begin:: Aside -->
	<div class="kt-aside__brand kt-grid__item " id="kt_aside_brand">
		<div class="kt-aside__brand-logo">
			<a href="demo1/index.html">
				<img alt="Logo" src="<?php echo base_url() . 'assetsa/media/logos/logo-light.png'; ?>" />
			</a>
		</div>
		<div class="kt-aside__brand-tools">
			<button class="kt-aside__brand-aside-toggler" id="kt_aside_toggler">
				<span><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
						<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
							<polygon id="Shape" points="0 0 24 0 24 24 0 24" />
							<path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" id="Path-94" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999) " />
							<path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" id="Path-94" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999) " />
						</g>
					</svg></span>
				<span><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
						<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
							<polygon id="Shape" points="0 0 24 0 24 24 0 24" />
							<path d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z" id="Path-94" fill="#000000" fill-rule="nonzero" />
							<path d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z" id="Path-94" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999) " />
						</g>
					</svg></span>
			</button>
		</div>
	</div>

	<!-- end:: Aside -->

	<!-- begin:: Aside Menu -->
	 <div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">
		<div id="kt_aside_menu" class="kt-aside-menu " data-ktmenu-vertical="1" data-ktmenu-scroll="1" data-ktmenu-dropdown-timeout="500">
			<ul class="kt-menu__nav ">
				<?php if ($showAnyMenu) {?>
				
					<li class="kt-menu__item " aria-haspopup="true">
						<a href="<?php echo base_url() .'Alkebulan/dashboard'; ?>" class="kt-menu__link ">
							<span class="kt-menu__link-icon">
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<polygon id="Bound" points="0 0 24 0 24 24 0 24" />
										<path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" id="Shape" fill="#000000" fill-rule="nonzero" />
										<path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" id="Path" fill="#000000" opacity="0.3" />
									</g>
								</svg>
							</span>
							<span class="kt-menu__link-text">Dashboard</span>
						</a>
					</li>
					
					<li class="kt-menu__section ">
						<h4 class="kt-menu__section-text" style="color: #FFF">General Administration</h4>
						<i class="kt-menu__section-icon flaticon-more-v2"></i>
					</li>
					<?php if ($canReservationDashboard): ?>
					<li class="kt-menu__item" aria-haspopup="true">
						<a href="<?php echo base_url() .'Reservations/dashboard'; ?>" class="kt-menu__link">
							<span class="kt-menu__link-icon">
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<rect id="bound" x="0" y="0" width="24" height="24" />
										<path d="M4,5 L10,5 C11.1045695,5 12,5.8954305 12,7 L12,19 C12,20.1045695 11.1045695,21 10,21 L4,21 C2.8954305,21 2,20.1045695 2,19 L2,7 C2,5.8954305 2.8954305,5 4,5 Z" fill="#000000" opacity="0.3"/>
										<path d="M14,3 L20,3 C21.1045695,3 22,3.8954305 22,5 L22,17 C22,18.1045695 21.1045695,19 20,19 L14,19 C12.8954305,19 12,18.1045695 12,17 L12,5 C12,3.8954305 12.8954305,3 14,3 Z" fill="#000000"/>
									</g>
								</svg>
							</span>
							<span class="kt-menu__link-text">Reservation Dashboard</span>
						</a>
					</li>
					<?php endif; ?>

					<?php if ($canFrontdesk): ?>
					<li class="kt-menu__item" aria-haspopup="true">
						<a href="<?php echo base_url() .'Reservations/frontdesk'; ?>" class="kt-menu__link">
							<span class="kt-menu__link-icon">
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<rect x="0" y="0" width="24" height="24"/>
										<path d="M6,4 L18,4 C19.1045695,4 20,4.8954305 20,6 L20,18 C20,19.1045695 19.1045695,20 18,20 L6,20 C4.8954305,20 4,19.1045695 4,18 L4,6 C4,4.8954305 4.8954305,4 6,4 Z" fill="#000000" opacity="0.3"/>
										<path d="M8,8 L16,8 L16,10 L8,10 L8,8 Z M8,12 L16,12 L16,14 L8,14 L8,12 Z" fill="#000000"/>
									</g>
								</svg>
							</span>
							<span class="kt-menu__link-text">Front Desk Board</span>
						</a>
					</li>
					<?php endif; ?>

					<?php if ($canReservationReports || $canFinanceReports): ?>
					<li class="kt-menu__item" aria-haspopup="true">
						<a href="<?php echo base_url() .'Reservations/reports'; ?>" class="kt-menu__link">
							<span class="kt-menu__link-icon">
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<rect x="0" y="0" width="24" height="24"/>
										<path d="M5,5 L19,5 C20.1045695,5 21,5.8954305 21,7 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,7 C3,5.8954305 3.8954305,5 5,5 Z" fill="#000000" opacity="0.3"/>
										<path d="M7,15 L9,15 L9,11 L7,11 L7,15 Z M11,15 L13,15 L13,8 L11,8 L11,15 Z M15,15 L17,15 L17,12 L15,12 L15,15 Z" fill="#000000"/>
									</g>
								</svg>
							</span>
							<span class="kt-menu__link-text">Reports & Analytics</span>
						</a>
					</li>
					<?php endif; ?>

					<li class="kt-menu__item" aria-haspopup="true">
						<a href="<?php echo base_url() .'book'; ?>" class="kt-menu__link" target="_blank">
							<span class="kt-menu__link-icon">
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<rect x="0" y="0" width="24" height="24"/>
										<path d="M7,5 L17,5 C18.1045695,5 19,5.8954305 19,7 L19,17 C19,18.1045695 18.1045695,19 17,19 L7,19 C5.8954305,19 5,18.1045695 5,17 L5,7 C5,5.8954305 5.8954305,5 7,5 Z" fill="#000000" opacity="0.3"/>
										<path d="M9,9 L15,9 L15,11 L9,11 L9,9 Z M9,13 L13,13 L13,15 L9,15 L9,13 Z" fill="#000000"/>
									</g>
								</svg>
							</span>
							<span class="kt-menu__link-text">Online Booking Portal</span>
						</a>
					</li>

					<?php if ($canReservationManage): ?>
					<li class="kt-menu__item" aria-haspopup="true">
						<a href="<?php echo base_url() .'Alkebulan/manage_chalet'; ?>" class="kt-menu__link">
							<span class="kt-menu__link-icon">
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<rect id="bound" x="0" y="0" width="24" height="24" />
										<path d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z" id="Combined-Shape" fill="#000000" opacity="0.3" />
										<path d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z" id="Rectangle-102-Copy" fill="#000000" />
									</g>
								</svg>
							</span>
							<span class="kt-menu__link-text">Chalet Manager</span>
						</a>
					</li>
					<?php endif; ?>

					<?php if ($showPosMenu): ?>
					<li class="kt-menu__item kt-menu__item--submenu" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
						<a href="javascript:;" class="kt-menu__link kt-menu__toggle">
							<span class="kt-menu__link-icon">
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<rect x="0" y="0" width="24" height="24"/>
										<path d="M4,5 L20,5 C21.1045695,5 22,5.8954305 22,7 L22,19 C22,20.1045695 21.1045695,21 20,21 L4,21 C2.8954305,21 2,20.1045695 2,19 L2,7 C2,5.8954305 2.8954305,5 4,5 Z" fill="#000000" opacity="0.3"/>
										<path d="M6,3 L18,3 C18.5522847,3 19,3.44771525 19,4 L19,7 L5,7 L5,4 C5,3.44771525 5.44771525,3 6,3 Z M7,11 L11,11 L11,15 L7,15 L7,11 Z M13,11 L17,11 L17,13 L13,13 L13,11 Z M13,14 L17,14 L17,16 L13,16 L13,14 Z" fill="#000000"/>
									</g>
								</svg>
							</span>
							<span class="kt-menu__link-text">POS &amp; Inventory</span>
							<i class="kt-menu__ver-arrow la la-angle-right"></i>
						</a>
						<div class="kt-menu__submenu">
							<span class="kt-menu__arrow"></span>
							<ul class="kt-menu__subnav">
								<?php if ($canPosSales): ?>
								<li class="kt-menu__item" aria-haspopup="true">
									<a href="<?php echo base_url() . 'Pos/sales'; ?>" class="kt-menu__link">
										<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
										<span class="kt-menu__link-text">New Sale</span>
									</a>
								</li>
								<?php endif; ?>
								<?php if ($canPosSalesView): ?>
								<li class="kt-menu__item" aria-haspopup="true">
									<a href="<?php echo base_url() . 'Pos/sales_history'; ?>" class="kt-menu__link">
										<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
										<span class="kt-menu__link-text">Sales History</span>
									</a>
								</li>
								<?php endif; ?>
								<?php if ($canPosStock): ?>
								<li class="kt-menu__item" aria-haspopup="true">
									<a href="<?php echo base_url() . 'Pos/stock'; ?>" class="kt-menu__link">
										<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
										<span class="kt-menu__link-text">Stock Ledger</span>
									</a>
								</li>
								<?php endif; ?>
								<?php if ($canPosReports): ?>
								<li class="kt-menu__item" aria-haspopup="true">
									<a href="<?php echo base_url() . 'Pos/reports'; ?>" class="kt-menu__link">
										<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
										<span class="kt-menu__link-text">Reports</span>
									</a>
								</li>
								<?php endif; ?>
								<?php if ($canPosShift): ?>
								<li class="kt-menu__item" aria-haspopup="true">
									<a href="<?php echo base_url() . 'Pos/shift_control'; ?>" class="kt-menu__link">
										<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
										<span class="kt-menu__link-text">Shift Control</span>
									</a>
								</li>
								<?php endif; ?>
								<?php if ($canPosAudit): ?>
								<li class="kt-menu__item" aria-haspopup="true">
									<a href="<?php echo base_url() . 'Pos/audit_logs'; ?>" class="kt-menu__link">
										<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
										<span class="kt-menu__link-text">Audit Logs</span>
									</a>
								</li>
								<?php endif; ?>
								<?php if ($canPosSetup): ?>
								<li class="kt-menu__item" aria-haspopup="true">
									<a href="<?php echo base_url() . 'Pos/points'; ?>" class="kt-menu__link">
										<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
										<span class="kt-menu__link-text">POS Setup</span>
									</a>
								</li>
								<?php endif; ?>
							</ul>
						</div>
					</li>
					<?php endif; ?>

					<?php if ($canUsersManage || $canAccessManage): ?>
					<li class="kt-menu__item " aria-haspopup="true">
						<a href="<?php echo base_url() .'Access/users'; ?>" class="kt-menu__link">
							<span class="kt-menu__link-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<rect x="0" y="0" width="24" height="24"/>
										<path d="M4,20 C4,16.6862915 7.581722,14 12,14 C16.418278,14 20,16.6862915 20,20" stroke="#000000" stroke-width="2" stroke-linecap="round"/>
										<path d="M12,13 C15.3137085,13 18,10.3137085 18,7 C18,3.6862915 15.3137085,1 12,1 C8.6862915,1 6,3.6862915 6,7 C6,10.3137085 8.6862915,13 12,13 Z" fill="#000000" opacity="0.3"/>
									</g>
								</svg>
							</span>
							<span class="kt-menu__link-text">User Management</span>
						</a>
					</li>
					<?php endif; ?>

					<?php if ($canAccessManage): ?>
					<li class="kt-menu__item " aria-haspopup="true">
						<a href="<?php echo base_url() .'Access'; ?>" class="kt-menu__link">
							<span class="kt-menu__link-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<rect x="0" y="0" width="24" height="24"/>
										<path d="M12,2 C15.3137085,2 18,4.6862915 18,8 C18,11.3137085 15.3137085,14 12,14 C8.6862915,14 6,11.3137085 6,8 C6,4.6862915 8.6862915,2 12,2 Z" fill="#000000" opacity="0.3"/>
										<path d="M4,20 C4,16.6862915 7.581722,14 12,14 C16.418278,14 20,16.6862915 20,20" stroke="#000000" stroke-width="2" stroke-linecap="round"/>
									</g>
								</svg>
							</span>
							<span class="kt-menu__link-text">User Privileges</span>
						</a>
					</li>
					<?php endif; ?>
					

				<?php } ?>
				
			</ul>
		</div>
	</div> 

	<!-- end:: Aside Menu -->
</div>
<!-- end:: Aside -->
