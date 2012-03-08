<?php

define( 'DIA_FORM_NAME', '_dia_forms_');

class DIAForm {
    private $forms = array();
    
    public function __construct() {
        $this->loadForms();
    }

    /**
     * Returns the number of configured forms.
     * 
     * @return integer The number of forms.
     */
    public function getNumForms() {
        return count($this->forms);
    }

    /**
     * Returns true if the form with the given key contains the given field.
     * 
     * @param string $key The key of the form.
     * @param string $field The identifier of the form field. 
     * @return boolean True if the form has the field, false otherwise.
     */
    public function isFieldInTheForm($key, $field) {
        $form = $this->getFields($key);
        
        foreach($form as $value) {
            if ($field->id == $value['id']) {
                return true;
            }
        }
        return false;
    }

    /**
     * Loads the form definitions from the Wordpress options.
     */
    public function loadForms() {
        $this->forms = array();        
        $values = get_option(NSJALAPENO_FORM_NAME);
        if (!empty($values)) {
            $this->forms = $values;
        }
    }
    
    /**
     * Saves the form definitions to the Wordpress options.
     */
    public function saveForms() {
        update_option(NSJALAPENO_FORM_NAME, $this->forms);
    }

    /**
     * Adds a new form configuration to the Wordpress options.
     * 
     * @param string $key The key of the form.
     * @param string $name The display name of the form.
     * @param array $form The definitions of the form items.
     */
    public function addForm($key, $name, $items) {
        if (empty($this->forms[$key])) {
            $this->forms[$key] = array(
              'name' => $name, 
              'key' => $key,
              'formitems' => $items
            );
            // Include all required fields by default
            $tokens = '';
            foreach ($items as $id => $item) {
                if ($item->required) {
                    $tokens .= "{{field:$id}}";
                }
            }
            $this->setFormTokens($key, $tokens);
        }
        else {
            $this->forms[$key]['name'] = $name;
            $this->forms[$key]['formitems'] = $items;
        }
        $this->saveForms();
        $this->loadForms();
    }
    
    /**
     * Updates the form items in a form.
     * 
     * @param string $key The key of the form.
     * @param array $form The definitions of the new form items.
     * @param string $name Optionally, the new name of the form.
     * 
     * @return array An array containing the definitions of the form items
     *   which were added and removed compared to the previous form items.
     */
    public function refreshForm($key, $name, $form) {
        if (empty($this->forms[$key])) {
            $this->addForm($key, $name, $form);
        }
        $old = $this->forms[$key]['formitems'];
        
        $old_keys = array();
        $new_keys = array();
        $added = array();
        $removed = array();
        
        foreach ($old as $field) {
            $old_keys[$field->id] = $field;
        }
        foreach ($form as $field) {
            $new_keys[$field->id] = $field;
            
            if (empty($old_keys[$field->id])) {
                $added[$field->id] = $field;
            }
        }
        foreach ($old as $field) {
            if (empty($new_keys[$field->id])) {
                $removed[$field->id] = $field;
            }
        }
        
        // Update the forms
        $this->forms[$key]['formitems'] = $new_keys;
        $this->forms[$key]['name'] = $name;
        
        $this->saveForms();
        $this->loadForms();
        
        return array('added' => $added, 'removed' => $removed);
    }

    /**
     * Returns the form with given display name.
     * 
     * @param string $name The display name of the form.
     * @return array The form definition, or NULL if none could be found.
     */
    public function getFormByName($name) {
        foreach($this->forms as $key => $form) {
            if ($form['name']==$name)
                return $form;
        }
        return NULL;
    }
    
    /**
     * Returns the form with the given key.
     * 
     * @param string $key The key of the form.
     * @return array The form definition, or NULL if none could be found.
     */
    public function getForm($key) {
        return $this->forms[$key];
    }

    /**
     * Returns the definitions of all the configured forms.
     * @return array All the form definitions.
     */
    public function getForms() {
        return $this->forms;
    }

    /**
     * Updates the rendered version of the given form.
     * 
     * @param string $key The key of the form.
     * @param string $value A set of tokens representing the order of the 
     *   form fields.
     */
    public function setFormTokens($key, $tokens) {
        if (!empty($this->forms[$key])) {
            $this->forms[$key]['tokens'] = $tokens;
            $this->forms[$key]['fields'] = $this->parseTokens($tokens);
        }
    }

    /**
     * Displays the form with the given key.
     * 
     * @param string $key The key of the form.
     */
    public function displayForm($key, $parent) {
        $out = "";
        if (empty($this->forms[$key])) {
            return "There is no form with key $key.";
        }
        $form = $this->forms[$key];
        
        include NSJALAPENO_BOXES_TEMPLATES_DIR.'formconfig.php';
    }

    /**
     * Splits the token string representing the order of the fields 
     * into an array containing the fields identifiers.
     * 
     * @param string $form The tokens
     * @return array An array of form fields in the order specified by the tokens.
     *   Each form field will have
     *     - type: 'field'
     *     - id: The id of the field in Salsa, such as 'First_Name'
     */
    private function parseTokens($form) {
        $result = array();
        while (!empty($form)) {
            $start = strpos($form, '{{');
            if ($start !== false) {
                $end = strpos(substr($form, $start), '}}');
                if ($end !== false) {
                    $key = substr($form, $start+2, $end-2);
                    $splits = split(':', $key);
                    $result[] = array(
                      'type' => $splits[0],
                      'id' => $splits[1]
                    );
                    $form = substr($form, $end+2);
                }
            }
        }
        return $result;
    }
    
    /**
     * Gets the definitions of the fields in the configured order.
     * 
     * @param string $key The key of the form.
     * @return array An array of form fields in the order specified by the tokens.
     *   Each form field will have
     *     - type: 'field'
     *     - id: The id of the field in Salsa, such as 'First_Name'
     */
    public function getFields($key) {
        if (!empty($this->forms[$key])) {
            
            // Parse the tokens if we haven't done it yet
            if (empty($this->forms[$key]['fields'])) {
                $fields = $this->parseTokens($this->forms[$key]['tokens']);
                $this->forms[$key]['fields'] = $fields;
            }
            return $this->forms[$key]['fields'];
        }
        return NULL;
    }

    /**
     * Renders the given form.
     * 
     * @param string $key The key of the from to render.
     */
    public function render($key) {
        if (!isset($this->forms[$key])) {
            print "There is no form with key $key.";
        }
        $form = $this->getForm($key);
        $fields = $this->getFields($key);
        
        if (is_array($fields)) {
            include NSJALAPENO_BOXES_TEMPLATES_DIR.'formtemplate.php';
        }
        else {
            print "The form with key $key is not configured properly.";
        }
    }
}
?>
