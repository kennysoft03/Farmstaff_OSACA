<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($page_title) ? htmlspecialchars($page_title).' — ' : ''; ?>Employer Portal | <?php echo SITE_NAME; ?></title>
<meta name="robots" content="noindex, nofollow">
<meta name="theme-color" content="#111111">
<link rel="icon"       type="image/png" sizes="32x32" href="<?php echo base_url('assetsa/media/logos/farvicon.png'); ?>">
<link rel="shortcut icon"              href="<?php echo base_url('assetsa/media/logos/farvicon.png'); ?>">
<link rel="apple-touch-icon"           href="<?php echo base_url('assetsa/media/logos/farvicon.png'); ?>">

<!-- KT Template CSS -->
<link href="<?php echo base_url('assetsa/css/style.css'); ?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url('assetsa/vendors/general/perfect-scrollbar/css/perfect-scrollbar.css'); ?>" rel="stylesheet"/>
<link href="<?php echo base_url('assetsa/vendors/general/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css'); ?>" rel="stylesheet"/>
<link href="<?php echo base_url('assetsa/vendors/general/select2/dist/css/select2.css'); ?>" rel="stylesheet"/>
<link href="<?php echo base_url('assetsa/vendors/general/animate.css/animate.css'); ?>" rel="stylesheet"/>
<link href="<?php echo base_url('assetsa/vendors/general/sweetalert2/dist/sweetalert2.css'); ?>" rel="stylesheet"/>
<link href="<?php echo base_url('assetsa/vendors/general/toastr/build/toastr.css'); ?>" rel="stylesheet"/>
<link href="<?php echo base_url('assetsa/vendors/general/@fortawesome/fontawesome-free/css/all.min.css'); ?>" rel="stylesheet"/>
<link href="<?php echo base_url('assetsa/vendors/custom/vendors/line-awesome/css/line-awesome.css'); ?>" rel="stylesheet"/>
<link href="<?php echo base_url('assetsa/vendors/custom/vendors/flaticon/flaticon.css'); ?>" rel="stylesheet"/>
<link href="<?php echo base_url('assetsa/vendors/custom/vendors/flaticon2/flaticon.css'); ?>" rel="stylesheet"/>
<link href="<?php echo base_url('assetsa/css/demo1/style.bundle.css'); ?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url('assetsa/css/demo1/skins/header/base/light.css'); ?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url('assetsa/css/demo1/skins/header/menu/light.css'); ?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url('assetsa/css/demo1/skins/brand/dark.css'); ?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url('assetsa/css/demo1/skins/aside/dark.css'); ?>" rel="stylesheet" type="text/css"/>

<!-- Farmstaff CSS (badges, helpers) -->
<link rel="stylesheet" href="<?php echo base_url('assets/css/farmstaff.css'); ?>">

<style>
/* ── Farmstaff Employer: Black sidebar (matches admin) ── */
.kt-aside,
.kt-aside-menu,
.kt-aside .kt-aside__footer         { background-color: #111111 !important; }
.kt-aside__brand                    { background-color: #000000 !important; border-bottom: 1px solid rgba(255,255,255,.08) !important; }

/* Text & icons */
.kt-aside-menu .kt-menu__nav > .kt-menu__item > .kt-menu__link .kt-menu__link-text { color: #b5b5c3 !important; }
.kt-aside-menu .kt-menu__nav > .kt-menu__item > .kt-menu__link .kt-menu__link-icon { color: #6d6d80 !important; }
.kt-aside-menu .kt-menu__nav > .kt-menu__section .kt-menu__section-text            { color: #6d6d80 !important; }

/* Hover */
.kt-aside-menu .kt-menu__nav > .kt-menu__item:not(.kt-menu__item--open):hover > .kt-menu__link { background-color: rgba(255,255,255,.06) !important; }
.kt-aside-menu .kt-menu__nav > .kt-menu__item:not(.kt-menu__item--open):hover > .kt-menu__link .kt-menu__link-text { color: #ffffff !important; }
.kt-aside-menu .kt-menu__nav > .kt-menu__item:not(.kt-menu__item--open):hover > .kt-menu__link .kt-menu__link-icon { color: #ffffff !important; }

/* Active */
.kt-aside-menu .kt-menu__nav > .kt-menu__item.kt-menu__item--active > .kt-menu__link { background-color: rgba(255,255,255,.10) !important; }
.kt-aside-menu .kt-menu__nav > .kt-menu__item.kt-menu__item--active > .kt-menu__link .kt-menu__link-text { color: #ffffff !important; }
.kt-aside-menu .kt-menu__nav > .kt-menu__item.kt-menu__item--active > .kt-menu__link .kt-menu__link-icon { color: #ffffff !important; }

/* Mobile header */
#kt_header_mobile                   { background-color: #000000 !important; }

.kt-aside__brand-logo img { height: 38px; }
.kt-aside .ps > .ps__rail-y > .ps__thumb-y { background: #333 !important; }
</style>
</head>
<body class="kt-quick-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--transparent kt-aside--enabled kt-aside--fixed kt-page--loading">

<div class="kt-grid kt-grid--hor kt-grid--root">
<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">

<!-- ===== ASIDE / SIDEBAR ===== -->
<button class="kt-aside-close" id="kt_aside_close_btn"><i class="la la-close"></i></button>
<div class="kt-aside kt-aside--fixed kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop" id="kt_aside">

  <!-- Brand -->
  <div class="kt-aside__brand kt-grid__item" id="kt_aside_brand">
    <div class="kt-aside__brand-logo">
      <a href="<?php echo site_url('dashboard'); ?>">
        <img alt="<?php echo SITE_NAME; ?>" src="<?php echo base_url('assets/images/logo_white.png'); ?>"/>
      </a>
    </div>
    <div class="kt-aside__brand-tools">
      <button class="kt-aside__brand-aside-toggler" id="kt_aside_toggler">
        <span><i class="la la-angle-double-left"  style="color:#fff;font-size:18px;"></i></span>
        <span><i class="la la-angle-double-right" style="color:#fff;font-size:18px;"></i></span>
      </button>
    </div>
  </div>

  <!-- Menu -->
  <div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">
    <div id="kt_aside_menu" class="kt-aside-menu" data-ktmenu-vertical="1" data-ktmenu-scroll="1" data-ktmenu-dropdown-timeout="500">
      <ul class="kt-menu__nav">

        <!-- MAIN -->
        <li class="kt-menu__section">
          <h4 class="kt-menu__section-text">Main</h4>
          <i class="kt-menu__section-icon flaticon-more-v2"></i>
        </li>
        <li class="kt-menu__item <?php echo (isset($active_nav)&&$active_nav==='dashboard')?'kt-menu__item--active':''; ?>" aria-haspopup="true">
          <a href="<?php echo site_url('dashboard'); ?>" class="kt-menu__link">
            <span class="kt-menu__link-icon"><i class="flaticon2-architecture-and-city"></i></span>
            <span class="kt-menu__link-text">Dashboard</span>
          </a>
        </li>
        <li class="kt-menu__item <?php echo (isset($active_nav)&&$active_nav==='workers')?'kt-menu__item--active':''; ?>" aria-haspopup="true">
          <a href="<?php echo site_url('dashboard/workers'); ?>" class="kt-menu__link">
            <span class="kt-menu__link-icon"><i class="flaticon2-user"></i></span>
            <span class="kt-menu__link-text">My Workers</span>
          </a>
        </li>
        <li class="kt-menu__item" aria-haspopup="true">
          <a href="<?php echo site_url('dashboard/register-worker'); ?>" class="kt-menu__link">
            <span class="kt-menu__link-icon"><i class="flaticon2-add-1"></i></span>
            <span class="kt-menu__link-text">Register Worker</span>
          </a>
        </li>
        <li class="kt-menu__item <?php echo (isset($active_nav)&&$active_nav==='check')?'kt-menu__item--active':''; ?>" aria-haspopup="true">
          <a href="<?php echo site_url('dashboard/background-check'); ?>" class="kt-menu__link">
            <span class="kt-menu__link-icon"><i class="flaticon2-search-1"></i></span>
            <span class="kt-menu__link-text">Background Check</span>
          </a>
        </li>

        <!-- ACCOUNT -->
        <li class="kt-menu__section">
          <h4 class="kt-menu__section-text">Account</h4>
          <i class="kt-menu__section-icon flaticon-more-v2"></i>
        </li>
        <li class="kt-menu__item <?php echo (isset($active_nav)&&$active_nav==='notifications')?'kt-menu__item--active':''; ?>" aria-haspopup="true">
          <a href="<?php echo site_url('dashboard/notifications'); ?>" class="kt-menu__link">
            <span class="kt-menu__link-icon"><i class="flaticon2-bell-alarm-symbol"></i></span>
            <span class="kt-menu__link-text">Notifications</span>
            <?php if (!empty($unread_notifs) && $unread_notifs > 0): ?>
              <span class="kt-menu__link-badge">
                <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill"><?php echo $unread_notifs; ?></span>
              </span>
            <?php endif; ?>
          </a>
        </li>
        <li class="kt-menu__item <?php echo (isset($active_nav)&&$active_nav==='profile')?'kt-menu__item--active':''; ?>" aria-haspopup="true">
          <a href="<?php echo site_url('dashboard/profile'); ?>" class="kt-menu__link">
            <span class="kt-menu__link-icon"><i class="flaticon2-gear"></i></span>
            <span class="kt-menu__link-text">My Profile</span>
          </a>
        </li>

        <!-- QUICK LINKS -->
        <li class="kt-menu__section">
          <h4 class="kt-menu__section-text">Quick Links</h4>
          <i class="kt-menu__section-icon flaticon-more-v2"></i>
        </li>
        <li class="kt-menu__item" aria-haspopup="true">
          <a href="<?php echo site_url('/'); ?>" class="kt-menu__link" target="_blank">
            <span class="kt-menu__link-icon"><i class="flaticon2-world"></i></span>
            <span class="kt-menu__link-text">Public Site</span>
          </a>
        </li>
        <li class="kt-menu__item" aria-haspopup="true">
          <a href="<?php echo site_url('about'); ?>#contact" class="kt-menu__link">
            <span class="kt-menu__link-icon"><i class="flaticon2-help"></i></span>
            <span class="kt-menu__link-text">Help &amp; Support</span>
          </a>
        </li>

      </ul>
    </div>
  </div>
  <!-- end Menu -->

</div>
<!-- ===== END ASIDE ===== -->

<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">

  <!-- Mobile header -->
  <div id="kt_header_mobile" class="kt-header-mobile kt-header-mobile--fixed">
    <div class="kt-header-mobile__logo">
      <a href="<?php echo site_url('dashboard'); ?>">
        <img alt="Logo" src="<?php echo base_url('assets/images/logo_white.png'); ?>" style="height:34px;"/>
      </a>
    </div>
    <div class="kt-header-mobile__toolbar">
      <button class="kt-header-mobile__toggler kt-header-mobile__toggler--left" id="kt_aside_mobile_toggler"><span></span></button>
      <button class="kt-header-mobile__toggler" id="kt_header_mobile_toggler"><span></span></button>
      <button class="kt-header-mobile__topbar-toggler" id="kt_header_mobile_topbar_toggler"><i class="flaticon-more"></i></button>
    </div>
  </div>

  <!-- ===== HEADER ===== -->
  <div id="kt_header" class="kt-header kt-grid__item kt-header--fixed">

    <!-- Page title in header menu area -->
    <div class="kt-header-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_header_menu_wrapper">
      <div id="kt_header_menu" class="kt-header-menu kt-header-menu-mobile kt-header-menu--layout-default">
        <ul class="kt-menu__nav">
          <li class="kt-menu__item" aria-haspopup="true">
            <span class="kt-menu__link-text" style="font-weight:700;font-size:1rem;color:#333;">
              <?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Dashboard'; ?>
            </span>
          </li>
        </ul>
      </div>
    </div>

    <!-- Topbar -->
    <div class="kt-header__topbar">

      <!-- Register Worker shortcut -->
      <div class="kt-header__topbar-item">
        <div class="kt-header__topbar-wrapper">
          <a href="<?php echo site_url('dashboard/register-worker'); ?>"
             class="btn btn-success btn-sm btn-bold"
             style="border-radius:4px;">
            + Register Worker
          </a>
        </div>
      </div>

      <!-- Notification bell -->
      <?php if (!empty($unread_notifs) && $unread_notifs > 0): ?>
      <div class="kt-header__topbar-item">
        <div class="kt-header__topbar-wrapper">
          <a href="<?php echo site_url('dashboard/notifications'); ?>" style="position:relative;display:inline-block;">
            <span class="kt-header__topbar-icon kt-header__topbar-icon--warning">
              <i class="flaticon2-bell-alarm-symbol"></i>
            </span>
            <span class="kt-badge kt-badge--danger kt-badge--md kt-badge--rounded"
                  style="position:absolute;top:-4px;right:-4px;font-size:.65rem;">
              <?php echo $unread_notifs; ?>
            </span>
          </a>
        </div>
      </div>
      <?php endif; ?>

      <!-- User dropdown -->
      <?php
        $emp      = isset($employer) ? $employer : [];
        $ename    = $emp['farm_name']      ?? ($this->session->userdata('employer_name') ?? 'Employer');
        $einitial = strtoupper(substr($ename, 0, 1));
      ?>
      <div class="kt-header__topbar-item kt-header__topbar-item--user">
        <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">
          <div class="kt-header__topbar-user">
            <span class="kt-header__topbar-welcome kt-hidden-mobile">Hi,</span>
            <span class="kt-header__topbar-username kt-hidden-mobile"><?php echo htmlspecialchars($einitial === '' ? 'Employer' : substr($ename,0,12)); ?></span>
            <span class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold">
              <?php echo $einitial; ?>
            </span>
          </div>
        </div>
        <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl">
          <!-- Head card -->
          <div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x"
               style="background-image:url(<?php echo base_url('assetsa/media/misc/bg-1.jpg'); ?>)">
            <div class="kt-user-card__avatar">
              <?php if (!empty($emp['logo'])): ?>
                <img src="<?php echo base_url($emp['logo']); ?>" alt="Logo"
                     style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
              <?php else: ?>
                <span class="kt-badge kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-success">
                  <?php echo $einitial; ?>
                </span>
              <?php endif; ?>
            </div>
            <div class="kt-user-card__name"><?php echo htmlspecialchars($ename); ?></div>
            <div class="kt-user-card__badge">
              <span class="btn btn-success btn-sm btn-bold btn-font-md">Employer</span>
            </div>
          </div>
          <!-- Nav items -->
          <div class="kt-notification">
            <a href="<?php echo site_url('dashboard'); ?>" class="kt-notification__item">
              <div class="kt-notification__item-icon"><i class="flaticon2-architecture-and-city kt-font-success"></i></div>
              <div class="kt-notification__item-details">
                <div class="kt-notification__item-title kt-font-bold">Dashboard</div>
                <div class="kt-notification__item-time">Overview &amp; quick stats</div>
              </div>
            </a>
            <a href="<?php echo site_url('dashboard/profile'); ?>" class="kt-notification__item">
              <div class="kt-notification__item-icon"><i class="flaticon2-gear kt-font-success"></i></div>
              <div class="kt-notification__item-details">
                <div class="kt-notification__item-title kt-font-bold">Profile &amp; Settings</div>
                <div class="kt-notification__item-time">Update your farm details</div>
              </div>
            </a>
            <a href="<?php echo site_url('dashboard/background-check'); ?>" class="kt-notification__item">
              <div class="kt-notification__item-icon"><i class="flaticon2-search-1 kt-font-success"></i></div>
              <div class="kt-notification__item-details">
                <div class="kt-notification__item-title kt-font-bold">Background Check</div>
                <div class="kt-notification__item-time">Search worker records</div>
              </div>
            </a>
            <div class="kt-notification__custom kt-space-between">
              <a href="<?php echo site_url('logout'); ?>" class="btn btn-label btn-label-brand btn-sm btn-bold">Sign Out</a>
              <a href="<?php echo site_url('dashboard/profile'); ?>" class="btn btn-clean btn-sm btn-bold">My Profile</a>
            </div>
          </div>
        </div>
      </div>
      <!-- end User dropdown -->

    </div>
    <!-- end Topbar -->
  </div>
  <!-- ===== END HEADER ===== -->

  <!-- ===== CONTENT ===== -->
  <div class="kt-content kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
    <div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">

      <!-- Flash Messages -->
      <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin:20px 0 0;">
          <i class="la la-check-circle"></i> <?php echo $this->session->flashdata('success'); ?>
          <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
      <?php endif; ?>
      <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin:20px 0 0;">
          <i class="la la-exclamation-circle"></i> <?php echo $this->session->flashdata('error'); ?>
          <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
      <?php endif; ?>
      <?php if (isset($error) && $error): ?>
        <div class="alert alert-danger" style="margin:20px 0 0;">
          <i class="la la-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <div style="padding-top:25px;">
