      </div><!-- /padding wrapper -->
    </div><!-- /kt-container -->
  </div><!-- /kt-content -->

  <!-- Footer -->
  <div class="kt-footer kt-grid__item kt-grid kt-grid--desktop kt-grid--ver-desktop" id="kt_footer">
    <div class="kt-container kt-container--fluid">
      <div class="kt-footer__copyright">
        <?php echo date('Y'); ?> &nbsp;&copy;&nbsp;
        <a href="<?php echo site_url('/'); ?>" target="_blank" class="kt-link"><?php echo SITE_NAME; ?></a>
        &nbsp;—&nbsp; <?php echo OSACA_NAME; ?>
      </div>
      <div class="kt-footer__menu">
        <a href="<?php echo site_url('/'); ?>"          target="_blank" class="kt-footer__menu-link kt-link">Public Site</a>
        <a href="<?php echo site_url('about'); ?>#contact"               class="kt-footer__menu-link kt-link">Support</a>
        <a href="<?php echo site_url('for-transparency'); ?>"            class="kt-footer__menu-link kt-link">Transparency</a>
      </div>
    </div>
  </div>

</div><!-- /kt-wrapper -->
</div><!-- /kt-page -->
</div><!-- /kt-root -->

<!-- KT Global Config -->
<script>
var KTAppOptions = {
  "colors": {
    "state": {
      "brand":   "#5d78ff", "dark": "#282a3c", "light": "#ffffff",
      "primary": "#5867dd", "success": "#34bfa3", "info": "#36a3f7",
      "warning": "#ffb822", "danger":  "#fd3995"
    },
    "base": {
      "label": ["#c5cbe3","#a1a8c3","#3d4465","#3e4466"],
      "shape": ["#f0f3ff","#d9dffa","#afb4d4","#646c9a"]
    }
  }
};
</script>

<!-- KT Mandatory Vendors -->
<script src="<?php echo base_url('assetsa/vendors/general/jquery/dist/jquery.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/popper.js/dist/umd/popper.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/bootstrap/dist/js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/js-cookie/src/js.cookie.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/moment/min/moment.min.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/tooltip.js/dist/umd/tooltip.min.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/perfect-scrollbar/dist/perfect-scrollbar.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/sticky-js/dist/sticky.min.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/wnumb/wNumb.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/select2/dist/js/select2.full.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/sweetalert2/dist/sweetalert2.min.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/waypoints/lib/jquery.waypoints.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/counterup/jquery.counterup.js'); ?>"></script>
<script src="<?php echo base_url('assetsa/vendors/general/chart.js/dist/Chart.bundle.js'); ?>"></script>

<!-- KT Theme Bundle -->
<script src="<?php echo base_url('assetsa/js/demo1/scripts.bundle.js'); ?>"></script>

<!-- Farmstaff JS -->
<script src="<?php echo base_url('assets/js/farmstaff.js'); ?>"></script>

<script>
$(function(){
  // Auto-init select2 on dropdowns with 6+ options
  $('select.form-control').not('.no-select2').each(function(){
    if($(this).find('option').length >= 6){
      $(this).select2({ width:'100%', allowClear: !$(this).prop('required') });
    }
  });
  // Auto-dismiss flash alerts
  setTimeout(function(){
    $('.alert.alert-success, .alert.alert-danger').fadeOut(500);
  }, 5000);
  // Confirm dialogs
  $('[data-confirm]').on('click', function(e){
    if(!confirm($(this).data('confirm'))) e.preventDefault();
  });
  // Photo preview
  $('.photo-input').on('change', function(){
    var previewId = $(this).data('preview');
    var reader = new FileReader();
    var $el = $('#' + previewId);
    reader.onload = function(e){
      if($el.is('img')) $el.attr('src', e.target.result);
      else $el.css('background-image', 'url(' + e.target.result + ')');
    };
    if(this.files && this.files[0]) reader.readAsDataURL(this.files[0]);
  });
  // Skills tag input
  var skillInput  = document.getElementById('skill-input');
  var skillTags   = document.getElementById('skill-tags');
  if(skillInput && skillTags){
    var skills = [];
    function renderTags(){
      skillTags.innerHTML = '';
      skills.forEach(function(s, i){
        var tag = document.createElement('span');
        tag.className = 'kt-badge kt-badge--success kt-badge--inline kt-badge--pill';
        tag.style.margin = '3px';
        tag.innerHTML = s + ' <a href="#" data-i="'+i+'" style="color:inherit;margin-left:4px;font-weight:700;">&times;</a>';
        tag.querySelector('a').addEventListener('click', function(e){
          e.preventDefault();
          skills.splice(parseInt(this.dataset.i),1);
          renderTags(); updateHidden();
        });
        skillTags.appendChild(tag);
      });
    }
    function updateHidden(){
      document.querySelectorAll('input[name="skills[]"]').forEach(function(el){ el.remove(); });
      skills.forEach(function(s){
        var inp = document.createElement('input');
        inp.type='hidden'; inp.name='skills[]'; inp.value=s;
        skillInput.closest('form').appendChild(inp);
      });
    }
    skillInput.addEventListener('keydown', function(e){
      if((e.key==='Enter'||e.key===',') && this.value.trim()){
        e.preventDefault();
        var v = this.value.trim().replace(/,/g,'');
        if(v && !skills.includes(v)){ skills.push(v); renderTags(); updateHidden(); }
        this.value = '';
      }
    });
    var addBtn = document.getElementById('add-skill-btn');
    if(addBtn) addBtn.addEventListener('click', function(){
      var v = skillInput.value.trim();
      if(v && !skills.includes(v)){ skills.push(v); renderTags(); updateHidden(); skillInput.value=''; }
    });
  }
});
</script>

</body>
</html>
