							<div class="kt-header__topbar-item kt-header__topbar-item--user">
								<div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">
									<div class="kt-header__topbar-user">
										<?php
										$emailaddress = (string) $this->session->userdata('emailaddress');
										$userData = $this->session->userdata('user_data');
										$displayName = '';
										if (is_array($userData)) {
											$surname = isset($userData['surname']) ? (string) $userData['surname'] : (isset($userData['sname']) ? (string) $userData['sname'] : '');
											$othernames = isset($userData['othernames']) ? (string) $userData['othernames'] : (isset($userData['onames']) ? (string) $userData['onames'] : '');
											$displayName = trim($surname . ' ' . $othernames);
										}
										if ($displayName === '') {
											$displayName = $emailaddress;
										}
										?>
										<span class="kt-header__topbar-welcome kt-hidden-mobile">Hi,</span>
										<span class="kt-header__topbar-username kt-hidden-mobile"><?php echo html_escape($displayName); ?></span>
										<img class="kt-hidden" alt="Pic" src="<?php echo base_url() . 'assetsa/media/users/300_25.jpg'; ?>" />

										<!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
										<span class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold">S</span>
									</div>
								</div>
								<div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl">

									<!--begin: Head -->
									<div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x" style="background-image: url(<?php echo base_url() . 'assetsa/media/misc/bg-1.jpg'; ?>)">
										<div class="kt-user-card__avatar">
											<img class="kt-hidden" alt="Pic" src="<?php echo base_url() . 'assetsa/media/users/300_25.jpg'; ?>" />

											
											<span class="kt-badge kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-success">
												<?php echo strtoupper(substr($displayName, 0,1)); ?></span>
										</div>
										<div class="kt-user-card__name">
											<?php echo html_escape($displayName); ?>
										</div>
										
									</div>

									<!--end: Head -->

									<!--begin: Navigation -->
									<div class="kt-notification">
										<a href="#" class="kt-notification__item">
											<div class="kt-notification__item-icon">
												<i class="flaticon2-calendar-3 kt-font-success"></i>
											</div>
											<div class="kt-notification__item-details">
												<div class="kt-notification__item-title kt-font-bold">
													App Settings
												</div>
												<div class="kt-notification__item-time">
													Account settings and Configurations
												</div>
											</div>
										</a>
										
										<div class="kt-notification__custom kt-space-between">
											<a href="<?php echo base_url() .'backoffice/signout'; ?>" class="btn btn-label btn-label-brand btn-sm btn-bold">Sign Out</a>
											<a href="<?php echo base_url() .'backoffice/change_password'; ?>" class="btn btn-clean btn-sm btn-bold">Change Password</a>
										</div>
									</div>

									<!--end: Navigation -->
								</div>
							</div>