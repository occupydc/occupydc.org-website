<form id="jalapeno-shortcode-insert">
  <h3 class="media-title">Insert Salsa Action</h3>

  <label for="action-key"><?php _e('Action Key:'); ?></label>
  <select id="action-key"  name="action-key">
    <option value="">- Select an action -</option>
  <?php $actions = $this->getForms(); ?>
  <?php foreach ($actions as $key => $action) { ?>
    <?php $name = $action['name']; ?>
    <option value="<?php echo $key; ?>"><?php echo $key?> - <?php echo $name; ?></option>
  <?php } ?>
  </select>
  
  <input type="submit" value="Insert" name="insert" id="insert" class="button savebutton" />
  
  <a href="#" id="cancel">Cancel</a>
<script>
// get main window
w = window.dialogArguments || opener || parent || top;

jQuery('#jalapeno-shortcode-insert').submit(function(event){
  
  // insert
  var v = jQuery('#action-key').val();
  if( v ){
    // insert
    w.send_to_editor('<?php echo NSJALAPENO_ACTION_SHORT_TAG; ?>'+v+']');
  }
  
  event.stopPropagation();
  return false;
});

jQuery('#cancel').click(function(event){
  w.tb_remove();
  
  event.stopPropagation();
  return false;
});


</script>
</form>
<style>
body, html {
  height: auto !important;
} 
</style>