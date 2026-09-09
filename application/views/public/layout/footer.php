</main>

<!-- ===== SITE FOOTER ===== -->
<footer class="site-footer">
  <div class="footer-grid">
    <div class="footer-brand">
      <img src="<?php echo base_url('assets/images/logo_white.png'); ?>" alt="<?php echo SITE_NAME; ?>">
      <p><?php echo OSACA_NAME; ?><br>
      A centralized platform to help agricultural employers register, verify and manage farm workers across Ondo State.</p>
      <p style="margin-top:10px;opacity:.7;font-size:.8rem;">📧 <?php echo SITE_EMAIL; ?><br>📞 <?php echo SITE_PHONE; ?></p>
    </div>
    <div class="footer-col">
      <h6>For Employers</h6>
      <a href="<?php echo site_url('register'); ?>">Register as Employer</a>
      <a href="<?php echo site_url('login'); ?>">Employer Login</a>
      <a href="<?php echo site_url('dashboard/register-worker'); ?>">Register a Worker</a>
      <a href="<?php echo site_url('dashboard/background-check'); ?>">Background Check</a>
    </div>
    <div class="footer-col">
      <h6>Platform</h6>
      <a href="<?php echo site_url('for-transparency'); ?>">For Transparency</a>
      <a href="<?php echo site_url('about'); ?>">About Us</a>
      <a href="<?php echo site_url('resources'); ?>">Resources</a>
      <a href="<?php echo site_url('about'); ?>#faq">FAQs</a>
    </div>
    <div class="footer-col">
      <h6>Administration</h6>
      <a href="<?php echo site_url('admin'); ?>">Admin Portal</a>
      <a href="<?php echo site_url('about'); ?>#contact">Contact Support</a>
      <a href="<?php echo site_url('about'); ?>#privacy">Privacy Policy</a>
      <a href="<?php echo site_url('about'); ?>#terms">Terms of Use</a>
    </div>
  </div>
  <div class="footer-bottom">
    <span>© <?php echo date('Y'); ?> <?php echo SITE_NAME; ?> — <?php echo OSACA_NAME; ?>. All rights reserved.</span>
    <span>Built with trust &amp; transparency for Ondo State farmers.</span>
  </div>
</footer>

<script src="<?php echo base_url('assets/js/farmstaff.js'); ?>"></script>
</body>
</html>
