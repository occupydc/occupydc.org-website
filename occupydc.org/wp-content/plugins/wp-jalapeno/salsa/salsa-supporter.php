<?php
class SalsaSupporterCustom extends SalsaObject {
    public $type;
    public $name;
    public $label;
    public $Display_Name;
    public $options = array();
	
    public function __construct($obj) {
    	  parent::__construct($obj);
    	  
        // See if there are any enum values
        $conn = SalsaConnector::instance();
        $xml = $conn->getObjects('custom_column_option', "custom_column_KEY=$this->key");
        
        if (isset($xml->custom_column_option->item)) {
		        foreach ($xml->custom_column_option->item as $option) {
		            $value = (string)$option->value;
		            $label = (string)$option->label;
		            $this->options[$value] = $label;
		        }
        }
    }
	
    /**
     * Gets the definition of the custom field with the given API name
     * 
     * @param string $id The name of the field
     * @return SalsaSupporterCustom The field definition
     */
    public static function get($id) {
        $conn = SalsaConnector::instance();
        if ($conn) {
            $objs = $conn->getObjects('custom_column', "name=$id", null, 'SalsaSupporterCustom');
            if (count($objs) > 0) {
            	  return $objs[0];
            }
        }
        return NULL;
	  }
	  
	  /**
	   * Gets the SalsaSupporterField to render this custom field.
	   * 
	   * @param boolean $required True if the field is required, false otherwise.
	   * @return SalsaSupporterField The field to render.
	   */
	  public function getField($required) {
	  	  switch($this->type) {
	  	  case 'varchar':
	  	  	  if (count($this->options) > 0) {
	  	  	  	  return new SalsaSupporterFieldSelect($this->name, $required, $this->Display_Name, $this->options);
	  	  	  }
	  	  	  break;
 	  	  	  
	  	  case 'bool':
	  	  	  return new SalsaSupporterFieldCheckBox($this->name, $this->Display_Name);
	  	  }
	  	  
 	  	  return new SalsaSupporterField($this->name, $required, $this->Display_Name);
	  }
}

/**
 * Defines a supporter field in the Salsa framework.
 *  
 * @author Andrew Marcus
 * @since May 27, 2010
 */
class SalsaSupporterField {
    public $id;
    public $name;
    public $type;
    public $required;
    public $classes = '';
    
    /**
     * Gets a new SalsaSupporterField instance for the field with the given type.
     * 
     * @param string $id The Salsa identifier of the field, such as "First_Name"
     * @param boolean $required True if the field is required, false otherwise.
     * @return SalsaSupporterField A new field instance.
     */
    public static function get($id, $required = false) {
        switch ($id) {
        case 'Email':
            return new SalsaSupporterFieldEmail($id, $required);
        case 'State':
            return new SalsaSupporterFieldState($id, $required);
        case 'Zip':
            return new SalsaSupporterFieldZip($id);
            
        default:
        	  // If this is a custom field, get its properties
        	  $custom = SalsaSupporterCustom::get($id);
        	  if (!empty($custom)) {
        	  	  return $custom->getField($required);
        	  }
            return new SalsaSupporterField($id, $required);
        }
    }
    
    public function __construct($id, $required, $name = NULL) {
        $this->id = trim($id);
        $this->name = !empty($name) ? $name : str_replace('_', ' ', $this->id);
        $this->type = 'textfield';
        $this->required = $required;
    }
    
    /**
     * Validates the given user value.
     * 
     * @param mixed $value The value specified by the user for this field. 
     * @return array Null if the value is valid, or an array of errors otherwise.
     */
    public function validate($value) {
        $value = trim($value);
        if ($this->required && empty($value)) {
            return array($this->id => "$this->name is required");
        }
        return NULL;
    }
    
    /**
     * Renders the form element in a block with a label.
     * 
     * @param mixed $value The current value of the field.
     * @param array $attributes An array of key->value pairs to add as
     *   attributes to the tag.
     * @param string $label to substitute at the original label. If empty does not display the label tag.
     * @return string HTML representing the form.
     *
     * If attribute key is uqual "class" then the value is added to the class tag of the element.
     */
    public function render($value = '', $attributes = array(), $label = null) {
        $out  = $this->renderLabel($label);
        $out .= $this->renderField($value);
        
        return $this->renderWrapper($out, $attributes);
    }
    
    /**
     * Wraps the given content in a div with the given attributes.
     * 
     * @param string $content The HTML content to wrap. 
     * @param array $attributes An array of key->value pairs to add as
     *   attributes to the tag.
     * @return string HTML wrapping the given content.
     */
    public function renderWrapper($content, $attributes = array()) {
        $class = 'formrow ' . $this->classes;
        $attrs = '';
        if (!empty($attributes)) {
            foreach ($attributes as $k => $v) {
                if ($k === 'class') {
                    $class .= ' '.$v.' ';
                }
                else {
                    $attrs .= " $k=\"$v\"";
                }
            }
        }
        $out = "<div class=\"$class\" $attrs>\n";
        $out .= $content;
        $out .= "</div>\n";
        return $out;
    }

    /**
     * Renders the label for the field.
     * 
     * @param mixed $label If null, the default label will be rendered
     *   if necessary.  If true, the default label will ALWAYS be rendered.
     *   If a string, the given label text will be rendered instead of the 
     *   default label.
     * @return string HTML representing the label
     */
    public function renderLabel($label) {
        if ($label === null || $label === true) {
            return "<label for=\"$this->id\">$this->name</label>\n";
        } else if (!empty($label)) {
            return "<label for=\"$this->id\">$label</label>\n";
        }
        return '';
    }
    
    /**
     * Renders the individual field, without the wrapper.
     * 
     * @param mixed $value The current value of the field. 
     * @return string HTML representing the form element.
     */
    public function renderField($value) {
        return "<input type=\"text\" name=\"$this->id\" value=\"$value\" title=\"$this->name\" />";
    }
}

class SalsaSupporterFieldEmail extends SalsaSupporterField {
    public function __construct($id, $required) {
        parent::__construct($id, $required);
    }
    
    /**
     * Checks that the given value is a valid email address.
     */
    public function validate($value) {
        $errors = parent::validate($value);
        if (!empty($errors)) {
            return $errors;
        }
        
        // See if the email address is valid
        $valid = true;

        // First, we check that there's one @ symbol, and that the lengths are right
        if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $value)) {
            // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
            $valid = false;
        }
        else {
            // Split it into sections to make life easier
            $email_array = explode("@", $value);
            $local_array = explode(".", $email_array[0]);
            for ($i = 0; $i < sizeof($local_array); $i++) {
                if (!ereg("^(([A-Za-z0-9!#$%&#038;'*+/=?^_`{|}~-][A-Za-z0-9!#$%&#038;'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
                    $valid = false;
                    break;
                }
            }
            if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
                $domain_array = explode(".", $email_array[1]);
                if (sizeof($domain_array) < 2) { // Not enough parts to domain
                    $valid = false;
                }
                for ($i = 0; $i < sizeof($domain_array); $i++) {
                    if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
                        $valid = false;
                    }
                }
            }
        }
        if (!$valid) {
            return array($this->id => "Email address is invalid");
        }
        return NULL;
    }
}

class SalsaSupporterFieldSelect extends SalsaSupporterField {
	  protected $options = array();
	  
    public function __construct($id, $required, $name = NULL, $options) {
        parent::__construct($id, $required, $name);
        
        $this->options = $options;
    }

    public function renderField($value = '') {
    	  $out = "<select name=\"$this->id\" title=\"$this->name\" >\n";
    	  $options = $this->getOptions();
    	  
        foreach ($options as $k => $v) {
            $selected = '';
            if ($k == $value) {
                $selected = 'selected="selected"';
            }
            $out .= "  <option value=\"$k\" $selected>$v</option>\n";
        }
        $out .= "</select>";
        return $out;
    }
    
    public function addOption($key, $value) {
    	  $this->options[$key] = $value;
    }
    
    public function getOptions() {
    	  // Make sure there is an empty option
    	  if (empty($this->options[''])) {
    	  	  $this->options[''] = '- Select -';
    	  }
      	return $this->options;
    }
}

class SalsaSupporterFieldState extends SalsaSupporterFieldSelect {
    public function __construct($id, $required, $name = NULL) {
        parent::__construct($id, $required, $name, array(
          "" => '- Select -',
          "AL" => "Alabama",
          "AK" => "Alaska",
          "AS" => "American Samoa",
          "AZ" => "Arizona",
          "AR" => "Arkansas",
          "CA" => "California",
          "CO" => "Colorado",
          "CT" => "Connecticut",
          "DE" => "Delaware",
          "DC" => "D.C.",
          "FL" => "Florida",
          "GA" => "Georgia",
          "GU" => "Guam",
          "HI" => "Hawaii",
          "ID" => "Idaho",
          "IL" => "Illinois",
          "IN" => "Indiana",
          "IA" => "Iowa",
          "KS" => "Kansas",
          "KY" => "Kentucky",
          "LA" => "Louisiana",
          "ME" => "Maine",
          "MD" => "Maryland",
          "MA" => "Massachusetts",
          "MI" => "Michigan",
          "MN" => "Minnesota",
          "MS" => "Mississippi",
          "MO" => "Missouri",
          "MT" => "Montana",
          "NE" => "Nebraska",
          "NV" => "Nevada",
          "NH" => "New Hampshire",
          "NJ" => "New Jersey",
          "NM" => "New Mexico",
          "NY" => "New York",
          "NC" => "North Carolina",
          "ND" => "North Dakota",
          "MP" => "Northern Mariana Islands",
          "OH" => "Ohio",
          "OK" => "Oklahoma",
          "OR" => "Oregon",
          "PA" => "Pennsylvania",
          "PR" => "Puerto Rico",
          "RI" => "Rhode Island",
          "SC" => "South Carolina",
          "SD" => "South Dakota",
          "TN" => "Tennessee",
          "TX" => "Texas",
          "UT" => "Utah",
          "VT" => "Vermont",
          "VI" => "Virgin Islands",
          "VA" => "Virginia",
          "WA" => "Washington",
          "WV" => "West Virginia",
          "WI" => "Wisconsin",
          "WY" => "Wyoming",
          "AA" => "Armed Forces (the) Americas",
          "AE" => "Armed Forces Europe",
          "AP" => "Armed Forces Pacific",
          "AB" => "Alberta",
          "BC" => "British Columbia",
          "MB" => "Manitoba",
          "NF" => "Newfoundland",
          "NB" => "New Brunswick",
          "NS" => "Nova Scotia",
          "NT" => "Northwest Territories",
          "NU" => "Nunavut",
          "ON" => "Ontario",
          "PE" => "Prince Edward Island",
          "QC" => "Quebec",
          "SK" => "Saskatchewan",
          "YT" => "Yukon Territory",
          "ot" => "Other"
        ));
    }
}

class SalsaSupporterFieldZip extends SalsaSupporterField {
    /**
     * The zip code field is ALWAYS required.
     */
    public function __construct($id) {
        parent::__construct($id, TRUE);
    }
    
    /**
     * Checks that the given value is a valid zip code
     */
    public function validate($value) {
        $errors = parent::validate($value);
        if (!empty($errors)) {
            return $errors;
        }
        // Make sure the zip code is valid
        if (!preg_match('/^(\d{5})(-\d{4})?$/', $value)) {
            return array($this->id => 'Zip code is not valid');
        }
        return NULL;
    }
}

class SalsaSupporterFieldLabel extends SalsaSupporterField {
    public $value;
    
    public function __construct($id, $name, $value) {
        $this->id = $id;
        $this->name = $name;
        $this->type = 'label';
        $this->required = false;
        $this->classes = 'nsjalapeno--field-label';
        $this->value = $value;
    }
    
    /**
     * Does nothing.
     */
    public function validate($value) {
        return NULL;
    }
    
    /**
     * Renders the label for the field.  Unless the parameter is explicitly set
     * to true, no label will be rendered.
     * 
     * @param mixed $label If true, the default label will be rendered.
     *   If a string, the given label text will be rendered instead of the 
     *   default label.  Otherwise, no label will be rendered.
     * @return string HTML representing the label
     */
    public function renderLabel($label) {
        if ($label === true) {
            return "<label for=\"$this->id\">$this->name</label>\n";
        } else if (!empty($label)) {
            return "<label for=\"$this->id\">$label</label>\n";
        }
        return '';
    }
    
    /**
     * Render the individual field, without the wrapper.
     * 
     * @return string HTML representing the form element.
     */
    public function renderField() {
        // Replace line breaks with <br> tags
        $this->value = str_replace("\n", "<br/>\n", $this->value);
        return "<div class=\"content\">$this->value</div>";
    }
}

class SalsaSupporterFieldTextArea extends SalsaSupporterField {
    public function __construct($id, $required, $name = NULL) {
    	parent::__construct($id, $required, $name);
        $this->type = 'textarea';
    }
    
    /**
     * Renders the individual field, without the wrapper.
     * 
     * @param mixed $value The current value of the field. 
     * @return string HTML representing the form element.
     */
    public function renderField($value = 0) {
        return "<textarea name=\"$this->id\" title=\"$this->name\">$value</textarea>";
    }
}

class SalsaSupporterFieldCheckBox extends SalsaSupporterField {
    public function __construct($id, $name = NULL) {
        $this->id = trim($id);
        $this->name = !empty($name) ? $name : str_replace('_', ' ', $this->id);
        $this->type = 'checkbox';
        $this->required = false;
    }
    
    /**
     * Renders the form element in a block with a label.
     * 
     * @param mixed $value The current value of the field.
     * @param array $attributes An array of key->value pairs to add as
     *   attributes to the tag.
     * @param string $label to substitute at the original label. If empty does not display the label tag.
     * @return string HTML representing the form.
     *
     * If attribute key is uqual "class" then the value is added to the class tag of the element.
     */
    public function render($value = '', $attributes = array(), $label = null) {
        $out  = $this->renderField($value) . '&nbsp;';
        $out .= $this->renderLabel($label);
        
        return $this->renderWrapper($out, $attributes);
    }
    
    /**
     * Renders the label for the field.
     * 
     * @param mixed $label If null, the default label will be rendered
     *   if necessary.  If true, the default label will ALWAYS be rendered.
     *   If a string, the given label text will be rendered instead of the 
     *   default label.
     * @return string HTML representing the label
     */
    public function renderLabel($label) {
        if ($label === null || $label === true) {
            return $this->name;
        } 
        else if (!empty($label)) {
            return $label;
        }
        return '';
    }
    
    /**
     * Renders the individual field, without the wrapper.
     * 
     * @param mixed $value The current value of the field. 
     * @return string HTML representing the form element.
     */
    public function renderField($value = 0) {
        $checked = (!empty($value) ? 'checked="checked"' : '');
        return "<input type=\"checkbox\" name=\"$this->id\" value=\"1\" title=\"$this->name\" $checked />";
    }
}

class SalsaSupporterFieldGroupBoxes extends SalsaSupporterField {
    public $groups;
    
    public function __construct($groups) {
        $this->id = 'groups';
        $this->name = 'Add me to the following list(s)';
        $this->type = 'label';
        $this->required = false;
        $this->classes = 'nsjalapeno--field-group-boxes';
        $this->groups = $groups;
    }
    
    /**
     * Does nothing.
     */
    public function validate($value) {
        return NULL;
    }
    
    /**
     * Renders the form element in a block with a label.
     * 
     * @param mixed $value The current value of the field.
     * @param array $attributes An array of key->value pairs to add as
     *   attributes to the tag.
     * @param string $label to substitute at the original label. If empty does not display the label tag.
     * @return string HTML representing the form.
     *
     * If attribute key is uqual "class" then the value is added to the class tag of the element.
     */
    public function render($value = '', $attributes = array(), $label = null) {
        $out  = $this->renderLabel($label);
        $out .= "<ul>\n";
        foreach ($this->groups as $group_KEY => $group) {
            $out .= $this->renderField($group_KEY, $group);
        }
        $out .= "</ul>\n";
        
        return $this->renderWrapper($out, $attributes); 
    }
    
    /**
     * Render the individual field, without the wrapper.
     * 
     * @param mixed $value The current value of the field. 
     * @return string HTML representing the form element.
     */
    public function renderField($group_KEY, $group) {
        $out = "<li>\n";
        $out .= "<input type='hidden' name='group_KEY{$group}' value='true' />\n";
        $out .= "<input type='checkbox' name='group_KEY{$group}_checkbox' value='1' />";
        $out .= "<strong>$group</strong>\n";
        $out .= "</li>\n";
        return $out;
    }
}
