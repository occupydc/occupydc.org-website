<?php
/**
 * Declares a sidebar widget with an action form
 * 
 * @author Andrew Marcus
 */
class NSJalapenoActionWidget extends WP_Widget {
	function NSJalapenoActionWidget() {
		parent::WP_Widget(false, $name = 'Jalape&ntilde;o Action', array(
		  'description' => 'A Salsa action',
		));
	}
	
	function form($instance) {
		$action_KEY = esc_attr($instance['action_KEY']);
		$title = esc_attr($instance['title']);
		 
		$actions = the_jalapeno_actions();
        ?>
          <p>
            <label for="<?php echo $this->get_field_id('action_KEY'); ?>"><?php _e('Action Key:'); ?></label>
            <select id="<?php echo $this->get_field_id('action_KEY'); ?>"  name="<?php echo $this->get_field_name('action_KEY'); ?>">
              <option value="">- Select an action -</option>
            <?php foreach ($actions as $key => $action) { ?>
              <?php $name = $action['name']; ?>
              <?php $selected = ($key == $action_KEY ? 'selected="selected"': ''); ?>
              <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $key?> - <?php echo $name; ?></option>
            <?php } ?>
            </select>
          </p>
          <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
          </p>
        <?php
	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$action_KEY = (int)($new_instance['action_KEY']);
		$instance['action_KEY'] = $action_KEY;
		$instance['title'] = strip_tags($new_instance['title']);
		
		// Use the default title if it was not overridden
		if (empty($instance['title'])) {
			$action = the_jalapeno_actions($action_KEY);
			if (!empty($action['name'])) {
				$instance['title'] = $action['name'];
			}
		}
        return $instance;
	}
	
	function widget($args, $instance) {
		extract( $args );
        $action_KEY = $instance['action_KEY'];
        $title = apply_filters('widget_title', $instance['title']);
        
        echo $before_widget;
        if ( !empty($title) ) {
        	echo $before_title . $title . $after_title;
        }
		the_jalapeno_action($action_KEY);
		echo $after_widget;
	}
}

/**
 * Declares a sidebar widget with an action form
 * 
 * @author Andrew Marcus
 */
class NSJalapenoActionSignersWidget extends WP_Widget {
	function NSJalapenoActionSignersWidget() {
		parent::WP_Widget(false, $name = 'Jalape&ntilde;o Action Signatures', array(
		  'description' => 'The most recent Salsa signatures',
		));
	}
	
	function form($instance) {
		$action_KEY = esc_attr($instance['action_KEY']);
		$title = esc_attr($instance['title']);
		$number = esc_attr($instance['number']);
		if (empty($number)) {
			$number = 10;
		}
		 
		$actions = the_jalapeno_actions();
        ?>
          <p>
            <label for="<?php echo $this->get_field_id('action_KEY'); ?>"><?php _e('Action Key:'); ?></label>
            <select id="<?php echo $this->get_field_id('action_KEY'); ?>"  name="<?php echo $this->get_field_name('action_KEY'); ?>">
              <option value="">- Select an action -</option>
            <?php foreach ($actions as $key => $action) { ?>
              <?php $name = $action['name']; ?>
              <?php $selected = ($key == $action_KEY ? 'selected="selected"': ''); ?>
              <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $key?> - <?php echo $name; ?></option>
            <?php } ?>
            </select>
          </p>
          <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
          </p>
          <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Number to show:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" />
          </p>
        <?php
	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$action_KEY = (int)($new_instance['action_KEY']);
		$instance['action_KEY'] = $action_KEY;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int)strip_tags($new_instance['number']);
		
		// Use the default title if it was not overridden
		if (empty($instance['title'])) {
			$action = the_jalapeno_actions($action_KEY);
			if (!empty($action['name'])) {
				$instance['title'] = $action['name'];
			}
		}
        return $instance;
	}
	
	function widget($args, $instance) {
		extract( $args );
        $action_KEY = $instance['action_KEY'];
        $title = apply_filters('widget_title', $instance['title']);
        $number = $instance['number'];
        
        echo $before_widget;
        if ( !empty($title) ) {
        	echo $before_title . $title . $after_title;
        }
		the_jalapeno_action_signatures($action_KEY, $number);
		echo $after_widget;
	}
}