<?php
define( 'NSJALAPENO_ACTION_SHORT_TAG', '[jalapeno-action:');
define( 'NSJALAPENO_BOXES_TEMPLATES_DIR', dirname(__FILE__).'/boxes/' );
define( 'NSJALAPENO_CRYPT_KEY', "\x38\x37\x31\x35");
define( 'NSJALAPENO_FORM_NAME', 'jalapeno-form');

include "salsa/salsa-action.php";
include "classes/diaform.php";
include "classes/diamessages.php";
include "jalapeno.widgets.php";

class NSJalapenoPlugin {
    private $httpRoot;
    private $ajaxUrl;
    private $formObject;
    private $messages = array();
    private $postedFields = array();
    private $adminStatusMessages = array();
    private $adminErrorMessages = array();

    public function  __construct() {
        $this->ajaxUrl = admin_url('admin-ajax.php');
        $this->httpRoot = plugins_url('', __FILE__) . '/';
        
        add_action('admin_init', array($this, 'adminInit'));
        add_action('template_redirect', array($this, 'mainHeader'));
        add_action('admin_menu', array($this, 'mainMenu'));
        add_filter('the_content', array($this, 'checkForm'));
        add_action('admin_init', array($this, 'addFormBox'));
        add_action('init', array($this, 'init'));
        add_action('wp_ajax_loadForm', array($this,'ajaxLoadForm'));
        add_action('wp_ajax_addFormKey', array($this,'ajaxAddFormKey'));
        add_action('wp_ajax_testConnection', array($this,'ajaxTestConnection'));
        add_action('wp_ajax_saveForm', array($this,'ajaxSaveForm'));
        add_action('wp_ajax_refreshForm', array($this, 'ajaxRefreshForm'));
        add_action('widgets_init', array($this, 'initWidgets'));
        add_action('media_buttons', array($this, 'mediaButtons'), 20);
        add_action('wp_ajax_nsjalapeno_mediaButtonIframe', array($this, 'mediaButtonIframe'));
        $this->formObject = new DIAForm();
        $this->messages = array();
    }

    public function adminInit() {
        $this->registerSettings();
        wp_register_style('nsjalapeno-style',  $this->httpRoot . 'jalapeno.css');
        wp_register_style('nsjalapeno-formeditor', $this->httpRoot . 'jalapeno.formeditor.css');
    }
    
    public function init() {
        if (get_option('dia_enable_css') == 1) {
            wp_enqueue_style('nsjalapeno-frontend', $this->httpRoot . 'jalapeno-frontend.css');
        }
    }
    
    public function registerSettings() {
        register_setting( 'dia-group', 'dia_server' );
        register_setting( 'dia-group', 'dia_username' );
        register_setting( 'dia-group', 'dia_password', array($this, crypta) );
        register_setting( 'dia-group', 'dia_enable_css');        
    }
    
    public function initWidgets() {
    	register_widget('NSJalapenoActionWidget');
    	register_widget('NSJalapenoActionSignersWidget');
    }

    /**
     * Decrypts the Salsa authentication password.
     * 
     * @param $string
     * @return unknown_type
     */
    public function crypta($string) {
        $index = 0;
        $crypt = NSJALAPENO_CRYPT_KEY;
        $result = "";
        for ($i = 0; $i < strlen($string); $i++) {
            $result .= substr($string,$i,1) ^ substr($crypt,$index,1);
            $index = ($index+1) % strlen(NSJALAPENO_CRYPT_KEY);
        }
        return $result;
    }

    /**
     * Connects to the Salsa service with the configured auth data.
     * 
     * @return SalsaConnector The salsa connector object.
     */
    public function initSalsa() {
        $dia = SalsaConnector::instance();
        if (!$dia) {
            $username = get_option('dia_username');
            $password = $this->crypta(get_option('dia_password'));
            $server = get_option('dia_server');
            $dia = SalsaConnector::initialize($server, $username, $password);
        }
        return $dia;
    }
    
    public function getAjaxUrl() {
        return $this->ajaxUrl;
    }

    /**
     * Every time a page is loaded we check to see if it is a post of our form...
     */
    public function mainHeader() {
        $forms = $this->getForms();
        if (is_array($forms)) {
            foreach($forms as $form) {
                if (isset($_POST['diapluginformsubmit_'.$form['key']])) {
                    $this->executeDiaFormSubmit($form, $_POST);
                }
            }
        }
    }

    /**
     * Processes the submitted DIA form, validates the submitted values
     * and posts them to the Salsa framework.
     * 
     * The error and success messages will be recorded in $this->messages.
     * 
     * @param DiaForm $form The form object. 
     * @param array $postForm The submitted form values 
     */
    public function executeDiaFormSubmit($form, $postForm) {
        $key = $form['key'];
        
        // Save the posted fields for future reference
        $this->postedFields[$key] = $postForm;
        $this->messages[$key] = new DIAMessages();
        
        // Validate the supporter form
        foreach($form['formitems'] as $id => $formItem) {
            if ($this->formObject->isFieldInTheForm($key, $formItem)) {
                $error = $formItem->validate($postForm[$id]);
                if (!empty($error)) {
                    $this->messages[$key]->addValidation($error);
                }
            }
        }
        
        // If the form is valid, submit it to the Salsa framework
        if (!$this->messages[$key]->hasErrors()) {
            
            // Connect to Salsa
            $dia = $this->initSalsa();
            
            $errors = $dia->getErrors();
            if (!empty($errors)) {
                $this->messages[$key]->addErrors($errors);
            }
            
            if (!$this->messages[$key]->hasErrors()) {
                $obj = SalsaAction::get($form['key']);
                if (empty($obj->key)) {
                    $this->messages[$key]->addError('Sorry, this action is not valid');
                }
                else {
                    $results = $obj->submit($postForm);
                    
                    // Handle the results of the Salsa submission
                    $this->messages[$key]->addErrors($dia->getErrors());
                    if (!empty($results)) {
                        $this->messages[$key]->setContent($results);
                        $this->messages[$key]->setSuccess('Thank you, your form was submitted!');
                    }
                }
            }
        }
    }
    
    public function getSigners($key, $max = 10) {
        // Connect to Salsa
        $dia = $this->initSalsa();
        
	    $errors = $dia->getErrors();
        if (!empty($errors)) {
            return array();
        }
        else {
        	return SalsaAction::getSigners($key, $max);
        }
    }
    
    public function renderSigners($key, $max = 10) {
    	$signers = $this->getSigners($key, $max);
    	
    	include NSJALAPENO_BOXES_TEMPLATES_DIR.'signers.php';
    }

    /**
     * Returns the messages that accumulated for the given form.
     * 
     * @param string $key The key of the form. 
     * @return DIAMessages Error, validation or success messages.
     */
    public function getMessages($key) {
        return $this->messages[$key];
    }

    /**
     * Returns the fields that were posted for the given form.
     * 
     * @param string $key The key of the form.
     * @return array The posted values.
     */
    public function getPostedFields($key) {
        return $this->postedFields[$key];
    }

    /**
     * Displays the given form and returns the result.
     * 
     * @param string $diaId The key of the form.
     * @return string The HTML for rendering the form.
     */
    public function displayForm($key) {
        ob_start();
        $this->formObject->render($key);
        $result = ob_get_contents();
        ob_clean();
        return $result;
    }

    /**
     * Replaces [diaform:key] tags in content with the rendered form.
     *   
     * @param string $content The content to display. 
     * @return string The same content, with any [diaform] custom tags replaced
     *   with the actual form.
     */
    public function checkForm($content) {
        $startPosition = strpos($content, NSJALAPENO_ACTION_SHORT_TAG);
        if ($startPosition === false) return $content;

        $endPosition = strpos($content, ']', $startPosition);
        if ($endPosition === false) return $content;

        $startDataPosition = $startPosition+strlen(NSJALAPENO_ACTION_SHORT_TAG);
        $diaId = substr($content, $startDataPosition, $endPosition-$startDataPosition);

        // Render the content up to this point
        $result = substr($content,0,$startPosition);
        
        // Render the form
        $result .= $this->displayForm($diaId);
        
        // Look for additional tags in the rest of the content
        $result .= $this->checkForm(substr($content, $endPosition+1));

        return $result;
    }

    public function loadOptions() {
        ob_start();
        include NSJALAPENO_BOXES_TEMPLATES_DIR.'formoptions.php';
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }

    public function ajaxLoadForm() {
        echo $this->formObject->displayForm($_POST['formid'], $this);
        exit();
    }

    public function ajaxTestConnection() {
        $msg="";
        $dia = SalsaConnector::initialize($_POST['server'],$_POST['username'],$_POST['password']);
        $errors = $dia->getErrors();
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $msg .= $error."\n";
            }
            echo $msg;
        }
        else {
            echo "Connection Successful!";
        }
        exit();
    }

    /**
     * Adds a new form configuration.  If a configuration already exists with 
     * the same key, it will be replaced.
     * 
     * @param string $key The key of the form.
     * @param string $name The display name of the form.
     * @param SalsaAction $object The Salsa Action to configured.
     */
    public function addNewKey($key, $name, $object) {
        $this->initSalsa();
        $items = $object->getSupporterFields();
        $this->formObject->addForm($key, $name, $items);
    }

    public function getNumForms() {
        return $this->formObject->getNumForms();
    }

    public function ajaxSaveForm() {
        $this->formObject->setFormTokens($_POST['formid'], $_POST['content']);
        $this->formObject->saveForms();
        echo "Form Saved!";
        exit();
    }
    
    public function ajaxRefreshForm() {
        $key = $_POST['formid'];
        $out = array();

        // Connect to Salsa
        $dia = $this->initSalsa();
        
        $errors = $dia->getErrors();
        if (count($errors) > 0) {
            $out['error'] = $errors;
        }
        else {
            $obj = SalsaAction::get($key);
            if (empty($obj->key)) {
                $out['error'][] =  "No action with this ID...";
            }
            else {
                $items = $obj->getSupporterFields();
                $changes = $this->formObject->refreshForm($key, $obj->Reference_Name, $items);
                
                $added = array();
                $removed = array();
                foreach ($changes['added'] as $field) {
                    $added[] = array(
                      'id' => $field->id,
                      'required' => $field->required,
                      'output' => $field->render()
                    );
                }
                foreach ($changes['removed'] as $field) {
                    $removed[] = array(
                      'id' => $field->id,
                      'required' => $field->required,
                      'output' => $field->render()
                    );
                }
                $out = array(
                  'added' => $added, 
                  'removed' => $removed, 
                  'status' => 'success',
                  'formname' => $obj->Reference_Name,
                  'content' => $this->loadOptions()
                );
            }
        }
        echo $dia->json_encode($out);
        exit();
    }

    public function ajaxAddFormKey() {
        $key = $_POST['actionkey'];
        $out = array();

        // Connect to Salsa
        $dia = $this->initSalsa();
        
        $errors = $dia->getErrors();
        if (count($errors) > 0) {
            $out['error'] = $errors;
        }
        else {
            // Load the Salsa action.
            $obj = SalsaAction::get($key);
            
            if (empty($obj->key)) {
                $out['error'][] =  "No action with this ID...";
            }
            else {
                $this->addNewKey($key, $obj->Reference_Name, $obj);
                $out = array(
                  'formname' => $obj->Reference_Name,
                  'content' => $this->loadOptions()
                );
            }
        }
        echo $dia->json_encode($out);
        exit();
    }

    public function mainMenu() {
        $page2 = add_menu_page('WP Jalape&ntilde;o Action Form Editor', 'WP Jalape&ntilde;o', 'administrator','jalapeno-action-form', array($this, 'formCreator'), $this->httpRoot.'images/internal-icon-16.png');
        add_action('admin_print_scripts-'.$page2, array($this, 'optionsScripts' ));
        add_action('admin_print_styles-'.$page2, array($this, 'optionsStyles' ));
        
        // This is a work around to have a different menu title for the primary page for the menu set instead of duplicate menu titles
        $page3 = add_submenu_page('jalapeno-action-form', 'WP Jalape&ntilde;o Action Form Editor', 'Action Form Editor', 'administrator', 'jalapeno-action-form', array($this, 'noop'));
        
        $page1 = add_submenu_page('jalapeno-action-form', 'WP Jalape&ntilde;o Settings', 'Settings', 'administrator', 'jalapeno-settings', array($this, 'options'));
        add_action('admin_print_scripts-'.$page1, array($this, 'optionsScripts' ));
        add_action('admin_print_styles-'.$page1, array($this, 'optionsStyles' ));
    }
    
    public function noop() {
    
    }

    public function formCreator() {
        // Make sure we can connect to the Salsa framework.
        $dia = $this->initSalsa();
        $errors = $dia->getErrors();
        if (!empty($errors)) {
            $this->adminErrorMessages = $errors;
            $this->options();
        }
        else {
            include NSJALAPENO_BOXES_TEMPLATES_DIR."forms.php";
        }
    }

    public function getForms() {
        $result = $this->formObject->getForms();
        return $result;
    }

    public function getFieldValue($name) {
        $val = get_option($name);
        if ($name == 'dia_password') {
            $val = $this->crypta($val);
        }
        return $val;
    }

    public function optionsStyles() {
        wp_enqueue_style('nsjalapeno-style');
        wp_enqueue_style('nsjalapeno-formeditor');
    }

    public function optionsScripts() {
        wp_enqueue_script('nsjalapeno-script', $this->httpRoot . 'jalapeno.js', array('postbox'));
        wp_enqueue_script('nsjalapeno-formeditor', $this->httpRoot . 'jalapeno.formeditor.js', array('jquery', 'jquery-ui-draggable', 'jquery-ui-sortable'));
    }

    public function options() {
        include NSJALAPENO_BOXES_TEMPLATES_DIR."settings.php";
    }

    public function addFormBox() {
        add_meta_box('dia-form-creator', "Form editor", array($this, 'boxForm'), 'content-type', 'normal', 'high' );
        add_meta_box('dia-form-creator1', "Add a form", array($this, 'boxNewKey'), 'content-type', 'side', 'high' );
    }

    public function boxForm() {
      include NSJALAPENO_BOXES_TEMPLATES_DIR.'formitems.php';
    }

    public function boxNewKey() {
      include NSJALAPENO_BOXES_TEMPLATES_DIR.'formnewkey.php';
    }
    
    
    
    // Status messages
    public function addAdminStatusMessage($message){
      $this->adminStatusMessages[] = $message;
    }
    
    public function addAdminErrorMessage($message){
      $this->adminErrorMessages[] = $message;
    }
    
    
    public function themeAdminMessages(){
      $statuses = $this->adminStatusMessages;
      $errors = $this->adminErrorMessages;
      include NSJALAPENO_BOXES_TEMPLATES_DIR.'adminmessages.php';
    }
    
    
    /**
     * Media Buttons hook callback
     *
     * The callback for the action 'media_buttons' to add a custom button to the top of the
     * post editor to insert a form.
     */
    public function mediaButtons(){
      $title = _('Insert Salsa Form');
      $button = '<a href="'.$this->ajaxUrl.'?action=nsjalapeno_mediaButtonIframe&amp;TB_iframe=true&amp;height=150&amp;respect_dimensions=true" class="thickbox" title="'.$title.'" onclick="return false;"><img src="'.$this->httpRoot.'images/insert-icon.png'.'" alt="'.$title.'" /></a>';
      
      echo $button;
    }
    
    
    /**
     * Editor media button iframe
     *
     * This generates the content to load into the thickbox iframe to insert a form into the editor.
     */
    public function mediaButtonIframe(){
      wp_enqueue_script('jquery');
      wp_iframe(array($this, 'mediaButtonIframeContent'));
      exit();
    }
    
    public function mediaButtonIframeContent(){
      include NSJALAPENO_BOXES_TEMPLATES_DIR . 'insert-into-editor.php';
    }
    

}

$jalapeno = new NSJalapenoPlugin();

function the_jalapeno_action($key) {
    global $jalapeno;
    echo $jalapeno->displayForm($key);
}

function the_jalapeno_action_signatures($key, $max) {
	global $jalapeno;
	echo $jalapeno->renderSigners($key, $max);
}

function the_jalapeno_actions($key = NULL) {
	global $jalapeno;
	$forms = $jalapeno->getForms();
	if (empty($key)) {
		return $forms;
	}
	return $forms[$key];
}

function the_jalapeno_messages($key) {
    global $jalapeno;
    return $jalapeno->getMessages($key);
}

function the_jalapeno_posted_fields($key) {
    global $jalapeno;
    return $jalapeno->getPostedFields($key);
}