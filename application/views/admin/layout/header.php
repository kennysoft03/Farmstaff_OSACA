<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($page_title) ? htmlspecialchars($page_title).' — ' : ''; ?>Admin | <?php echo SITE_NAME; ?></title>
<meta name="robots" content="noindex, nofollow">
<meta name="theme-color" content="#1a2640">
<link rel="icon"       type="image/png" sizes="32x32" href="<?php echo base_url('assetsa/media/logos/farvicon.png'); ?>">
<link rel="shortcut icon"              href="<?php echo base_url('assetsa/media/logos/farvicon.png'); ?>">
<link rel="apple-touch-icon"           href="<?php echo base_url('assetsa/media/logos/farvicon.png'); ?>">

<!-- KT Template CSS (from assetsa) -->
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

<!-- Farmstaff public CSS (for badge/util helpers only) -->
<link rel="stylesheet" href="<?php echo base_url('assets/css/farmstaff.css'); ?>">

<style>
/* ── Farmstaff Admin: Black sidebar ── */
.kt-aside,
.kt-aside-menu,
.kt-aside .kt-aside__footer,
.kt-aside__brand                          { background-color: #111111 !important; }

/* Brand area top strip */
.kt-aside__brand                          { background-color: #000000 !important; border-bottom: 1px solid rgba(255,255,255,.08) !important; }

/* Menu item text & icons */
.kt-aside-menu .kt-menu__nav > .kt-menu__item > .kt-menu__link .kt-menu__link-text    { color: #b5b5c3 !important; }
.kt-aside-menu .kt-menu__nav > .kt-menu__item > .kt-menu__link .kt-menu__link-icon    { color: #6d6d80 !important; }

/* Section labels */
.kt-aside-menu .kt-menu__nav > .kt-menu__section .kt-menu__section-text              { color: #6d6d80 !important; }

/* Hover state */
.kt-aside-menu .kt-menu__nav > .kt-menu__item:not(.kt-menu__item--open):hover > .kt-menu__link,
.kt-aside-menu .kt-menu__nav > .kt-menu__item.kt-menu__item--here > .kt-menu__link   { background-color: rgba(255,255,255,.06) !important; }
.kt-aside-menu .kt-menu__nav > .kt-menu__item:not(.kt-menu__item--open):hover > .kt-menu__link .kt-menu__link-text { color: #ffffff !important; }
.kt-aside-menu .kt-menu__nav > .kt-menu__item:not(.kt-menu__item--open):hover > .kt-menu__link .kt-menu__link-icon { color: #ffffff !important; }

/* Active state */
.kt-aside-menu .kt-menu__nav > .kt-menu__item.kt-menu__item--active > .kt-menu__link { background-color: rgba(255,255,255,.10) !important; }
.kt-aside-menu .kt-menu__nav > .kt-menu__item.kt-menu__item--active > .kt-menu__link .kt-menu__link-text { color: #ffffff !important; }
.kt-aside-menu .kt-menu__nav > .kt-menu__item.kt-menu__item--active > .kt-menu__link .kt-menu__link-icon { color: #ffffff !important; }

/* Mobile header bar */
#kt_header_mobile                         { background-color: #000000 !important; }

/* Toggle button strips */
.kt-aside__brand-aside-toggler span svg g [fill] { fill: #ffffff !important; }

/* Scrollbar */
.kt-aside .ps > .ps__rail-y > .ps__thumb-y { background: #333333 !important; }

/* Misc */
.kt-aside__brand-logo img { height: 38px; }
</style>
</head>
<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--transparent kt-aside--enabled kt-aside--fixed kt-page--loading">

<!-- begin:: Page -->
<div class="kt-grid kt-grid--hor kt-grid--root">
<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">

<!-- begin:: Aside -->
<button class="kt-aside-close" id="kt_aside_close_btn"><i class="la la-close"></i></button>
<div class="kt-aside kt-aside--fixed kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop" id="kt_aside">

  <!-- begin:: Aside Brand -->
  <div class="kt-aside__brand kt-grid__item" id="kt_aside_brand">
    <div class="kt-aside__brand-logo">
      <a href="<?php echo site_url('admin/dashboard'); ?>">
        <img alt="Farm Staff Registry" src="<?php echo base_url('assets/images/logo_white.png'); ?>"/>
      </a>
    </div>
    <div class="kt-aside__brand-tools">
      <button class="kt-aside__brand-aside-toggler" id="kt_aside_toggler">
        <span><i class="la la-angle-double-left" style="color:#fff;font-size:18px;"></i></span>
        <span><i class="la la-angle-double-right" style="color:#fff;font-size:18px;"></i></span>
      </button>
    </div>
  </div>
  <!-- end:: Aside Brand -->

  <!-- begin:: Aside Menu -->
  <div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">
    <div id="kt_aside_menu" class="kt-aside-menu" data-ktmenu-vertical="1" data-ktmenu-scroll="1" data-ktmenu-dropdown-timeout="500">
      <ul class="kt-menu__nav">

        <!-- MAIN -->
        <li class="kt-menu__section">
          <h4 class="kt-menu__section-text">Main</h4>
          <i class="kt-menu__section-icon flaticon-more-v2"></i>
        </li>
        <li class="kt-menu__item <?php echo (isset($active_nav) && $active_nav==='dashboard') ? 'kt-menu__item--active' : ''; ?>" aria-haspopup="true">
          <a href="<?php echo site_url('admin/dashboard'); ?>" class="kt-menu__link">
            <span class="kt-menu__link-icon"><i class="kt-menu__link-icon flaticon2-architecture-and-city"></i></span>
            <span class="kt-menu__link-text">Dashboard</span>
          </a>
        </li>

        <!-- REGISTRY -->
        <li class="kt-menu__section">
          <h4 class="kt-menu__section-text">Registry</h4>
          <i class="kt-menu__section-icon flaticon-more-v2"></i>
        </li>
        <li class="kt-menu__item <?php echo (isset($active_nav) && $active_nav==='workers') ? 'kt-menu__item--active' : ''; ?>" aria-haspopup="true">
          <a href="<?php echo site_url('admin/workers'); ?>" class="kt-menu__link">
            <span class="kt-menu__link-icon"><i class="kt-menu__link-icon flaticon2-user"></i></span>
            <span class="kt-menu__link-text">Workers</span>
          </a>
        </li>
        <li class="kt-menu__item <?php echo (isset($active_nav) && $active_nav==='employers') ? 'kt-menu__item--active' : ''; ?>" aria-haspopup="true">
          <a href="<?php echo site_url('admin/employers'); ?>" class="kt-menu__link">
            <span class="kt-menu__link-icon"><i class="kt-menu__link-icon flaticon2-group"></i></span>
            <span class="kt-menu__link-text">Employers</span>
          </a>
        </li>

        <!-- MODERATION -->
        <li class="kt-menu__section">
          <h4 class="kt-menu__section-text">Moderation</h4>
          <i class="kt-menu__section-icon flaticon-more-v2"></i>
        </li>
        <li class="kt-menu__item <?php echo (isset($active_nav) && $active_nav==='incidents') ? 'kt-menu__item--active' : ''; ?>" aria-haspopup="true">
          <a href="<?php echo site_url('admin/incidents'); ?>" class="kt-menu__link">
            <span class="kt-menu__link-icon"><i class="kt-menu__link-icon flaticon-warning-sign"></i></span>
            <span class="kt-menu__link-text">Incidents</span>
            <?php if (!empty($stats['pending_incidents']) && $stats['pending_incidents'] > 0): ?>
              <span class="kt-menu__link-badge"><span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill"><?php echo $stats['pending_incidents']; ?></span></span>
            <?php endif; ?>
          </a>
        </li>
        <li class="kt-menu__item <?php echo (isset($active_nav) && $active_nav==='ratings') ? 'kt-menu__item--active' : ''; ?>" aria-haspopup="true">
          <a href="<?php echo site_url('admin/farm-ratings'); ?>" class="kt-menu__link">
            <span class="kt-menu__link-icon"><i class="kt-menu__link-icon flaticon-star"></i></span>
            <span class="kt-menu__link-text">Farm Ratings</span>
            <?php if (!empty($stats['pending_ratings']) && $stats['pending_ratings'] > 0): ?>
              <span class="kt-menu__link-badge"><span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill"><?php echo $stats['pending_ratings']; ?></span></span>
            <?php endif; ?>
          </a>
        </li>
        <li class="kt-menu__item" aria-haspopup="true">
          <a href="<?php echo site_url('admin/trust-scores'); ?>" class="kt-menu__link">
            <span class="kt-menu__link-icon"><i class="kt-menu__link-icon flaticon2-shield"></i></span>
            <span class="kt-menu__link-text">Trust Scores</span>
          </a>
        </li>

        <!-- INTELLIGENCE -->
        <li class="kt-menu__section">
          <h4 class="kt-menu__section-text">Intelligence</h4>
          <i class="kt-menu__section-icon flaticon-more-v2"></i>
        </li>
        <li class="kt-menu__item <?php echo (isset($active_nav) && $active_nav==='reports') ? 'kt-menu__item--active' : ''; ?>" aria-haspopup="true">
          <a href="<?php echo site_url('admin/reports'); ?>" class="kt-menu__link">
            <span class="kt-menu__link-icon"><i class="kt-menu__link-icon flaticon2-chart2"></i></span>
            <span class="kt-menu__link-text">Reports</span>
          </a>
        </li>
        <li class="kt-menu__item <?php echo (isset($active_nav) && $active_nav==='audit') ? 'kt-menu__item--active' : ''; ?>" aria-haspopup="true">
          <a href="<?php echo site_url('admin/audit'); ?>" class="kt-menu__link">
            <span class="kt-menu__link-icon"><i class="kt-menu__link-icon flaticon2-search-1"></i></span>
            <span class="kt-menu__link-text">Audit Trail</span>
          </a>
        </li>

        <!-- SYSTEM -->
        <li class="kt-menu__section">
          <h4 class="kt-menu__section-text">System</h4>
          <i class="kt-menu__section-icon flaticon-more-v2"></i>
        </li>
        <li class="kt-menu__item <?php echo (isset($active_nav) && $active_nav==='settings') ? 'kt-menu__item--active' : ''; ?>" aria-haspopup="true">
          <a href="<?php echo site_url('admin/settings'); ?>" class="kt-menu__link">
            <span class="kt-menu__link-icon"><i class="kt-menu__link-icon flaticon2-gear"></i></span>
            <span class="kt-menu__link-text">Settings</span>
          </a>
        </li>
        <li class="kt-menu__item" aria-haspopup="true">
          <a href="<?php echo site_url('/'); ?>" class="kt-menu__link" target="_blank">
            <span class="kt-menu__link-icon"><i class="kt-menu__link-icon flaticon2-world"></i></span>
            <span class="kt-menu__link-text">Public Site</span>
          </a>
        </li>

      </ul>
    </div>
  </div>
  <!-- end:: Aside Menu -->

</div>
<!-- end:: Aside -->

<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">

  <!-- begin:: Header Mobile -->
  <div id="kt_header_mobile" class="kt-header-mobile kt-header-mobile--fixed">
    <div class="kt-header-mobile__logo">
      <a href="<?php echo site_url('admin/dashboard'); ?>">
        <img alt="Logo" src="<?php echo base_url('assets/images/logo_white.png'); ?>" style="height:34px;"/>
      </a>
    </div>
    <div class="kt-header-mobile__toolbar">
      <button class="kt-header-mobile__toggler kt-header-mobile__toggler--left" id="kt_aside_mobile_toggler"><span></span></button>
      <button class="kt-header-mobile__toggler" id="kt_header_mobile_toggler"><span></span></button>
      <button class="kt-header-mobile__topbar-toggler" id="kt_header_mobile_topbar_toggler"><i class="flaticon-more"></i></button>
    </div>
  </div>
  <!-- end:: Header Mobile -->

  <!-- begin:: Header -->
  <div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed">
    <!-- begin:: Header Menu -->
    <div class="kt-header-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_header_menu_wrapper">
      <div id="kt_header_menu" class="kt-header-menu kt-header-menu-mobile kt-header-menu--layout-default">
        <ul class="kt-menu__nav">
          <li class="kt-menu__item" aria-haspopup="true">
            <span class="kt-menu__link-text" style="font-weight:700;font-size:1rem;color:#333;">
              <?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Admin Panel'; ?>
            </span>
          </li>
        </ul>
      </div>
    </div>
    <!-- end:: Header Menu -->

    <!-- begin:: Header Topbar -->
    <div class="kt-header__topbar">

      <!-- Notification bell -->
      <?php if (!empty($unread_notifs) && $unread_notifs > 0): ?>
      <div class="kt-header__topbar-item">
        <div class="kt-header__topbar-wrapper">
          <span class="kt-header__topbar-icon kt-header__topbar-icon--warning">
            <i class="flaticon2-bell-alarm-symbol"></i>
            <span class="kt-badge kt-badge--warning kt-badge--lg kt-badge--rounded" style="position:absolute;top:-2px;right:-2px;font-size:.65rem;">
              <?php echo $unread_notifs; ?>
            </span>
          </span>
        </div>
      </div>
      <?php endif; ?>

      <!-- User menu -->
      <div class="kt-header__topbar-item kt-header__topbar-item--user">
        <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">
          <div class="kt-header__topbar-user">
            <span class="kt-header__topbar-welcome kt-hidden-mobile">Hi,</span>
            <span class="kt-header__topbar-username kt-hidden-mobile">
              <?php echo isset($admin) ? htmlspecialchars($admin['surname']) : 'Admin'; ?>
            </span>
            <span class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold">
              <?php echo isset($admin) ? strtoupper(substr($admin['surname'],0,1)) : 'A'; ?>
            </span>
          </div>
        </div>
        <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl">
          <div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x"
               style="background-image:url(<?php echo base_url('assetsa/media/misc/bg-1.jpg'); ?>)">
            <div class="kt-user-card__avatar">
              <span class="kt-badge kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-success">
                <?php echo isset($admin) ? strtoupper(substr($admin['surname'],0,1)) : 'A'; ?>
              </span>
            </div>
            <div class="kt-user-card__name">
              <?php echo isset($admin) ? htmlspecialchars($admin['surname'].' '.$admin['othernames']) : 'Administrator'; ?>
            </div>
            <div class="kt-user-card__badge">
              <span class="btn btn-success btn-sm btn-bold btn-font-md">
                <?php echo isset($admin) ? ucfirst($admin['role']) : 'Admin'; ?>
              </span>
            </div>
          </div>
          <div class="kt-notification">
            <a href="<?php echo site_url('admin/settings'); ?>" class="kt-notification__item">
              <div class="kt-notification__item-icon">
                <i class="flaticon2-gear kt-font-success"></i>
              </div>
              <div class="kt-notification__item-details">
                <div class="kt-notification__item-title kt-font-bold">Settings</div>
                <div class="kt-notification__item-time">Admin settings &amp; users</div>
              </div>
            </a>
            <a href="<?php echo site_url('/'); ?>" class="kt-notification__item" target="_blank">
              <div class="kt-notification__item-icon">
                <i class="flaticon2-world kt-font-success"></i>
              </div>
              <div class="kt-notification__item-details">
                <div class="kt-notification__item-title kt-font-bold">Public Site</div>
                <div class="kt-notification__item-time">View the live website</div>
              </div>
            </a>
            <div class="kt-notification__custom kt-space-between">
              <a href="<?php echo site_url('admin/logout'); ?>" class="btn btn-label btn-label-brand btn-sm btn-bold">Sign Out</a>
            </div>
          </div>
        </div>
      </div>
      <!-- end: User menu -->

    </div>
    <!-- end:: Header Topbar -->
  </div>
  <!-- end:: Header -->

  <!-- begin:: Content -->
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
