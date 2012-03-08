<h4><?php _e('Edit a form layout'); ?></h4>
<div class="select-form-box form-selected">
  <select name="dia_formid" id="dia_formid" onchange="diacontext.loadForm({'url':'<?php echo $this->getAjaxUrl(); ?>','object':this});">
<?php echo $this->loadOptions(); ?>
  </select>
</div>
