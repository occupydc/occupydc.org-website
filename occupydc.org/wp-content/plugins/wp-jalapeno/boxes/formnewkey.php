<h4><?php _e('Add a Salsa action form'); ?></h4>
<p>
  <label for="newformkey"><?php _e('Action key'); ?></label> 
  <input type="text"  name="newformkey" id="newformkey"> <input type="submit" class="button-primary" value="<?php _e('Add'); ?>" name="save_form" onclick="diacontext.addNewActionForm({'url':'<?php echo $this->getAjaxUrl();  ?>'});" />
</p>