<form method="get" id="searchform" action="<?php bloginfo('url'); ?>/">
	<input type="text" class="field" name="s" id="s" value="<?php _e('Enter keywords...', 'cp') ?>" onfocus="if (this.value == '<?php _e('Enter keywords...', 'cp') ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('Enter keywords...', 'cp') ?>';}" />
	<input type="hidden" name="submit" value="" />
</form>