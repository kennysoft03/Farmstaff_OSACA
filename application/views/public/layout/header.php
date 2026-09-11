<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- ===== Primary SEO ===== -->
<title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' — ' : ''; echo SITE_NAME; ?> | <?php echo SITE_TAGLINE; ?></title>
<meta name="description" content="Farm Staff Registry — the trusted platform for agricultural employers and farm workers in Ondo State, Nigeria. Register workers, verify skills, track attendance, run background checks and build safer, more accountable farm workplaces.">
<meta name="keywords" content="farm staff registry, farm workers Ondo State, agricultural employment Nigeria, worker background check, farm worker verification, hire farm workers, Ondo farm labour, agric workforce management">
<meta name="author" content="Farm Staff Registry — Ondo State, Nigeria">
<meta name="robots" content="index, follow">
<meta name="theme-color" content="#1a5c2a">
<link rel="canonical" href="<?php echo current_url(); ?>">

<!-- ===== Open Graph (Facebook / WhatsApp) ===== -->
<meta property="og:type"        content="website">
<meta property="og:url"         content="<?php echo current_url(); ?>">
<meta property="og:site_name"   content="<?php echo SITE_NAME; ?>">
<meta property="og:title"       content="<?php echo isset($page_title) ? htmlspecialchars($page_title).' — '.SITE_NAME : SITE_NAME; ?>">
<meta property="og:description" content="The trusted Farm Staff Registry for Ondo State — register, verify and manage agricultural workers with confidence.">
<meta property="og:image"       content="<?php echo base_url('assets/images/logo.png'); ?>">
<meta property="og:locale"      content="en_NG">

<!-- ===== Twitter Card ===== -->
<meta name="twitter:card"        content="summary">
<meta name="twitter:title"       content="<?php echo isset($page_title) ? htmlspecialchars($page_title).' — '.SITE_NAME : SITE_NAME; ?>">
<meta name="twitter:description" content="The trusted Farm Staff Registry for Ondo State — register, verify and manage agricultural workers with confidence.">
<meta name="twitter:image"       content="<?php echo base_url('assets/images/logo.png'); ?>">

<!-- ===== Geo / Local SEO ===== -->
<meta name="geo.region"       content="NG-ON">
<meta name="geo.placename"    content="Ondo State, Nigeria">
<meta name="language"         content="English">
<meta name="coverage"         content="Ondo State, Nigeria">
<meta name="category"         content="Agriculture, Employment, Registry">

<!-- ===== Favicon ===== -->
<link rel="icon"       type="image/png" sizes="32x32" href="<?php echo base_url('assets/images/farvicon.png'); ?>">
<link rel="shortcut icon"              href="<?php echo base_url('assets/images/farvicon.png'); ?>">
<link rel="apple-touch-icon"           href="<?php echo base_url('assets/images/farvicon.png'); ?>">

<!-- ===== Fonts & Styles ===== -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo base_url('assets/css/farmstaff.css'); ?>">
</head>
<body>

<!-- ===== NAVBAR ===== -->
<header class="fs-navbar">
  <div class="container">
    <a href="<?php echo site_url('/'); ?>" class="brand">
      <img src="<?php echo base_url('assets/images/logo.png'); ?>" alt="<?php echo SITE_NAME; ?>">
    </a>

    <button class="nav-toggle" aria-label="Toggle navigation">
      <span></span><span></span><span></span>
    </button>

    <nav>
      <a href="<?php echo site_url('/'); ?>"        class="<?php echo (isset($active_nav) && $active_nav==='home') ? 'active' : ''; ?>">Home</a>

      <div class="nav-dropdown">
        <button class="dropdown-toggle">For Employers ▾</button>
        <div class="nav-dropdown-menu">
          <a href="<?php echo site_url('register'); ?>">Register as Employer</a>
          <a href="<?php echo site_url('login'); ?>">Employer Login</a>
          <a href="<?php echo site_url('dashboard'); ?>">My Dashboard</a>
          <a href="<?php echo site_url('dashboard/background-check'); ?>">Background Check</a>
        </div>
      </div>

      <a href="<?php echo site_url('for-transparency'); ?>" class="<?php echo (isset($active_nav) && $active_nav==='transparency') ? 'active' : ''; ?>">For Transparency</a>
      <a href="<?php echo site_url('about'); ?>"            class="<?php echo (isset($active_nav) && $active_nav==='about') ? 'active' : ''; ?>">About Us</a>

      <div class="nav-dropdown">
        <button class="dropdown-toggle">Resources ▾</button>
        <div class="nav-dropdown-menu">
          <a href="<?php echo site_url('resources'); ?>">Help &amp; Guides</a>
          <a href="<?php echo site_url('about'); ?>#faq">FAQs</a>
          <a href="<?php echo site_url('about'); ?>#contact">Contact Us</a>
        </div>
      </div>
    </nav>

    <div class="nav-actions">
      <?php if ($this->session->userdata('employer_id')): ?>
        <a href="<?php echo site_url('dashboard'); ?>" class="btn-login">Dashboard</a>
        <a href="<?php echo site_url('logout'); ?>" class="btn-register-nav">Logout</a>
      <?php else: ?>
        <a href="<?php echo site_url('login'); ?>"    class="btn-login">Login</a>
        <a href="<?php echo site_url('register'); ?>" class="btn-register-nav">Register Now</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<!-- Flash messages -->
<?php if ($this->session->flashdata('success')): ?>
  <div class="alert alert-success auto-dismiss" style="margin:0;border-radius:0;border-left:none;border-right:none;">
    ✅ <?php echo $this->session->flashdata('success'); ?>
  </div>
<?php endif; ?>
<?php if ($this->session->flashdata('error')): ?>
  <div class="alert alert-danger auto-dismiss" style="margin:0;border-radius:0;border-left:none;border-right:none;">
    ❌ <?php echo $this->session->flashdata('error'); ?>
  </div>
<?php endif; ?>


<main>
